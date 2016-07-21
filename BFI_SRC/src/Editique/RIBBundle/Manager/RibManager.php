<?php

namespace Editique\RIBBundle\Manager;

use Editique\RIBBundle\Entity\Rib;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of releveManager
 *
 * @author d.briand
 */
class RibManager extends \Editique\MasterBundle\Manager\EditiqueManager
{
    public $ribArray = array();
    public $oRib = null;
    public $contentSortie = array();
    public $numLigne = 1;
    
    public function initEditique()
    {
        $this->ribArray = array();
        $this->oRib = null;
        $this->contentSortie = array();
        $this->numLigne = 1;
        
        parent::initEditique();
    }

    public function getORib()
    {
        return $this->oRib;
    }
    /*     * *************************************************************************
     *
     *              PARTIE LECTURE DE FICHIER
     *
     * ************************************************************************ */

    public function readFile($filePath)
    {
        // Lecture du fichier d'entrée. On passe par un json_decode(json_encode)
        // pour tranformer le tout en tableau (sinon objet SimpleXml)
        @$xml = simplexml_load_file($filePath);
        if ($xml) {
            $this->ribArray = json_decode(json_encode(simplexml_load_file($filePath)), true);
            $this->setORib();
        } else {
            $this->logManager->addError(
                'Le fichier XML (' . $filePath . ') est vide ou comporte une/plusieurs erreur(s).',
                'Editique > RIB',
                'Lecture du XML en entrée'
            );
            $this->addFatalError('Le RIB spécifié est vide ou comporte une erreur. Fichier associé : ' . $filePath);
        }
    }

    public function setORib()
    {
        $this->oRib = new Rib();

        //Gestion du titulaire
        $holder = $this->getHolder();
        $this->oRib->setHolder($holder);

        //Gestion de l'adresse
        list($address1, $address2) = $this->getAddress();
        $this->oRib->setAddress($address1);
        $this->oRib->setAddressSuite($address2);

        //Gestion du numéro de compte
        $this->oRib->setAccountNumber($this->getAccountNumber());

        //Gestion de l'IBAN
        $this->oRib->setIban($this->getIban());

        //Gestion du code BIC
        $this->oRib->setBic($this->getBic());

        // Gestion de l'id client
        $this->oRib->setIdClient($this->getIdClient());
    }

    public function getIdClient()
    {
        $req = $this->entityManager
            ->getConnection()
            ->prepare("SELECT TITULACLI FROM ZTITULA0 WHERE TITULACOM = '" . $this->getAccountNumber() . "'");

        $req->execute();
        $res = $req->fetch();

        if ($res['TITULACLI']) {
            return $res['TITULACLI'];
        } else {
            $this->logManager->addError(
                'Erreur lors de la recherche de l\'id client en base de données.',
                'Editique > RIB',
                'Génération d\'un RIB'
            );
            $this->addFatalError('Erreur lors de la recherche en base');
        }
    }

    public function getHolder()
    {
        $holder = '';

        if (isset($this->ribArray['body']['libCpt']) && !empty($this->ribArray['body']['libCpt'])) {
            $holder .= $this->ribArray['body']['libCpt'];
        }

        $finalHolder = $this->ecritureManager->centrerEspace($holder, 32);
        return $finalHolder;
    }

    public function getAddress()
    {
        $address1 = '';
        
        if (isset($this->ribArray['body']["adrTit1"]) && !empty($this->ribArray['body']["adrTit1"])) {
            $address1 .= $this->ribArray['body']["adrTit1"];
        }
        if (isset($this->ribArray['body']["adrTit2"]) && !empty($this->ribArray['body']["adrTit2"])) {
            if ($address1 != '') {
                $address1 .= " &#8211; ";
            }
            $address1 .= $this->ribArray['body']["adrTit2"];
        }

        $address2 = $this->ribArray['body']['codPosTit'] . " " . $this->ribArray['body']['vilTit'];

        $finalAddress1 = $this->ecritureManager->centrerEspace($address1, 67);
        $finalAddress2 = $this->ecritureManager->centrerEspace($address2, 32);
        return array($finalAddress1, $finalAddress2);
    }

    public function getAccountNumber()
    {
        return $this->ribArray['body']['cptEdt'];
    }

    public function getIban()
    {
        // On doit tronquer les 5 premiers caractères : "IBAN ", pour n'avoir que la chaîne voulue
        return substr($this->ribArray['body']['codIbaPap'], 5, 33);
    }

    public function getBic()
    {
        return $this->ribArray['body']['codIbi'];
    }
    /*     * *************************************************************************
     *
     *              PARTIE ECRITURE DE FICHIER
     *
     * ************************************************************************ */

    public function ecrireSortie($directory)
    {
        $this->numLigne = 1;

        $this->initRIB('001')
            ->writeHolderAndAddress()
            ->writeAccountInformations();

        $this->contentSortie = $this->ecritureManager->getContent();

        $sortieBrute = implode("\r\n", $this->contentSortie);

        $pdfFileName = $this->getFileName('pdf');

        // Ecriture du fichier txt
        $this->fileManager->ecrireFichier($directory, $this->getFileName('txt'), $sortieBrute);

        // ecrire pdf
        $this->fileManager->ecrireFichier($directory, $pdfFileName, $this->getPDF());

        // log dans editique
        $this->logEditique(
            $this->oRib->getIdClient(),
            $this->oRib->getAccountNumber(),
            'rib',
            $directory . $pdfFileName
        );

        // transfert vers le serveur de fichier
        $this->transfertVersServeurFichier($this->oRib->getIdClient(), $directory . $pdfFileName);

        return $sortieBrute;
    }

    public function getPDF()
    {
        $response = new Response();
        $this->tplManager->renderResponse(
            'EditiqueRIBBundle:Default:rib.pdf.twig',
            array(
                'template' => __DIR__ . '/../Resources/pdf_template/rib_template.pdf',
                'rib'      => $this->getORib(),
                'adrFinal' => implode('', $this->getAddress())
            ),
            $response
        );

        return $this->pdfManager->render($response->getContent());
    }

    public function getFileName($ext)
    {
        $fileName = "Avis_" .
            $this->oRib->getIdClient() .
            "_RIB_" .
            $this->getAccountNumber() .
            "_" .
            date('Ymd') .
            "." .
            $ext;
        return $fileName;
    }

    public function initRIB($modelNumber)
    {
        $string = '1RIB' . $modelNumber . '00' . $this->oRib->getIdClient();
        $this->ecritureManager->ecrireLigneSortie($string, $this->numLigne++, 1, 16);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);
        $this->ecritureManager->ecrireLigneSortie(' ', $this->numLigne++);

        return $this;
    }

    public function writeHolderAndAddress()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oRib->getHolder(), $this->numLigne++, 3, 32);
        $this->ecritureManager->ecrireLigneSortie($this->oRib->getAddress(), $this->numLigne++, 3, 67);
        $this->ecritureManager->ecrireLigneSortie($this->oRib->getAddressSuite(), $this->numLigne++, 3, 32);

        return $this;
    }

    public function writeAccountInformations()
    {
        $this->ecritureManager->ecrireLigneSortie($this->oRib->getIban(), $this->numLigne, 3, 34);
        $this->ecritureManager->ecrireLigneSortie($this->oRib->getBic(), $this->numLigne++, 39, 12);

        return $this;
    }

    public function getContentSortie()
    {
        return $this->contentSortie;
    }
}
