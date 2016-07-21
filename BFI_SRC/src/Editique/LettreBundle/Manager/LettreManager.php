<?php

namespace Editique\LettreBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

/**
 * Description of releveManager
 *
 * @author jd labails
 */
class LettreManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public $error = array();
    public $fatalError = false;
    public $datArray = array();
    public $oLettre = null;
    public $oEntity = null;
    public $contentSortie = array();
    public $templates = array();
    public $numLigne = 1;
    public $idClient = '0';
    public $accountNumber = '';

    public function getOLettre()
    {
        return $this->oLettre;
    }
    
    public function getOEntity()
    {
        return $this->oEntity;
    }
    
    public function setEntityManagerPers($em)
    {
        $this->em = $em;
    }
    
    public function prepareLettre($id, $type)
    {
        switch ($type) {
            case 'CHQ':
                $this->oEntity = $this->em
                    ->getRepository('Editique\LettreBundle\Entity\Chequier')
                    ->findOneById($id);
                break;
            case 'IMP':
                $this->oEntity = $this->em
                    ->getRepository('Editique\LettreBundle\Entity\Impaye')
                    ->findOneById($id);
                $this->setNumsImpaye();
                break;
        }
        
        if ($this->oEntity === null) {
            return false;
        }
        
        $this->oLettre = $this->em
            ->getRepository('Editique\LettreBundle\Entity\Lettre')
            ->findOneByIdClient($this->oEntity->getIdClientRemastered());
        
        if ($this->oLettre === null) {
            return false;
        } else {
            $this->checkRaisonSociale();
            $this->setAdresse();
        }
        
        return true;
    }
    
    public function getImpayes()
    {
        $em = $this->em;
        $entities = array();
        
        $query = $em->createQuery(
            "SELECT i
            FROM EditiqueLettreBundle:Impaye i
            WHERE i.dateTrait = :date
            AND i.nbJour > 0
            AND i.nbJour < 10
            AND i.nature <> 'OUV'"
        )->setParameter('date', date('Ymd'));
        //)->setParameter('date', '20151028');
                
        $maybeEntities = $query->getResult();
        
        foreach ($maybeEntities as $maybeEntity) {
            $query2 = $em->createQuery(
                'SELECT i
                FROM EditiqueLettreBundle:Impaye i
                WHERE i.numDossier = :numDos
                AND i.dateTrait IN (
                    SELECT MAX(i2.dateTrait)
                    FROM EditiqueLettreBundle:Impaye i2
                    WHERE i2.dateTrait < :date
                    AND i2.numDossier = :numDos
                )
                '
            )->setParameter('date', date('Ymd'))
            //)->setParameter('date', '20151028')
            ->setParameter('numDos', $maybeEntity->getNumDossier());
            
            $lastTrait = $query2->getSingleResult();
            
            if ($lastTrait->getNbJour() == 0) {
                array_push($entities, $maybeEntity);
            }
        }
        
        return $entities;
    }
    
    private function setNumsImpaye()
    {
        $q = "SELECT CREBISCOM, CREBISDOS, CREBISPRE FROM ZCREBIS0 WHERE CREBISPAY = '" .
            $this->oEntity->getIdClient() . "' AND CREBISMOD = 'INT'";

        $stmt = $this->em->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        $this->oEntity->setNumCompte($res['CREBISCOM']);
        $this->oEntity->setNumDossier($res['CREBISDOS']);
        $this->oEntity->setNumPret($res['CREBISPRE']);
    }
    
    private function checkRaisonSociale()
    {
        if (!$this->oLettre->getRaisonSociale1() && !$this->oLettre->getRaisonSociale2()) {
            $q = "SELECT CLIENARA1, CLIENARA2 FROM ZCLIENA0 WHERE CLIENACLI = ".$this->oEntity->getIdClient();

            $stmt = $this->em->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();
            
            $this->oLettre->setRaisonSociale1($res['CLIENARA1']);
            $this->oLettre->setRaisonSociale2($res['CLIENARA2']);
        }
        
        return $this;
    }
    
    private function setAdresse()
    {
        $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
            "WHERE ADRESSTYP = 1 AND ADRESSCOA = 'CH' AND ADRESSNUM = ".$this->oLettre->getIdClient();

        $stmt = $this->em->getConnection()->prepare($q);
        $stmt->execute();
        $res = $stmt->fetch();
        
        if (!$res) {
            $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
                "WHERE ADRESSTYP = 1 AND ADRESSCOA = 'CO' AND ADRESSNUM = ".$this->oLettre->getIdClient();

            $stmt = $this->em->getConnection()->prepare($q);
            $stmt->execute();
            $res = $stmt->fetch();
            
            if (!$res) {
                $q = "SELECT ADRESSAD1, ADRESSAD2, ADRESSAD3, ADRESSCOP, ADRESSVIL FROM ZADRESS0 " .
                    "WHERE ADRESSTYP = 1 AND ADRESSNUM = ".$this->oLettre->getIdClient();

                $stmt = $this->em->getConnection()->prepare($q);
                $stmt->execute();
                $res = $stmt->fetch();
            }
        }
        
        $this->oLettre->setAdresse1($res['ADRESSAD1']);
        $this->oLettre->setAdresse2($res['ADRESSAD2']);
        $this->oLettre->setAdresse3($res['ADRESSAD3']);
        $this->oLettre->setAdresseCP($res['ADRESSCOP']);
        $this->oLettre->setAdresseVil($res['ADRESSVIL']);
        
        return $this;
    }

    public function ecrireSortie($directory, $type)
    {
        // INIT
        $this->ecritureManager->content = array();
        
        switch ($type) {
            case 'CHQ':
                $this->ecrireLettreChequier();

                $this->templates = array(
                    'page1' => __DIR__ . '/../Resources/pdf_template/let_chequier_template001.pdf',
                );
                break;
            case 'IMP':
                $this->ecrireLettreImpaye();

                $this->templates = array(
                    'page1' => __DIR__ . '/../Resources/pdf_template/let_impaye_template002.pdf',
                );
                break;
        }
        
        $this->contentSortie = $this->ecritureManager->getContent();

        $sortieBrute = implode("\r\n", $this->contentSortie);

        $pdfFileName = $this->getFileName('pdf', $type);
        $txtFileName = $this->getFileName('txt', $type);

        // Ecriture du fichier txt
        $this->fileManager->ecrireFichier($directory, $txtFileName, $sortieBrute);

        // ecrire pdf
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF($type));

        // log dans editique
        $idClient = $this->oEntity->getIdClient();
        $numCpt = $this->oEntity->getNumCompte();
        switch ($type) {
            case 'CHQ':
                $this->logEditique($idClient, $numCpt, 'lettre_chequier', $directory . $pdfFileName);
                
                // transfert vers le serveur de fichier
                $this->transfertVersServeurFichier($idClient, $directory . $pdfFileName);
                
                break;
            case 'IMP':
                $this->logEditique($idClient, $numCpt, 'lettre_impaye', $directory . $pdfFileName);
                
                // transfert vers le serveur DSI
                $this->transfertVersServeurFichierDSI($directory . $pdfFileName, 'impayes');
                $this->sendMail('ANGERS', $pdfFileName);
                
                break;
        }

        // transfert vers le serveur de fichier
        $this->transfertVersServeurFichier($idClient, $directory . $pdfFileName);

        return $pdfFileName;
    }

    public function getPDF($type)
    {
        $response = new Response();
        
        switch ($type) {
            case 'CHQ':
                $template = 'EditiqueLettreBundle:Default:lettre_chequier.pdf.twig';
                break;
            case 'IMP':
                $template = 'EditiqueLettreBundle:Default:lettre_impaye.pdf.twig';
                break;
        }
        
        $this->tplManager->renderResponse(
            $template,
            array(
                'templates' => $this->templates,
                'lettre'    => $this->oLettre,
                'entity'    => $this->oEntity
            ),
            $response
        );

        return $this->pdfManager->render($response->getContent());
    }

    public function getFileName($ext, $type)
    {
        switch ($type) {
            case 'CHQ':
                $txt = 'LettreChequier';
                break;
            case 'IMP':
                $txt = 'LettreImpaye';
                break;
        }
        
        $fileName = "Avis_" .
            trim($this->oEntity->getIdClient()) .
            "_" .
            $txt .
            "_" .
            trim($this->oEntity->getNumCompte()) .
            "_" .
            date('Ymd') .
            "."
            . $ext;
        
        return $fileName;
    }
    
    public function ecrireLettreChequier()
    {
        $this
            ->initLettre(3)
            ->ecrireAdresse();
        
        // Chequier
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne++, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oEntity->getNumCompte(), $this->numLigne++, 3, 20);
    }
    
    public function ecrireLettreImpaye()
    {
        $this
            ->initLettre(4)
            ->ecrireAdresse();
        
        // Impayé
        $this->ecritureManager->ecrireLigneSortie($this->oEntity->getNumDossier(), $this->numLigne, 3, 7);
        $this->ecritureManager->ecrireLigneSortie($this->oEntity->getMontantEch(), $this->numLigne, 12, 14);
        $this->ecritureManager->ecrireLigneSortie($this->oEntity->getDateEch(), $this->numLigne, 28, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oEntity->getIdClient(), $this->numLigne++, 40, 7);
        $this->ecritureManager->ecrireLigneSortie(date('d/m/Y'), $this->numLigne++, 3, 10);
        $this->ecritureManager->ecrireLigneSortie($this->oEntity->getNumCompte(), $this->numLigne++, 3, 20);
    }
    
    public function initLettre($i)
    {
        $ligne1 = '1LET00' . $i . '00' . $this->oLettre->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($ligne1, $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        
        return $this;
    }
    
    public function ecrireAdresse()
    {
        list($raiSoc1, $raiSoc2) = $this->oLettre->getRaisonSociale();
        $this->ecritureManager->ecrireLigneSortie($raiSoc1, $this->numLigne++, 3, 60);
        $this->ecritureManager->ecrireLigneSortie($raiSoc2, $this->numLigne++, 3, 17);
        
        $adresse = $this->oLettre->getAdresseFinale();
        $this->ecritureManager->ecrireLigneSortie($adresse[0], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adresse[1], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adresse[2], $this->numLigne++, 3, 45);
        $this->ecritureManager->ecrireLigneSortie($adresse[3], $this->numLigne++, 3, 45);
        
        return $this;
    }
}
