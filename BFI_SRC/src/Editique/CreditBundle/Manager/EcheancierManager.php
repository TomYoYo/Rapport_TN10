<?php

namespace Editique\CreditBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use Editique\CreditBundle\Manager\EcheancierReader;

/**
 * Manager pour la création d'échéancier de crédit
 *
 * @author d.briand
 */
class EcheancierManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    const SEUIL_NB_ECH_UNE_PAGE = 48;
    const SEUIL_NB_ECH_DEUX_PAGES = 116;
    const SEUIL_NB_ECH_DERNIERE_PAGE = 68;

    public $oCredit = null;
    public $nbEcheancesRestantes = 0;
    public $nbPage = '001';
    public $numPage = '001';
    public $enchainementTpl = array();
    public $tabStartAndLength = array();
    public $tabEcheance = array();
    public $typeSpool = null;

    public function __construct()
    {
        $this->reader = new EcheancierReader();
    }
    
    public function initCompte()
    {
        $this->oCredit = null;
        $this->nbEcheancesRestantes = 0;
        $this->nbPage = '001';
        $this->numPage = '001';
        $this->enchainementTpl = array();
        $this->tabStartAndLength = array();
        $this->tabEcheance = array();
        $this->typeSpool = null;
    }
    
    public function setEntityManager($em)
    {
        $this->entityManager = $em;
        $this->reader->setEntityManager($em);
    }
    
    public function lireContent($parameter)
    {
        $this->tabEcheance = array();
        
        switch ($this->typeSpool) {
            case 'XML':
                $this->oCredit = $this->reader->lireXML($parameter);
                break;
            case 'TXT':
                $this->oCredit = $this->reader->lireTXT($parameter);
                break;
            case 'BDD':
                $this->oCredit = $this->reader->lireBDD($parameter);
                break;
        }
        
        if (!$this->oCredit) {
            $this->addFatalError('Spool incorrect');
        }
    }
    
    public function getOCredit()
    {
        return $this->oCredit;
    }

    public function ecrireSortie($directory)
    {
        // pas de bras, pas de chocolat
        if ($this->oCredit === null
            || !is_object($this->oCredit)
            || get_class($this->oCredit) != 'Editique\CreditBundle\Entity\Credit') {
            return false;
        }
        
        foreach ($this->oCredit->getEcheances() as $ech) {
            $this->tabEcheance[] = $ech;
        }
        
        $this->nbEcheancesRestantes = count($this->tabEcheance);
        
        if ($this->nbEcheancesRestantes <= self::SEUIL_NB_ECH_UNE_PAGE) {
            $this->getECH001();
        } elseif ($this->nbEcheancesRestantes <= self::SEUIL_NB_ECH_DEUX_PAGES) {
            $this->getECH002();
            $this->getECH004();
            $this->numPage = '002';
            $this->nbPage = '002';
        } else {
            $this->getECH002();
            $this->genereSortieSuite();
        }

        return $this->finaliserEcriture($directory);
    }

    public function genereSortieSuite()
    {
        // On ajoute une page supplémentaire (correspondant
        $this->numPage ++;
        $this->numPage = $this->ecritureManager->addZero($this->numPage, 3);

        if ($this->nbEcheancesRestantes > self::SEUIL_NB_ECH_DERNIERE_PAGE) {
            $this->getECH003();
            $this->genereSortieSuite();
        } else {
            $this->getECH004();
        }
    }

    public function finaliserEcriture($directory)
    {
        $this->ecritureManager->ecrireNbPage($this->numPage, $this->nbPage);

        $this->dateEcriture = date('YmdHis');
        $txt = $this->getFileName('txt');
        $pdf = $this->getFileName('pdf');
        $sortieBrute = implode("\r\n", $this->ecritureManager->content);

        // on genere les sorties txt et pdf
        $this->fileManager->ecrireFichier($directory, $txt, $sortieBrute);
        $this->fileManager->ecrireFichier($directory, $pdf, $this->getPDF());

        // log dans editique
        $idClient = $this->oCredit->getIdClient();
        $idCredit = $this->oCredit->getNumPret();
        $this->logEditique($idClient, $idCredit, 'echeancier', $directory . $pdf);

        // transfert vers le serveur de fichier
        $this->transfertVersServeurFichier($idClient, $directory . $pdf);

        return $pdf;
    }

    /**
     * Renvoi le contenu du fichier PDF généré
     * @return type
     */
    public function getPDF()
    {
        $response = new Response();
        $tpl = array(
            'ech_template1' => __DIR__ . '/../Resources/pdf_template/ech_template1.pdf',
            'ech_template2' => __DIR__ . '/../Resources/pdf_template/ech_template2.pdf',
            'ech_template3' => __DIR__ . '/../Resources/pdf_template/ech_template3.pdf',
            'ech_template4' => __DIR__ . '/../Resources/pdf_template/ech_template4.pdf',
        );

        $paramTpl = array(
            'oCredit'         => $this->oCredit,
            'tabEcheance'     => $this->oCredit->getEcheances(),
            'tplArray'        => $tpl,
            'nbPage'          => $this->numPage,
            'enchainementTpl' => $this->enchainementTpl,
            'startAndLength'  => $this->tabStartAndLength,
        );
        $this->tplManager->renderResponse('EditiqueCreditBundle:pdf:echeancier.pdf.twig', $paramTpl, $response);

        return $this->pdfManager->render($response->getContent());
    }

    public function getFileName($ext)
    {
        $idClient = trim($this->oCredit->getIdClient());
        return
            "Avis_" .
            $idClient .
            "_ECHEANCIER_" .
            $this->oCredit->getNumDos() .
            "_" .
            $this->oCredit->getNumPret() .
            "_" .
            $this->dateEcriture .
            "." .
            $ext;
    }

    public function getECH001()
    {
        $this
            ->initECH('001')
            ->ecrireLigneInfosGenerales()
            ->ecrireLignesAdresse()
            ->ecrireLignesInfosPret()
            ->ecrireLigneTotaux()
            ->ecrireTitresAndLibelles();
        
        // Echeances
        for ($i = 1; $i <= 48; $i++) {
            $this->ecrireLigneEcheance();
        }

        $this->tabStartAndLength[] = array('start' => 0, 'length' => 48);
        $this->enchainementTpl  [] = 'ech_template1';
    }

    public function getECH002()
    {
        $this
            ->initECH('002')
            ->ecrireLigneInfosGenerales()
            ->ecrireLignesAdresse()
            ->ecrireLignesInfosPret()
            ->ecrireTitresAndLibelles();
        
        // Echeances
        for ($i = 1; $i <= 48; $i++) {
            $this->ecrireLigneEcheance();
        }

        $this->tabStartAndLength[] = array('start' => 0, 'length' => 48);
        $this->enchainementTpl  [] = 'ech_template2';
    }

    public function getECH003()
    {
        $this
            ->initECH('003')
            ->ecrireLigneInfosGenerales(false);
        
        $last = end($this->tabStartAndLength);
        $start = $last['start'] + $last['length'];

        // Echeances
        for ($i = 1; $i <= 64; $i++) {
            $this->ecrireLigneEcheance();
        }

        $this->tabStartAndLength[] = array('start' => $start, 'length' => 68);
        $this->enchainementTpl  [] = 'ech_template3';
    }

    public function getECH004()
    {
        $this
            ->initECH('004')
            ->ecrireLigneInfosGenerales(false)
            ->ecrireLigneTotaux();
        
        $last = end($this->tabStartAndLength);
        $start = $last['start'] + $last['length'];

        // Echeances
        for ($i = 1; $i <= 64; $i++) {
            $this->ecrireLigneEcheance();
        }

        $this->tabStartAndLength[] = array('start' => $start, 'length' => 68);
        $this->enchainementTpl  [] = 'ech_template4';
    }

    /**
     * Ecrit la premiere ligne qui precise la maquette les deux lignes vides
     * suivantes et le type de compte
     * @param int $num numero de la maquette
     * @return object EcheancierManager
     */
    public function initECH($num)
    {
        $ligne1 = '1ECH' . $num . '00' . $this->oCredit->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }

    public function ecrireLigneInfosGenerales($full = true)
    {
        $this->ecritureManager->ecrireLigneSortie($this->numPage, $this->numLigne, 3, 3);
        $this->ecritureManager->ecrireLigneSortie($this->nbPage, $this->numLigne, 7, 3);

        if ($full) {
            $this->ecritureManager->ecrireLigneSortie($this->oCredit->getIdClient(), $this->numLigne, 11, 7);
            $this->ecritureManager->ecrireLigneSortie($this->oCredit->getNumPret(), $this->numLigne, 19, 16);
        }

        $this->numLigne++;

        return $this;
    }

    public function ecrireLignesAdresse()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getDenomitationComplete(), $this->numLigne++, 3, 65);

        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getAdresse1(), $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getAdresse2(), $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getAdresse3(), $this->numLigne++, 3, 45);
        $adr5 = $this->oCredit->getCodePostal() . ' ' . $this->oCredit->getVille();
        $this->ecritureManager->ecrireLigneSortie($adr5, $this->numLigne++, 3, 45);

        return $this;
    }

    public function ecrireLignesInfosPret()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getMontantPret(), $this->numLigne, 3, 21);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTaux(), $this->numLigne++, 25, 40);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTAEG(), $this->numLigne, 3, 6);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getDateEdition(), $this->numLigne, 10, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getDuree(), $this->numLigne++, 21, 15);

        return $this;
    }

    public function ecrireLigneTotaux()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getMontantCapital(), $this->numLigne, 3, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTotalInteret(), $this->numLigne, 19, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTotalHorsAssurance(), $this->numLigne++, 35, 15);

        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTotalAssurance(), $this->numLigne, 3, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTotalPaye(), $this->numLigne++, 19, 15);

        return $this;
    }
    
    public function ecrireTitresAndLibelles()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getLibelleTauxNominal(), $this->numLigne, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getLibelleTEG(), $this->numLigne++, 34, 15);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTitre(), $this->numLigne, 3, 36);
        $this->ecritureManager->ecrireLigneSortie($this->oCredit->getTitre2(), $this->numLigne++, 40, 14);
        
        return $this;
    }

    public function ecrireLigneEcheance()
    {
        $echeance = array_shift($this->tabEcheance);
        if ($echeance !== null) {
            $this->nbEcheancesRestantes--;
            
            $this->ecritureManager->ecrireLigneSortie($echeance->getNumEcheance(), $this->numLigne, 3, 6);
            $capRestDu = number_format($echeance->getCapRestDu(), 2, ',', ' ');
            $this->ecritureManager->ecrireLigneSortie($capRestDu, $this->numLigne, 10, 15);
            $this->ecritureManager->ecrireLigneSortie($echeance->getCapAmorti(), $this->numLigne, 26, 15);
            $this->ecritureManager->ecrireLigneSortie($echeance->getInteretEch(), $this->numLigne++, 42, 15);

            $this->ecritureManager->ecrireLigneSortie($echeance->getMontantHorsAss(), $this->numLigne, 3, 15);
            $this->ecritureManager->ecrireLigneSortie($echeance->getMontantAss(), $this->numLigne, 19, 15);
            $this->ecritureManager->ecrireLigneSortie($echeance->getTotalEcheance(), $this->numLigne, 35, 15);
            $this->ecritureManager->ecrireLigneSortie($echeance->getTotalEcheance(), $this->numLigne++, 51, 15);
        }
    }
}
