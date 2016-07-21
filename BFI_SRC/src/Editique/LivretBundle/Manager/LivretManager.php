<?php

namespace Editique\LivretBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of releveManager
 *
 * @author jdlabails
 */
class LivretManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public $error = array();
    public $fatalError = false;
    public $datArray = array();
    public $oLivret = null;
    public $contentSortie = array();
    public $templates = array();
    public $numLigne = 1;
    public $idClient = '0';
    public $accountNumber = '';

    public function getOLivret()
    {
        return $this->oLivret;
    }

    public function ecrireSortie($directory, $idLivret)
    {
        $this->numLigne = 1;

        $this->oLivret = $this->entityManager
            ->getRepository('Editique\LivretBundle\Entity\Livret')
            ->findOneById($idLivret);

        if ($this->oLivret === null) {
            return false;
        }

        $this->oLivret->setIdClient($this->getIdClient());

        if ($this->oLivret->isLVA()) {
            $this->templates = array(
                'page1' => __DIR__ . '/../Resources/pdf_template/lva_template1.pdf',
                'page2' => __DIR__ . '/../Resources/pdf_template/lva_template2.pdf',
                'page3' => __DIR__ . '/../Resources/pdf_template/lva_template3.pdf',
            );
            $this->ecrire('LVA', '001');
            $this->ecrire('LVA', '002');
            $this->ecrire('LVA', '003');
        } elseif ($this->oLivret->isLDD() && $this->oLivret->isParticulier()) {
            $this->templates = array(
                'page1' => __DIR__ . '/../Resources/pdf_template/ldd_template1.pdf',
                'page2' => __DIR__ . '/../Resources/pdf_template/ldd_template2.pdf',
            );
            $this->ecrire('LDD', '001');
            $this->ecrire('LDD', '002');
            $this->ecrire('LDD', '003');
        } elseif ($this->oLivret->isCSL()) {
            $this->templates = array(
                'page1' => __DIR__ . '/../Resources/pdf_template/csl_template1.pdf',
                'page2' => __DIR__ . '/../Resources/pdf_template/csl_template2.pdf',
            );
            $this->ecrire('CSL', '001');
            $this->ecrire('CSL', '002');
        } else {
            // on vérifie qu'on sait faire
            $this->logManager->AddError(
                'Génération d\'un livret d\'un produit non géré',
                'Editique > livret',
                'Génération d\'un livret'
            );
            return false;
        }

        $this->contentSortie = $this->ecritureManager->getContent();
        $pdfFileName = $this->getFileName('pdf');

        $sortieBrute = implode("\r\n", $this->contentSortie);

        // Ecriture du fichier txt
        $this->fileManager->ecrireFichier($directory, $this->getFileName('txt'), $sortieBrute);

        // ecrire pdf
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF());

        // log dans editique
        $idClient = $this->oLivret->getIdClient();
        $numCpt = $this->oLivret->getNumCptERE();
        $this->logEditique($idClient, $numCpt, 'livret', $directory . $pdfFileName);

        // transfert vers le serveur de fichier
        $this->transfertVersServeurFichier($idClient, $directory . $pdfFileName);

        return $pdfFileName;
    }

    public function getIdClient()
    {
        $numCpt = $this->oLivret->getNumCptERE();
        try {
            $q = "select ZTITULA0.TITULACLI from ZERECOU0, ZTITULA0 where ZTITULA0.TITULACOM= '" . $numCpt . "'";
            $req = $this->entityManager
                ->getConnection()
                ->prepare($q);

            $req->execute();
            $res = $req->fetch();

            return $res['TITULACLI'];
        } catch (Exception $ex) {
            $this->addFatalError('Erreur lors de la rercherche en base');
        }
    }

    public function getPDF()
    {
        $renderTemplate = '';
        $response = new Response();

        if ($this->oLivret->isLVA()) {
            $renderTemplate = "EditiqueLivretBundle:Default:lva.pdf.twig";
        } elseif ($this->oLivret->isLDD() && $this->oLivret->isParticulier()) {
            $renderTemplate = "EditiqueLivretBundle:Default:ldd.pdf.twig";
        } elseif ($this->oLivret->isCSL()) {
            $renderTemplate = "EditiqueLivretBundle:Default:csl.pdf.twig";
        }

        $paramTpl = array(
            'templates' => $this->templates,
            'livret'    => $this->getOLivret()
        );
        $this->tplManager->renderResponse($renderTemplate, $paramTpl, $response);

        return $this->pdfManager->render($response->getContent());
    }

    public function getFileName($ext)
    {
        $fileName = "Avis_" .
            trim($this->oLivret->getIdClient()) .
            "_" .
            trim($this->oLivret->getTypeCptERE()) .
            "_" .
            trim($this->oLivret->getNumCptERE()) .
            "_" .
            date('Ymd_His') .
            "." .
            $ext;
        return $fileName;
    }

    public function ecrire($typeCpt, $num)
    {
        $ligne1 = '1' . $typeCpt . $num . '00' . $this->oLivret->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        if ($num == '001') {
            $this->ecrire001();
        }
    }

    public function ecrire001()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getIdClient(), $this->numLigne, 3, 7);

        $libTit = $this->oLivret->isParticulier() ? 'Nom, Prénom : ' : 'Raison sociale : ';
        $this->ecritureManager->ecrireLigneSortie($libTit, $this->numLigne++, 11, 17);

        $tit = trim($this->oLivret->getRaisonSociale1()) . ' ' . trim($this->oLivret->getRaisonSociale2());
        $this->ecritureManager->ecrireLigneSortie($tit, $this->numLigne++, 3, 65);

        $adr = $this->oLivret->getAdresseFinale();
        $this->ecritureManager->ecrireLigneSortie($adr[0], $this->numLigne++, 3, 69);
        $this->ecritureManager->ecrireLigneSortie($adr[1], $this->numLigne++, 3, 69);
        $this->ecritureManager->ecrireLigneSortie($adr[2], $this->numLigne, 3, 23);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getVilleFinale(), $this->numLigne++, 27, 39);

        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getNumCptERE(), $this->numLigne++, 3, 20);

        $montant = $this->oLivret->getMontantFinal();
        $this->ecritureManager->ecrireLigneSortie($montant[0], $this->numLigne++, 3, 69);
        $this->ecritureManager->ecrireLigneSortie($montant[1], $this->numLigne++, 3, 69);

        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getCompteSupport(), $this->numLigne, 3, 20);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getTaux(), $this->numLigne, 24, 5);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getVersementPeriodiqueO(), $this->numLigne, 30, 1);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getVersementPeriodiqueN(), $this->numLigne, 32, 1);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getComptePreleve(), $this->numLigne, 34, 20);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getLibPeriodicite(), $this->numLigne++, 55, 15);

        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getMontantPeriodique(), $this->numLigne, 3, 26);
        $jv = $this->ecritureManager->addCaractere($this->oLivret->getJourVersement(), 3, ' ', false);
        $this->ecritureManager->ecrireLigneSortie($jv, $this->numLigne, 30, 3);
        $this->ecritureManager->ecrireLigneSortie($this->oLivret->getDateSouscription(), $this->numLigne++, 34, 10);
    }
}
