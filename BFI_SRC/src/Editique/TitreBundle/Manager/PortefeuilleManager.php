<?php

namespace Editique\TitreBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

class PortefeuilleManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    const SEUIL_NB_LG_UNE_PAGE = 17;
    const SEUIL_NB_LG_DEUX_PAGES = 44;
    const SEUIL_NB_LG_PLUS_REPART = 40;
    const SEUIL_LG_TOTAUX = 67;
    
    public $numPage = '001';
    public $nbPage = '%0%';
    public $oPortefeuille = null;
    public $tabStartAndLength = array();
    public $nbLignesValeursRestantes = 0;
    public $nbValeursTraitees = 0;
    public $cursorValeur = 0;
    
    public $tab_releve_global_print = array();
    public $tab_releve_global_mano = array();
    
    // Init
    
    public function setDirSortie($dirSortie)
    {
        $this->dirSortie = $dirSortie;
    }
    
    /**
     * On initialise un nouveau releve
     */
    public function initPortefeuille()
    {
        $this->numLigne = 1;
        $this->numPage = 1;
        $this->enchainementTpl = array();
        $this->tabStartAndLength = array();
        $this->nbOperationsTraitees = 0;

        $this->ecritureManager->content = array();

        // on initialise le surveillant de boucle
        $this->boucleIterator = 0;
        $this->cursorOperation = 0;
    }
    
    public function resetRights()
    {
        $query = "grant select on sabpbs.FDEUFCH00_PEXTENT_XTENT to RSABPBS_R";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        
        $query2 = "grant select on sabpbs.FDEUFCH00_PEXTDET_XTDET to RSABPBS_R";
        
        $req2 = $this->entityManager->getConnection()->prepare($query2);
        $req2->execute();
        
        return true;
    }
    
    
    // Lecture
    public function getPortefeuilles()
    {
        $portefeuilles = $this->entityManager
            ->getRepository('Editique\TitreBundle\Entity\Portefeuille')
            ->findAll();
        
        return $portefeuilles;
    }
    
    public function compterNbLignes()
    {
        return count($this->oPortefeuille->getValeurs());
    }
    
    public function getTotalPort()
    {
        $query = "SELECT X00301 FROM FDEUFCH00_PEXTDET_XTDET" .
            " WHERE CODTRT LIKE 'TOTG1%' AND NUPORT = '" . $this->oPortefeuille->getNumPort() . "'" .
            " AND P00004 LIKE '" . $this->oPortefeuille->getNumCompte() . "%'";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        $res = $req->fetch();
        
        return $res['X00301'];
    }
    
    public function getIdEsab()
    {
        $query = "SELECT BAGGCOCAB FROM ZBAGGCO0" .
            " WHERE BAGGCOCLI LIKE '" . $this->oPortefeuille->getIdClient() . "%'";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        $res = $req->fetch();
        
        return $res['BAGGCOCAB'];
    }
    
    public function getCategories()
    {
        $query = "SELECT X00013 AS code, X00300 AS lib, X00302 AS poids, X00301 AS total FROM FDEUFCH00_PEXTDET_XTDET" .
            " WHERE CODTRT LIKE 'TOTG%' AND CODTRT NOT LIKE 'TOTG1%'" .
            " AND NUPORT = '" . $this->oPortefeuille->getNumPort() . "'" .
            " AND P00004 LIKE '" . $this->oPortefeuille->getNumCompte() . "%'";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        $res = $req->fetchAll();
        
        return $res;
    }
    
    public function getValeurs()
    {
        $query = "SELECT X00014 AS categorie, P00032 AS lib, P00037 AS code, P00026 AS quantite," .
            " P00105 AS cours, P00114 AS prixRevient, P00111 AS valorisation FROM FDEUFCH00_PEXTDET_XTDET" .
            " WHERE CODTRT LIKE 'DETAIL%' AND NUPORT = '" . $this->oPortefeuille->getNumPort() . "'" .
            " AND P00004 LIKE '" . $this->oPortefeuille->getNumCompte() . "%'";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        $res = $req->fetchAll();
        
        foreach ($res as &$val) {
            $val['CATEGORIE'] = explode('#&', wordwrap(trim($val['CATEGORIE']), 16, '#&', true));
            $val['LIB'] = explode('#&', wordwrap(trim($val['LIB']), 22, '#&', true));
        }
        
        return $res;
    }
    
    //Enregistrement
    
    public function setData($portefeuille)
    {
        $this->oPortefeuille = $portefeuille;
        $this->oPortefeuille->setTotalPort($this->getTotalPort());
        $this->oPortefeuille->setIdEsab($this->getIdEsab());
        $this->oPortefeuille->setCategories($this->getCategories());
        $this->oPortefeuille->setValeurs($this->getValeurs());
    }
    
    // Ecriture
    
    public function ecrireSortie()
    {
        // on genere la sortie selon le nombre de lignes
        $this->nbLignesValeursRestantes = $this->compterNbLignes();
        if ($this->nbLignesValeursRestantes <= self::SEUIL_NB_LG_UNE_PAGE) {
            $this->getRTI001();
        } elseif ($this->nbLignesValeursRestantes <= self::SEUIL_NB_LG_DEUX_PAGES) {
            $this->getRTI002();
            $this->getRTI007();
        } else {
            $this->getRTI003();
            $this->genereSuite();
        }

        $this->finaliserEcriture($this->dirSortie);
        
        return true;
    }
    
    public function genereSuite()
    {
        $this->numPage ++;
        $this->boucleIterator ++;
        $this->numPage = $this->ecritureManager->addZero($this->numPage, 3);

        // on peut afficher la fin de liste, les totaux et le footer
        if ($this->nbLignesValeursRestantes <= self::SEUIL_NB_LG_PLUS_REPART) {
            $this->getRTI004();
        } elseif ($this->nbLignesValeursRestantes <= self::SEUIL_LG_TOTAUX) {
            $this->numPage ++;
            $this->numPage = $this->ecritureManager->addZero($this->numPage, 3);
            // on ne peut afficher que la fin de liste et les totaux. Suivi du footer
            $this->getRTI005();
            $this->getRTI007();
        } else {
            // on affiche une liste
            $this->getRTI006();
            if ($this->boucleIterator== 30) {
                $this->logManager->AddError(
                    'Génération d\'un portefeuille titres de plus de 30 pages... (num cpt : ' .
                    $this->oPortefeuille->getNumCompte().')',
                    'Editique > Titre',
                    'Génération d\'un relevé titre'
                );
                return;
            }
            $this->genereSuite(); // on reboucle car il reste des choses a imprimer
        }
    }
    
    public function finaliserEcriture($dirSortie)
    {
        // on stipule le nb page
        $this->ecritureManager->ecrireNbPage($this->numPage, $this->nbPage);
        $this->oPortefeuille->nbPage = $this->numPage;

        $this->dateEcriture = date('YmdHis');
        $txt = $this->getFileName('txt');
        $pdf = $this->getFileName('pdf');
        $sortieBrute = implode("\r\n", $this->ecritureManager->content);
        
        // on genere les sorties txt et pdf
        $this->fileManager->ecrireFichier($dirSortie, $txt, $sortieBrute);
        $this->fileManager->ecrireFichier($dirSortie, $pdf, $this->getPDF());

        // log dans editique
        $idClient = $this->oPortefeuille->getIdClient();
        $numCpt = $this->oPortefeuille->getNumCompte();
        $type = 'portefeuille_titres';
        $this->logEditique($idClient, $numCpt, $type, $dirSortie . $pdf);

        // transfert vers le serveur de fichier
        //$this->transfertVersServeurFichier($idClient, $dirSortie . $pdf);

        return $pdf;
    }
    
    public function getFileName($ext)
    {
        $idClient = trim($this->oPortefeuille->getIdClient());
        return
            "Avis_" .
            $idClient .
            "_REL_PORT_TITRE_" .
            $this->oPortefeuille->getNumCompte() .
            "_" .
            $this->dateEcriture .
            "." .
            $ext;
    }
    
    public function getRTI001()
    {
        $this
            ->initRTI('001')
            ->ecrireLignesEnTete()
            ->ecrireLignesPage();
        
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumPort(), $this->numLigne, 14, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getTotalPort(), $this->numLigne++, 22, 30);
        
        $this->ecrireLignesValeurs(17);
        $this->ecrireLignesCategories();

        $this->enchainementTpl [] = 'template001';
    }
    
    public function getRTI002()
    {
        $this
            ->initRTI('002')
            ->ecrireLignesEnTete()
            ->ecrireLignesPage();
        
        
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumPort(), $this->numLigne, 14, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getTotalPort(), $this->numLigne++, 22, 30);
        
        $this->ecrireLignesValeurs(44);

        $this->enchainementTpl [] = 'template002';
    }
    
    public function getRTI003()
    {
        $this
            ->initRTI('003')
            ->ecrireLignesEnTete()
            ->ecrireLignesPage();
        
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumPort(), $this->numLigne, 14, 7);
        
        $this->ecrireLignesValeurs(45);

        $this->enchainementTpl [] = 'template003';
    }
    
    public function getRTI004()
    {
        $this
            ->initRTI('004')
            ->ecrireLignesPage();
        
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumPort(), $this->numLigne, 14, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getTotalPort(), $this->numLigne++, 22, 30);
        
        $this->ecrireLignesValeurs(40);
        $this->ecrireLignesCategories();

        $this->enchainementTpl [] = 'template004';
    }
    
    public function getRTI005()
    {
        $this
            ->initRTI('005')
            ->ecrireLignesPage();
        
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumPort(), $this->numLigne, 14, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getTotalPort(), $this->numLigne++, 22, 30);
        
        $this->ecrireLignesValeurs(67);

        $this->enchainementTpl [] = 'template005';
    }
    
    public function getRTI006()
    {
        $this
            ->initRTI('006')
            ->ecrireLignesPage();
        
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumPort(), $this->numLigne++, 14, 7);
        
        $this->ecrireLignesValeurs(68);

        $this->enchainementTpl [] = 'template006';
    }
    
    public function getRTI007()
    {
        $this
            ->initRTI('007')
            ->ecrireLignesPage()
            ->ecrireLignesCategories();
        
        $this->enchainementTpl [] = 'template007';
    }
    
    private function ecrireLignesEnTete()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getDateValorisation(), $this->numLigne, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getIdEsab(), $this->numLigne, 14, 12);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getIdClient(), $this->numLigne, 27, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getClassMIF(), $this->numLigne++, 35, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oPortefeuille->getNumCompte(), $this->numLigne++, 3, 20);
        $raisonSociale = $this->oPortefeuille->getRaisonSociale();
        $this->ecritureManager->ecrireLigneSortie($raisonSociale[0], $this->numLigne++, 3, 60);
        $this->ecritureManager->ecrireLigneSortie($raisonSociale[1], $this->numLigne++, 3, 17);
        $adresse = $this->oPortefeuille->getAdresse();
        $this->ecritureManager->ecrireLigneSortie($adresse[0], $this->numLigne++, 3, 49);
        $this->ecritureManager->ecrireLigneSortie($adresse[1], $this->numLigne++, 3, 49);
        $this->ecritureManager->ecrireLigneSortie($adresse[2], $this->numLigne, 3, 49);
        
        return $this;
    }
    
    private function ecrireLignesPage()
    {
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 53, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne++, 57, 3);
        
        return $this;
    }
    
    public function ecrireLignesValeurs($capaciteMax = 0)
    {
        $valeurs = $this->oPortefeuille->getValeurs();
        
        $capaciteRestante = $capaciteMax;
        $iterator = 0;

        while ($capaciteRestante > 0 && isset($valeurs[$this->cursorValeur])) {
            $valeur = $valeurs[$this->cursorValeur];
            
            // Ecriture
            $this->ecritureManager->ecrireLigneSortie($valeur['CATEGORIE'][0], $this->numLigne, 3, 32);
            $this->ecritureManager->ecrireLigneSortie($valeur['LIB'][0], $this->numLigne++, 36, 32);
            $this->ecritureManager->ecrireLigneSortie($valeur['CODE'], $this->numLigne, 3, 20);
            $this->ecritureManager->ecrireLigneSortie($valeur['QUANTITE'], $this->numLigne, 24, 20);
            $this->ecritureManager->ecrireLigneSortie($valeur['COURS'], $this->numLigne++, 45, 20);
            $this->ecritureManager->ecrireLigneSortie($valeur['PRIXREVIENT'], $this->numLigne, 3, 20);
            $this->ecritureManager->ecrireLigneSortie($valeur['VALORISATION'], $this->numLigne++, 24, 30);

            $this->nbLignesValeursRestantes--;
            $capaciteRestante--;

            // on incremente le cursor de lecture des valeurs
            $iterator++;
            $this->cursorValeur++;
        }

        $this->tabStartAndLength[] = array('start' => $this->nbValeursTraitees, 'length' => $iterator);
        $this->nbValeursTraitees = $this->nbValeursTraitees + $iterator;
        return $this;
    }
    
    public function ecrireLignesCategories()
    {
        $categories = $this->oPortefeuille->getCategories();
        
        foreach ($categories as $categorie) {
            // Ecriture
            $this->ecritureManager->ecrireLigneSortie($categorie['LIB'], $this->numLigne, 3, 32);
            $this->ecritureManager->ecrireLigneSortie($categorie['POIDS'], $this->numLigne++, 36, 30);
            $this->ecritureManager->ecrireLigneSortie($categorie['TOTAL'], $this->numLigne, 3, 30);
        }
        
        return $this;
    }
    
    /**
     * Ecrit la premiere ligne qui precise la maquette les deux lignes vides
     * suivantes et le type de compte
     * @param int $num numero de la maquette
     * @return object ReleveManger
     */
    public function initRTI($num)
    {
        $ligne1 = '1RTI' . $num . '00' . $this->oPortefeuille->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }
    
    // PDF
    public function getListeTplPDF()
    {
        return array(
            'template001' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT001.pdf',
            'template002' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT002.pdf',
            'template003' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT003.pdf',
            'template004' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT004.pdf',
            'template005' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT005.pdf',
            'template006' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT006.pdf',
            'template007' => __DIR__ . '/../Resources/pdf_template/portefeuille_TIT007.pdf',
        );
    }

    public function getPDF()
    {
        $response = new Response();
        $tpl = $this->getListeTplPDF();

        // on stocke les infos pour pdf dans l'objet pour reutilisation sur le pdf global
        $this->oPortefeuille->enchainementTpl = $this->enchainementTpl;
        $this->oPortefeuille->startAndLength = $this->tabStartAndLength;
        $this->oPortefeuille->nbPage = $this->numPage;

        $paramTpl = array(
            'oPortefeuille' => $this->oPortefeuille,
            'tplArray'      => $tpl
        );
        $this->tplManager->renderResponse(
            'EditiqueTitreBundle:portefeuille_pdf:portefeuille.pdf.twig',
            $paramTpl,
            $response
        );

        return $this->pdfManager->render($response->getContent());
    }
    
    public function integrerPortefeuillePourImpression()
    {
        $idClient = $this->oPortefeuille->getIdClient();

        // on impacte le bon tableau en fonction du nombre de plis
        // pour cela on selectionne le bon attribut via une variable globale
        $tab = 'tab_releve_global_print';
        if ($this->oPortefeuille->nbPage > 8) {
            $tab = 'tab_releve_global_mano';
        }

        // on init le sous tableau si client pas encore rencontre
        if (!isset($this->{$tab}[$idClient])) {
            $this->{$tab}[$idClient] = array($this->oPortefeuille);
        } else {
            $tabTmp = array_merge(array($this->oPortefeuille), $this->{$tab}[$idClient]);
            $this->{$tab}[$idClient] = $tabTmp;
        }
    }
    
    public function ecrirePDFGlobaux($dirSortie)
    {
        $fileName = "Avis_REL_PORT_TITRE_Impression_".date("Ymd").'.pdf';
        $this->buildPDFGlobal('print', $dirSortie, $fileName);

        $fileName = "Avis_REL_PORT_TITRE_Impression_Manuelle_".date("Ymd").'.pdf';
        $this->buildPDFGlobal('mano', $dirSortie, $fileName);
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

        $this->tplManager->renderResponse(
            'EditiqueTitreBundle:portefeuille_pdf:portefeuilleGlobal.pdf.twig',
            $paramTpl,
            $response
        );

        $pdf = $this->pdfManager->render($response->getContent());

        $this->fileManager->ecrireFichier($dirSortie, $fileName, $pdf);

        if ($typeReleve == 'mano') {
            $this->sendMailPourImpressionManuelle($fileName);
        } else {
            $this->transfertVersServeurFichierDSI($dirSortie . $fileName, 'releve');
            $this->sendMail('ANGERS');
        }
    }
}
