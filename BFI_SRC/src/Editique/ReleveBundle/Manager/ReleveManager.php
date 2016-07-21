<?php

namespace Editique\ReleveBundle\Manager;

use Editique\ReleveBundle\Entity\Releve;
use Symfony\Component\HttpFoundation\Response;
use Editique\ReleveBundle\Entity\Operation;

class ReleveManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    const SEUIL_NB_OP_UNE_PAGE = 16;
    const SEUIL_NB_OP_DEUX_PAGES = 40;
    const SEUIL_NB_OP_PLUS_FOOTER = 30;
    const SEUIL_OP_TOTAUX = 61;

    public $error = array();
    public $filePath = '';
    public $fileContent = '';

    public $debutOpe = 0;
    public $finOpe = 0;
    public $currentLine = '';
    public $lines = array();
    public $numLigne = 1;
    public $nbLignesOperationsRestantes = 0;
    public $cursorOperation = 0;
    public $numPage = '001';
    public $nbPage = '%0%';
    public $enchainementTpl = array();
    public $nbOperationsTraitees = 0;
    public $tabStartAndLength = array();

    public $degradee = false;
    public $oReleve = null;
    public $numFeuillet = 0;
    public $typeCompte = 'courant';
    public $reader = null;
    public $dirSortie = '';

    public $tab_releve_global_print = array();
    public $tab_releve_global_mano = array();

    /**
     * Surcharge de EditiqueManager pour gérer les images imagick (nécessaire aux Relevés => OMR)
     */
    public function setPDFManager($builder)
    {
        $builder->setEngineOptions(array(
            'engine' => 'imagick',
            'format' => 'jpg',
            'quality' => 60,
        ));
        $this->pdfManager = $builder->build();
    }

    public function __construct()
    {
        $this->reader = new ReleveReader();
    }

    public function setTypeCompte($typeCompte)
    {
        $this->typeCompte = $typeCompte;
    }

    public function setDegradee($degradee)
    {
        $this->degradee = (bool)$degradee;
    }

    /**
     * On set le contenu du releve et on initialise le lecture manager au besoin
     * @param type $content
     */
    public function setContent($content)
    {
        $this->reader->setContent($content);
        if ($this->reader->getLectureManager() == null) {
            $this->reader->setLectureManager($this->lectureManager);
        }
        $this->reader->setEntityManager($this->entityManager);
    }

    public function getOReleve()
    {
        return $this->oReleve;
    }

    public function initOReleve()
    {
        $this->oReleve = null;
    }

    public function getNumFeuillet()
    {
        return $this->reader->getNumFeuillet();
    }

    public function setDirSortie($dirSortie)
    {
        $this->dirSortie = $dirSortie;
    }

    public function getRelevesFromFile($filePath)
    {
        $fileContent = $this->fileManager->lireFichier($filePath);
        $fileContent = $this->cleanContent($fileContent);
        return explode("\f", $fileContent);
    }
    
    public function cleanContent($content)
    {
        return str_replace("\f ", "", $content);
    }

    /**
     * On initialise un nouveau releve
     */
    public function initReleve()
    {
        $this->numLigne = 1;
        $this->numPage = 1;
        $this->enchainementTpl = array();
        $this->tabStartAndLength = array();
        $this->nbOperationsTraitees = 0;

        $this->oReleve = new Releve();
        $this->oReleve->setTypeCompte($this->typeCompte);
        $this->ecritureManager->content = array();

        // on initialise le surveillant de boucle
        $this->boucleIterator = 0;
        $this->cursorOperation = 0;
    }

    public function lireContent()
    {
        $this->reader->lireReleve($this->oReleve);

        if ($this->oReleve->getLibelleCompte()) {
            $typeCompte =
                $this->entityManager->getRepository('EditiqueMasterBundle:CorrespondanceReleve')->findOneByLibelle(
                    $this->oReleve->getLibelleCompte()
                );

            if ($typeCompte) {
                $this->oReleve->setTypeCompte($typeCompte->getType());
            } else {
                $this->oReleve->setTypeCompte('COPRO');
                $this->logManager->AddAlert(
                    'Génération d\'un relevé avec un type de compte inconnu (mis par défaut à "courant pro")',
                    'Editique > Relevé',
                    'Génération d\'un relevé'
                );
            }
        } else {
            $this->oReleve->setTypeCompte('NOTRA');
        }
    }

    /**
     * Compte le nombre de ligne ecrites que totaliseront les operations
     * @return int
     */
    public function compterNbLignesOperations()
    {
        $res = 0;
        if ($this->oReleve instanceof Releve) {
            foreach ($this->oReleve->getOperations() as $ope) {
                $lib = $ope->getLibelle();
                $tabLib = explode("\n", $lib);
                foreach ($tabLib as $l) {
                    if ($l !== '') {
                        $res ++;
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Retorune le nom du fichier généré en fonction de l'extension
     * @param type $ext
     * @return type
     */
    public function getFileName($ext)
    {
        $idClient = trim($this->oReleve->getIdClient());
        $name = $this->oReleve->getModeDiffusion() == 'F' ? "_REL_CPT_Q_" : "_REL_CPT_";
        return
            "Avis_" .
            $idClient .
            $name .
            $this->oReleve->getNumCompte() .
            "_" .
            $this->dateEcriture .
            "_" .
            $this->oReleve->getNumReleve() .
            "." .
            $ext;
    }

    public function ecrireSortie()
    {
        if ($this->oReleve->getTypeCompte() == 'NOTRA') {
            return false;
        }

        $this->ecrireSortieMaster();

        $sizePDF = 0;

        if (file_exists($this->dirSortie . $this->getFileName('pdf'))) {
            $sizePDF = filesize($this->dirSortie . $this->getFileName('pdf'));
        }

        if ($sizePDF > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function ecrireSortieMaster()
    {
        // on genere la sortie selon le nb d'operation initial
        $this->nbLignesOperationsRestantes = $this->compterNbLignesOperations();
        if ($this->nbLignesOperationsRestantes <= self::SEUIL_NB_OP_UNE_PAGE) {
            if ($this->oReleve->isCompteCourantPro()) {
                $this->getRLV001();
            } elseif ($this->oReleve->isCompteCourantPart()) {
                $this->getRLV013();
            } else {
                $this->getRLV002();
            }
        } elseif ($this->nbLignesOperationsRestantes <= self::SEUIL_NB_OP_DEUX_PAGES) {
            $this->getRLV003();
            $this->numPage = '002';
            if ($this->oReleve->isCompteCourantPro()) {
                $this->getRLV004();
            } elseif ($this->oReleve->isCompteCourantPart()) {
                $this->getRLV014();
            } else {
                $this->getRLV005();
            }
        } else {
            $this->getRLV006();
            $this->genereSuiteReleve();
        }

        $this->finaliserEcriture($this->dirSortie);
    }

    public function genereSuiteReleve()
    {
        $this->numPage ++;
        $this->boucleIterator ++;
        $this->numPage = $this->ecritureManager->addZero($this->numPage, 3);

        // on peut afficher la fin de liste, les totaux et le footer
        if ($this->nbLignesOperationsRestantes <= self::SEUIL_NB_OP_PLUS_FOOTER) {
            if ($this->oReleve->isCompteCourantPro()) {
                $this->getRLV009();
            } elseif ($this->oReleve->isCompteCourantPart()) {
                $this->getRLV015();
            } else {
                $this->getRLV010();
            }
        } elseif ($this->nbLignesOperationsRestantes <= self::SEUIL_OP_TOTAUX) {
            $this->numPage ++;
            $this->numPage = $this->ecritureManager->addZero($this->numPage, 3);
            // on ne peut afficher que la fin de liste et les totaux. Suivi du footer
            $this->getRLV007();
            if ($this->oReleve->isCompteCourantPro()) {
                $this->getRLV004();
            } elseif ($this->oReleve->isCompteCourantPart()) {
                $this->getRLV014();
            } else {
                $this->getRLV005();
            }
        } else {
            // on affiche une liste
            $this->getRLV008();
            if ($this->boucleIterator== 30) {
                $this->logManager->AddError(
                    'Génération d\'un releve de plus de 30 pages... (num cpt : '.$this->oReleve->getNumCompte().')',
                    'Editique > Relevé',
                    'Génération d\'un relevé'
                );
                return;
            }
            $this->genereSuiteReleve(); // on reboucle car il reste des choses a imprimer
        }
    }

    public function finaliserEcriture($dirSortie)
    {
        // on stipule le nb page
        $this->ecritureManager->ecrireNbPage($this->numPage, $this->nbPage);
        $this->oReleve->nbPage = $this->numPage;

        $this->dateEcriture = date('YmdHis');
        $txt = $this->getFileName('txt');
        $pdf = $this->getFileName('pdf');
        $sortieBrute = implode("\r\n", $this->ecritureManager->content);

        // on genere les sorties txt et pdf
        //$this->fileManager->ecrireFichier($dirSortie, $txt, $sortieBrute);
        $this->fileManager->ecrireFichier($dirSortie, $pdf, $this->getPDF());

        // log dans editique
        $idClient = $this->oReleve->getIdClient();
        $numCpt = $this->oReleve->getNumCompte();
        $type = $this->oReleve->getModeDiffusion() == 'F' ? 'releve_quotidien' : 'releve';
        $this->logEditique($idClient, $numCpt, $type, $dirSortie . $pdf);

        // transfert vers le serveur de fichier
        if ($this->oReleve->getModeDiffusion() != 'F') {
            $this->transfertVersServeurFichier($idClient, $dirSortie . $pdf);
        }

        return $pdf;
    }

    public function getRLV001()
    {
        $this->initRLV('001')
            ->ecrireLigneInfosGenerales()
            ->ecrireLigneTitulaireEtAdresse()
            ->ecrireMessagesCommerciaux(2);

        // ligne 12
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxRemunerationGlobal(), $this->numLigne, 3, 20);
        $this->ecritureManager->ecrireLigneSortie($this->getTotalInteretAcquisGlobal(), $this->numLigne++, 24, 38);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalInteretDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxInteret(), $this->numLigne, 28, 5);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTEG(), $this->numLigne++, 34, 7);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getCommissionsDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalCommissions(), $this->numLigne, 28, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFinPrecedente(), $this->numLigne++, 53, 10);

        $this->ecrireLigneAnciensSoldes()
            ->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(16);

        $this->enchainementTpl [] = 'template001';
    }

    public function getRLV002()
    {
        $this->initRLV('002')
            ->ecrireLigneInfosGenerales()
            ->ecrireLigneTitulaireEtAdresse()
            ->ecrireMessagesCommerciaux(2);

        // ligne 12
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxRemunerationGlobal(), $this->numLigne, 3, 20);
        $this->ecritureManager->ecrireLigneSortie($this->getTotalInteretAcquisGlobal(), $this->numLigne++, 24, 38);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFinPrecedente(), $this->numLigne++, 3, 10);

        // Ligne des anciens soldes
        $this->ecrireLigneAnciensSoldes()
            ->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(16);

        $this->enchainementTpl [] = 'template002';
    }

    public function getRLV003()
    {
        $this->initRLV('003')
            ->ecrireLigneInfosGenerales()
            ->ecrireLigneTitulaireEtAdresse(true)
            ->ecrireLigneAnciensSoldes()
            ->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(40);

        $this->enchainementTpl [] = 'template003';
    }

    public function getRLV004()
    {
        // INIT RLV : écrit les 4 premières lignes communes
        $this->initRLV('004');

        // Ligne d'infos
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxRemunerationGlobal(), $this->numLigne, 3, 20);
        $this->ecritureManager->ecrireLigneSortie($this->getTotalInteretAcquisGlobal(), $this->numLigne, 24, 38);
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 63, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne++, 67, 3);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalInteretDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalCommissions(), $this->numLigne++, 28, 24);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getCommissionsDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTEG(), $this->numLigne, 28, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxInteret(), $this->numLigne++, 36, 5);

        // Lignes pour les messages commerciaux
        $this->ecrireMessagesCommerciaux(10);

        $this->enchainementTpl [] = 'template004';
    }

    public function getRLV005()
    {
        // INIT RLV : écrit les 4 premières lignes communes
        $this->initRLV('005');

        // Ligne d'infos
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxRemunerationGlobal(), $this->numLigne, 3, 20);
        $this->ecritureManager->ecrireLigneSortie($this->getTotalInteretAcquisGlobal(), $this->numLigne, 24, 38);
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 63, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne++, 67, 3);

        // Lignes pour les messages commerciaux
        $this->ecrireMessagesCommerciaux(10);

        $this->enchainementTpl [] = 'template005';
    }

    public function getRLV006()
    {
        $this->initRLV('006')
            ->ecrireLigneInfosGenerales()
            ->ecrireLigneTitulaireEtAdresse(true)
            ->ecrireLigneAnciensSoldes()
            ->ecrireLignesOperations(43);

        $this->enchainementTpl [] = 'template006';
    }

    public function getRLV007()
    {
        $this->initRLV('007');

        // Lignes d'informations générales (pages, numéros de compte...)
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne, 7, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFin(), $this->numLigne++, 11, 10);

        // Ligne pour totaux et soldes actuels
        $this->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(61);

        $this->enchainementTpl [] = 'template007';
    }

    public function getRLV008()
    {
        $this->initRLV('008');

        // Lignes d'informations générales (pages, numéros de compte...)
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne++, 7, 3);

        $this->ecrireLignesOperations(64);

        $this->enchainementTpl [] = 'template008';
    }

    public function getRLV009()
    {
        $this->initRLV('009');

        // Lignes d'informations générales (pages, numéros de compte...)
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne, 7, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFin(), $this->numLigne, 11, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxRemunerationGlobal(), $this->numLigne, 22, 20);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxInteret(), $this->numLigne, 43, 5);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTEG(), $this->numLigne++, 47, 7);

        // Ligne interets
        $this->ecritureManager->ecrireLigneSortie($this->getTotalInteretAcquisGlobal(), $this->numLigne, 3, 38);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalInteretDebiteur(), $this->numLigne++, 42, 24);

        // Ligne commissions
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalCommissions(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getCommissionsDebiteur(), $this->numLigne++, 28, 24);

        // Ligne pour totaux et soldes actuels
        $this->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(30)
            ->ecrireMessagesCommerciaux(10);

        $this->enchainementTpl [] = 'template009';
    }

    public function getRLV010()
    {
        $this->initRLV('010');

        // Lignes d'informations générales (pages, numéros de compte...)
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne, 7, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFin(), $this->numLigne, 11, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxRemunerationGlobal(), $this->numLigne, 22, 20);

        // Ligne interets
        $this->ecritureManager->ecrireLigneSortie($this->getTotalInteretAcquisGlobal(), $this->numLigne++, 3, 38);

        $this->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(30)
            ->ecrireMessagesCommerciaux(10);

        $this->enchainementTpl [] = 'template010';
    }

    public function getRLV013()
    {
        $this->initRLV('013')
            ->ecrireLigneInfosGenerales()
            ->ecrireLigneTitulaireEtAdresse()
            ->ecrireMessagesCommerciaux(2);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalInteretDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxInteret(), $this->numLigne, 28, 5);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTEG(), $this->numLigne++, 34, 7);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getCommissionsDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalCommissions(), $this->numLigne, 28, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFinPrecedente(), $this->numLigne++, 53, 10);

        $this->ecrireLigneAnciensSoldes()
            ->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(16);

        $this->enchainementTpl [] = 'template013';
    }

    public function getRLV014()
    {
        // INIT RLV : écrit les 4 premières lignes communes
        $this->initRLV('014');

        // Ligne d'infos
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne++, 7, 3);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalInteretDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalCommissions(), $this->numLigne++, 28, 24);

        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getCommissionsDebiteur(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTEG(), $this->numLigne, 28, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxInteret(), $this->numLigne++, 36, 5);

        // Lignes pour les messages commerciaux
        $this->ecrireMessagesCommerciaux(10);

        $this->enchainementTpl [] = 'template014';
    }

    public function getRLV015()
    {
        $this->initRLV('015');

        // Lignes d'informations générales (pages, numéros de compte...)
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne, 7, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFin(), $this->numLigne, 11, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTxInteret(), $this->numLigne, 22, 5);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTEG(), $this->numLigne, 28, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalInteretDebiteur(), $this->numLigne++, 36, 25);

        // Ligne commissions
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getTotalCommissions(), $this->numLigne, 3, 24);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getCommissionsDebiteur(), $this->numLigne++, 28, 24);

        // Ligne pour totaux et soldes actuels
        $this->ecrireLigneTotauxEtSoldes()
            ->ecrireLignesOperations(30)
            ->ecrireMessagesCommerciaux(10);

        $this->enchainementTpl [] = 'template015';
    }

    /**
     * Ecrit la premiere ligne qui precise la maquette les deux lignes vides
     * suivantes et le type de compte
     * @param int $num numero de la maquette
     * @return object ReleveManger
     */
    public function initRLV($num)
    {
        $ligne1 = '1RLV' . $num . '00' . $this->oReleve->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        if ($num == '001' || $num == '002' || $num == '003' || $num == '006' || $num = '013') {
            $str = $this->ecritureManager->centrerEspace($this->oReleve->getLibelleCompteGlobal(), 69);
            $this->ecritureManager->ecrireLigneSortie($str, $this->numLigne++, 3, 69);
        }

        return $this;
    }

    /**
     * Lignes d'informations générales
     *      num pages,
     *      nb page
     *      date de debut
     *      date de fin
     *      num releve
     *      idClient
     *      num compte
     *      idEsab
     *  @return object ReleveManger
     */
    public function ecrireLigneInfosGenerales()
    {
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne, 7, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateDebut(), $this->numLigne, 11, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFin(), $this->numLigne, 22, 10);
        $numReleve = $this->ecritureManager->addZero($this->oReleve->getNumReleve(), 3);
        $this->ecritureManager->ecrireLigneSortie($numReleve, $this->numLigne, 33, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getIdClient(), $this->numLigne, 37, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getNumCompte(), $this->numLigne, 45, 11);
        $this->ecritureManager->ecrireLigneSortie($this->oReleve->getIdeSab(), $this->numLigne++, 57, 10);

        return $this;
    }

    /**
     * Ecrit titulaire et l'adresse sur 3 lignes     *
     * @param boolean $mettreDateFinPrecedente si true la date de fin est mise à 36
     * @return object ReleveManger
     */
    public function ecrireLigneTitulaireEtAdresse($mettreDateFinPrecedente = false)
    {
        $titulaire1 = $this->ecritureManager->addEspace($this->oReleve->getTitulaire1(), 69);
        $this->ecritureManager->ecrireLigneSortie($titulaire1, $this->numLigne++, 3, 69);

        $titulaire2 = $this->ecritureManager->addEspace($this->oReleve->getTitulaire2(), 69);
        $this->ecritureManager->ecrireLigneSortie($titulaire2, $this->numLigne, 3, 69);

        $adresseLines = explode("\n", $this->oReleve->getAdresse());
        //var_dump($adresseLines);
        for ($i = 0; $i < 5; $i++) {
            $adr = isset($adresseLines[$i]) ? trim($adresseLines[$i]) : '';
            if ($i % 2 == 0) {
                $deb = 3;
                $this->numLigne++;
            } else {
                $deb = 36;
            }
            $this->ecritureManager->ecrireLigneSortie($adr, $this->numLigne, $deb, 32);
        }
        if ($mettreDateFinPrecedente) {
            $this->ecritureManager->ecrireLigneSortie($this->oReleve->getDateFinPrecedente(), $this->numLigne, 36, 10);
        }

        $this->numLigne++;

        return $this;
    }

    /**
     * Ecrit les $nb premiers messages commerciaux (1 par lignes)
     * @param int $nb le nb de messages à ecrire
     * @return object ReleveManger
     */
    public function ecrireMessagesCommerciaux($nb)
    {
        //$tabMsg = $this->oReleve->getMessageCommerciaux();
        $tabMsg = $this->entityManager->getRepository('EditiqueMasterBundle:MessageCommercial')->findActiveBroken($nb);
        $this->oReleve->setMessageCommerciaux($tabMsg);

        foreach ($tabMsg as $partOfMsg) {
            $this->ecritureManager->ecrireLigneSortie($partOfMsg[0], $this->numLigne++, 3, 55);
            $this->ecritureManager->ecrireLigneSortie(
                isset($partOfMsg[1]) ? $partOfMsg[1] : '',
                $this->numLigne++,
                3,
                55
            );
        }

        return $this;
    }

    /**
     * Ecrit la ligne avec les anciens soldes : debiteur puis crediteur
     * @return object ReleveManger
     */
    public function ecrireLigneAnciensSoldes()
    {
        $this->ecritureManager->ecrireMontant($this->oReleve->getAncienSoldeDebiteur(), $this->numLigne, 3);
        $this->ecritureManager->ecrireMontant($this->oReleve->getAncienSoldeCrediteur(), $this->numLigne++, 22);

        return $this;
    }

    /**
     * Ecrit la ligne avec les mouvements : debiteur puis crediteur
     * Ecrit la ligne avec les soldes actuels : debiteur puis crediteur
     * @return object ReleveManger
     */
    public function ecrireLigneTotauxEtSoldes()
    {
        $this->ecritureManager->ecrireMontant($this->oReleve->getTotalMvtDebiteur(), $this->numLigne, 3);
        $this->ecritureManager->ecrireMontant($this->oReleve->getTotalMvtCrediteur(), $this->numLigne++, 22);

        $this->ecritureManager->ecrireMontant($this->oReleve->getSoldeDebiteur(), $this->numLigne, 3);
        $this->ecritureManager->ecrireMontant($this->oReleve->getSoldeCrediteur(), $this->numLigne++, 22);

        return $this;
    }

    /**
     * Ecrit les lignes operations
     * @todo mettre debut et fin pour pagination
     * @return object ReleveManger
     */
    public function ecrireLignesOperations($capaciteMax = 0)
    {
        $tabOpe = $this->oReleve->getOperations();
        $capaciteRestante = $capaciteMax;
        $iterator = 0;

        while ($capaciteRestante > 0 && isset($tabOpe[$this->cursorOperation])) {
            $ope = $tabOpe[$this->cursorOperation];
            $tabLib = explode("\n", $ope->getLibelle());
            array_pop($tabLib);

            // on verifie qu'il nous reste la place d'ecrire l'operation en
            // entier sinon on coupe
            if (count($tabLib) > $capaciteRestante) {
                break;
            }

            $this->ecritureManager->ecrireLigneSortie($ope->getDateOperation(), $this->numLigne, 3, 10);
            $this->ecritureManager->ecrireLigneSortie($tabLib[0], $this->numLigne, 14, 32);
            $this->ecritureManager->ecrireLigneSortie($ope->getDateValeur(), $this->numLigne++, 47, 10);

            $this->ecritureManager->ecrireMontant($ope->getDebit(), $this->numLigne, 3);
            $this->ecritureManager->ecrireMontant($ope->getCredit(), $this->numLigne++, 3);

            $this->nbLignesOperationsRestantes--;
            $capaciteRestante--;

            if (isset($tabLib[1]) && $tabLib[1] != "") {
                $this->ecritureManager->ecrireLigneSortie($tabLib[1], $this->numLigne++, 14, 32);
                $this->nbLignesOperationsRestantes--;
                $capaciteRestante--;
            }
            if (isset($tabLib[2]) && $tabLib[2] != "") {
                $this->ecritureManager->ecrireLigneSortie($tabLib[2], $this->numLigne++, 14, 32);
                $this->nbLignesOperationsRestantes--;
                $capaciteRestante--;
            }

            // on incremente le cursor de lecture des operations
            $iterator++;
            $this->cursorOperation++;
        }

        $this->tabStartAndLength[] = array('start' => $this->nbOperationsTraitees, 'length' => $iterator);
        $this->nbOperationsTraitees = $this->nbOperationsTraitees + $iterator;
        return $this;
    }

    /**
     * Renvoie la concatenation des interets + " euros au " +date de fin
     * @return string
     */
    public function getTotalInteretAcquisGlobal()
    {
        return $this->oReleve->getTotalInteretAcquis() . ' euros au ' . $this->oReleve->getDateFin();
    }

    /***************************************************************************
     *
     *                            Partie PDF
     *
     **************************************************************************/

    public function getListeTplPDF()
    {
        return array(
            'template001' => __DIR__ . '/../Resources/pdf_template/tplReleve001.pdf',
            'template002' => __DIR__ . '/../Resources/pdf_template/tplReleve002.pdf',
            'template003' => __DIR__ . '/../Resources/pdf_template/tplReleve003.pdf',
            'template004' => __DIR__ . '/../Resources/pdf_template/tplReleve004.pdf',
            'template005' => __DIR__ . '/../Resources/pdf_template/tplReleve005.pdf',
            'template006' => __DIR__ . '/../Resources/pdf_template/tplReleve006.pdf',
            'template007' => __DIR__ . '/../Resources/pdf_template/tplReleve007.pdf',
            'template008' => __DIR__ . '/../Resources/pdf_template/tplReleve008.pdf',
            'template009' => __DIR__ . '/../Resources/pdf_template/tplReleve009.pdf',
            'template010' => __DIR__ . '/../Resources/pdf_template/tplReleve010.pdf',
            'template011' => __DIR__ . '/../Resources/pdf_template/tplReleve011.pdf',
            'template012' => __DIR__ . '/../Resources/pdf_template/tplReleve012.pdf',
            'template013' => __DIR__ . '/../Resources/pdf_template/tplReleve013.pdf',
            'template014' => __DIR__ . '/../Resources/pdf_template/tplReleve014.pdf',
            'template015' => __DIR__ . '/../Resources/pdf_template/tplReleve015.pdf',
        );
    }

    public function getPDF()
    {
        $response = new Response();
        $tpl = $this->getListeTplPDF();

        // on stocke les infos pour pdf dans l'objet pour reutilisation sur le pdf global
        $this->oReleve->enchainementTpl = $this->enchainementTpl;
        $this->oReleve->startAndLength = $this->tabStartAndLength;
        $this->oReleve->nbPage = $this->numPage;

        $paramTpl = array(
            'oReleve'         => $this->oReleve,
            'tplArray'        => $tpl
        );
        $this->tplManager->renderResponse('EditiqueReleveBundle:pdf:releve.pdf.twig', $paramTpl, $response);

        return $this->pdfManager->render($response->getContent());
    }

    public function ecrirePDFGlobaux($dirSortie)
    {
        $fileName = "Avis_REL_CPT_Impression_".date("Ymd").'.pdf';
        $this->buildPDFGlobal('print', $dirSortie, $fileName);

        $fileName = "Avis_REL_CPT_Impression_Manuelle_".date("Ymd").'.pdf';
        $this->buildPDFGlobal('mano', $dirSortie, $fileName);

        $fileName = "Avis_REL_CPT_Impression_Decadaire_".date("Ymd").'.pdf';
        $this->buildPDFGlobal('decadaire', $dirSortie, $fileName);
    }

    public function buildPDFGlobal($typeReleve, $dirSortie, $fileName)
    {
        // variable dynamique pour selectionner le bon tableau
        $tab = $this->{'tab_releve_global_'.$typeReleve};

        if (empty($tab)) {
            return;
        }

        $response = new Response();

        $paramTpl = array(
            'tab_releve_global' => $tab,
            'tplArray'          => $this->getListeTplPDF()
        );

        $this->tplManager->renderResponse('EditiqueReleveBundle:pdf:releveGlobal.pdf.twig', $paramTpl, $response);

        $pdf = $this->pdfManager->render($response->getContent());

        $this->fileManager->ecrireFichier($dirSortie, $fileName, $pdf);

        $this->transfertVersServeurFichierDSI($dirSortie . $fileName, 'releve');
        $this->sendMail('ANGERS', $fileName);
            
        if ($typeReleve == 'mano') {
            $this->sendMailPourImpressionManuelle($fileName);
        }
    }

    public function integrerRelevePourImpression($modeDif)
    {
        $idClient = $this->oReleve->getIdClient();

        // on impacte le bon tableau en fonction du nombre de plie
        // pour cela on selectionne le bon attribut via une variable globale
        if ($modeDif == 'I') {
            $tab = 'tab_releve_global_print';
            if ($this->oReleve->nbPage > 8) {
                $tab = 'tab_releve_global_mano';
            }
        } else {
            $tab = 'tab_releve_global_decadaire';
        }

        // on init le sous tableau si client pas encore rencontre
        if (!isset($this->{$tab}[$idClient])) {
            $this->{$tab}[$idClient] = array($this->oReleve);
        } else {
            if ($this->oReleve->getTypeCompte() == 'COPRO' || $this->oReleve->getTypeCompte() == 'COPAR') {
                $tabTmp = array_merge(array($this->oReleve), $this->{$tab}[$idClient]);
                $this->{$tab}[$idClient] = $tabTmp;
            } else {
                $this->{$tab}[$idClient] []= $this->oReleve;
            }
        }
    }


    public function sendMailPourImpressionManuelle($fileName)
    {
        // titre
        $titre= "[BFI/Symfony] Releve de plus de 4 feuilles";

        $tpl = 'BackOfficeMonitoringBundle:Mailing:mail_impression_releve_manuelle.html.twig';
        $content = $this->tplManager->render($tpl, array('fileName' => $fileName));

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

        mail(
            $this->getDestMailErreur('ANGERS'),
            $titre,
            $content,
            $headers
        );
    }
}
