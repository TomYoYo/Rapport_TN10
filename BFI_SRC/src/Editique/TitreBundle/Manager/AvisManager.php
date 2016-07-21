<?php

namespace Editique\TitreBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

class AvisManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    const SEUIL_NB_LG_UNE_PAGE = 37;
    const SEUIL_NB_LG_DEUX_PAGES = 57;
    
    public $numPage = '001';
    public $nbPage = '%0%';
    public $oAvis = null;
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
    public function initAvis()
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
    
    
    // Lecture
    public function getAvis()
    {
        $avis = $this->entityManager
            ->getRepository('Editique\TitreBundle\Entity\Avis')
            ->findLast();
        
        return $avis;
    }
    
    public function compterNbLignes()
    {
        return count($this->oAvis->getValeurs());
    }
    
    public function getIdEsab()
    {
        $query = "SELECT BAGGCOCAB FROM ZBAGGCO0" .
            " WHERE BAGGCOCLI LIKE '" . $this->oAvis->getIdClient() . "%'";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        $res = $req->fetch();
        
        return $res['BAGGCOCAB'];
    }
    
    public function getDetails()
    {
        $query = "SELECT Z00264 AS dtCateg, Z00253 AS dtVal, Z00003 AS nomVal, Z00008 AS codeVal," .
            " Z00254 AS quantite, Z00258 AS sens, Z00255 AS cours, Z00360 AS montantNet FROM FDEUFCH00_PELDET_LDET" .
            " WHERE NUPORT = '" . $this->oAvis->getNumPort() . "'" .
            " AND RACINE LIKE '" . $this->oAvis->getIdClient() . "%'";
        
        $req = $this->entityManager->getConnection()->prepare($query);
        $req->execute();
        $res = $req->fetchAll();
        
        return $res;
    }
    
    //Enregistrement
    
    public function setData($avis)
    {
        $this->oAvis = $avis;
        $this->oAvis->setIdEsab($this->getIdEsab());
        $this->oAvis->setDetails($this->getDetails());
    }
    
    // Ecriture
    
    public function ecrireSortie()
    {
        // on genere la sortie selon le nombre de lignes
        $this->nbLignesValeursRestantes = $this->compterNbLignes();
        if ($this->nbLignesValeursRestantes <= self::SEUIL_NB_LG_UNE_PAGE) {
            $this->getATI001();
        } else {
            $this->getATI002();
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
        if ($this->nbLignesValeursRestantes <= self::SEUIL_NB_LG_DEUX_PAGES) {
            $this->getATI003();
        } else {
            // on affiche une liste
            $this->getATI004();
            if ($this->boucleIterator== 30) {
                $this->logManager->AddError(
                    'Génération d\'un avis d\'opéré de plus de 30 pages... (num cpt : ' .
                    $this->oAvis->getNumCompte().')',
                    'Editique > Titre',
                    'Génération d\'un avis d\'opéré'
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
        $this->oAvis->nbPage = $this->numPage;

        $this->dateEcriture = date('YmdHis');
        $txt = $this->getFileName('txt');
        $pdf = $this->getFileName('pdf');
        $sortieBrute = implode("\r\n", $this->ecritureManager->content);
        
        // on genere les sorties txt et pdf
        $this->fileManager->ecrireFichier($dirSortie, $txt, $sortieBrute);
        $this->fileManager->ecrireFichier($dirSortie, $pdf, $this->getPDF());

        // log dans editique
        $idClient = $this->oAvis->getIdClient();
        $numCpt = $this->oAvis->getNumCompte();
        $type = 'avis_opere';
        $this->logEditique($idClient, $numCpt, $type, $dirSortie . $pdf);

        // transfert vers le serveur de fichier
        //$this->transfertVersServeurFichier($idClient, $dirSortie . $pdf);

        return $pdf;
    }
    
    public function getFileName($ext)
    {
        $idClient = trim($this->oAvis->getIdClient());
        return
            "Avis_" .
            $idClient .
            "_AVIS_OPERE_" .
            $this->oAvis->getNumCompte() .
            "_" .
            $this->dateEcriture .
            "." .
            $ext;
    }
    
    public function getATI001()
    {
        $this
            ->initATI('001');

        $this->enchainementTpl [] = 'template001';
    }
    
    public function getATI002()
    {
        $this
            ->initATI('002');

        $this->enchainementTpl [] = 'template002';
    }
    
    public function getATI003()
    {
        $this
            ->initATI('003');

        $this->enchainementTpl [] = 'template003';
    }
    
    public function getATI004()
    {
        $this
            ->initATI('004');

        $this->enchainementTpl [] = 'template004';
    }
    
    
    
    /**
     * Ecrit la premiere ligne qui precise la maquette les deux lignes vides
     * suivantes et le type de compte
     * @param int $num numero de la maquette
     * @return object ReleveManger
     */
    public function initATI($num)
    {
        $ligne1 = '1ATI' . $num . '00' . $this->oAvis->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }
    
    // PDF
    public function getListeTplPDF()
    {
        return array(
            'template001' => __DIR__ . '/../Resources/pdf_template/avis_OP001.pdf',
            'template002' => __DIR__ . '/../Resources/pdf_template/avis_OP002.pdf',
            'template003' => __DIR__ . '/../Resources/pdf_template/avis_OP003.pdf',
            'template004' => __DIR__ . '/../Resources/pdf_template/avis_OP004.pdf',
        );
    }

    public function getPDF()
    {
        $response = new Response();
        $tpl = $this->getListeTplPDF();

        // on stocke les infos pour pdf dans l'objet pour reutilisation sur le pdf global
        $this->oAvis->enchainementTpl = $this->enchainementTpl;
        $this->oAvis->startAndLength = $this->tabStartAndLength;
        $this->oAvis->nbPage = $this->numPage;

        $paramTpl = array(
            'oAvis'     => $this->oAvis,
            'tplArray'  => $tpl
        );
        $this->tplManager->renderResponse('EditiqueTitreBundle:avis_pdf:avisOpere.pdf.twig', $paramTpl, $response);

        return $this->pdfManager->render($response->getContent());
    }
    
    public function integrerAvisPourImpression()
    {
        $idClient = $this->oAvis->getIdClient();

        // on impacte le bon tableau en fonction du nombre de plis
        // pour cela on selectionne le bon attribut via une variable globale
        $tab = 'tab_releve_global_print';
        if ($this->oAvis->nbPage > 8) {
            $tab = 'tab_releve_global_mano';
        }

        // on init le sous tableau si client pas encore rencontre
        if (!isset($this->{$tab}[$idClient])) {
            $this->{$tab}[$idClient] = array($this->oAvis);
        } else {
            $tabTmp = array_merge(array($this->oAvis), $this->{$tab}[$idClient]);
            $this->{$tab}[$idClient] = $tabTmp;
        }
    }
    
    public function ecrirePDFGlobaux($dirSortie)
    {
        $fileName = "Avis_Opere_Impression_".date("Ymd").'.pdf';
        $this->buildPDFGlobal('print', $dirSortie, $fileName);

        $fileName = "Avis_Opere_Impression_Manuelle_".date("Ymd").'.pdf';
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
            'EditiqueTitreBundle:avis_pdf:avisOpereGlobal.pdf.twig',
            $paramTpl,
            $response
        );

        $pdf = $this->pdfManager->render($response->getContent());

        $this->fileManager->ecrireFichier($dirSortie, $fileName, $pdf);

        if ($typeReleve == 'mano') {
            //$this->sendMailPourImpressionManuelle($fileName);
        } else {
            //$this->transfertVersServeurFichierDSI($dirSortie . $fileName, 'releve');
            //$this->sendMail('ANGERS');
        }
    }
}
