<?php

namespace Editique\MasterBundle\Manager;

use BackOffice\ParserBundle\Manager\EcritureManager;
use BackOffice\ParserBundle\Manager\LectureManager;
use BackOffice\MonitoringBundle\Manager\LogManager;

abstract class EditiqueManager
{
    public $error = array();
    public $fatalError = false;
    public $contentSortie = array();
    public $numLigne = 1;
    public $ecritureManager;
    public $lectureManager;
    public $logManager;
    public $entityManager;
    public $fileManager;
    public $windowsFTPManager;
    public $angersFTPManager;
    public $tplManager;
    public $pdfManager;
    
    public function initEditique()
    {
        $this->error = array();
        $this->fatalError = false;
        $this->contentSortie = array();
        $this->numLigne = 1;
    }

    public function addError($e)
    {
        $this->error[] = $e;
    }

    public function addFatalError($e)
    {
        $this->addError($e);
        $this->fatalError = true;
    }

    public function getErrors()
    {
        return $this->error + $this->ecritureManager->getErrors() + $this->lectureManager->getErrors();
    }

    public function getFatalError()
    {
        return $this->fatalError;
    }

    public function setEcritureManager(EcritureManager $em)
    {
        $this->ecritureManager = $em;
    }

    public function setLectureManager(LectureManager $lm)
    {
        $this->lectureManager = $lm;
    }

    public function setLogManager(LogManager $lm)
    {
        $this->logManager = $lm;
        if (isset($this->reader)) {
            $this->reader->setLogManager($lm);
        }
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    public function setWindowsFTPManager($wftp)
    {
        $this->windowsFTPManager = $wftp;
    }

    public function setAngersFTPManager($aftp)
    {
        $this->angersFTPManager = $aftp;
    }

    public function setFileManager(\BackOffice\FileBundle\Manager\FileManager $fm)
    {
        $this->fileManager = $fm;
    }

    public function setPDFManager($pm)
    {
        $this->pdfManager = $pm;
    }

    public function setTplManager($tpl)
    {
        $this->tplManager = $tpl;
    }

    public function logEditique($idClient, $numCpt, $typeDoc, $filePath)
    {
        if ($this->entityManager == null) {
            $this->logManager->addError(
                'L\'enregistrement en base de données n\'a pas pu se faire.',
                'Editique > Master',
                'Insertion d\'un editique en base'
            );
            return;
        }
        if ($this->entityManager instanceof \Doctrine\Bundle\DoctrineBundle\Registry) {
            $this->entityManager = $this->entityManager->getManager();
        }

        $e = new \Editique\MasterBundle\Entity\Editique();
        $e
            ->setIdClient($idClient)
            ->setNumCpt($numCpt)
            ->setTypeDoc($typeDoc)
            ->setFilePath($filePath)
            ->setIdUtilisateur(0)
            ->setDtGeneration(new \DateTime);
        $this->entityManager->persist($e);
        $this->entityManager->flush($e);
    }

    /**
     * Transfert vers le serveur de fichier windows
     */
    public function transfertVersServeurFichier($idClient, $filePath)
    {
        if (!$this->windowsFTPManager) {
            $this->logManager->addError(
                'Le serveur de fichier n\'est pas initialisé ou pas correctement.',
                'Editique > Master',
                'Déplacement vers serveur de fichier windows'
            );
            return;
        }
        
        // connexion ftp vers le serveur ftp
        if (!$this->windowsFTPManager->isConnected) {
            $this->logManager->addError(
                'Le serveur de fichier n\'est pas accessible.',
                'Editique > Master',
                'Déplacement vers serveur de fichier windows'
            );
            return;
        } else {
            $this->windowsFTPManager->copieRepClient($idClient, $filePath);
        }
    }
    
    public function transfertVersServeurFichierDSI($filePath, $type)
    {
        if (!$this->windowsFTPManager) {
            return;
        }
        
        // connexion ftp vers le serveur ftp
        if (!$this->windowsFTPManager->isConnected) {
            $this->logManager->addError(
                'Le serveur de fichier n\'est pas accessible.',
                'Editique > Master',
                'Déplacement vers serveur de fichier windows'
            );
            return;
        } else {
            $this->windowsFTPManager->copieDSI($filePath, $type);
        }
    }

    public function transfertVersAngers($filePath, $type = '')
    {
        if (!$this->angersFTPManager) {
            return;
        }

        // on reinitialise la connexion avec angers car elle arrive en fin de process
        // et a pu deja se fermer
        $this->angersFTPManager->logout();
        $this->angersFTPManager->login();

        // connexion ftp vers le serveur ftp
        if (!$this->angersFTPManager->isConnected) {
            $this->logManager->addError(
                'Le serveur d\'Angers n\'est pas accessible.',
                'Editique > Master',
                'Déplacement vers serveur Angers'
            );
            return;
        } else {
            $this->angersFTPManager->copieRep($filePath, $type);
        }
    }
    
    public function sendMail($notificationGroup, $fileName = "Non défini")
    {
        // Contenu
        $content = 'Un nouvel Editique vient d\'être généré. Ce dernier doit être transféré au serveur ' .
            'd\'impression des Editiques.<br/><br/><b>Nom du fichier :</b> '.$fileName;
        
        // Envoi mail à l'utilisateur
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";
        
        mail(
            $this->getDestMailErreur($notificationGroup),
            "[BANQUE FIDUCIAL] Nouvel Editique",
            $this->tplManager->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                'parts' => array(
                    array(
                        'title' => 'Nouvel Editique',
                        'content' => $content
                    )
                )
            )),
            $headers
        );
    }
    
    protected function getDestMailErreur($notificationGroup)
    {
        $repoUser = $this->entityManager->getRepository('BackOfficeUserBundle:Profil');
        $users = $repoUser->search(array('notification' => $notificationGroup));

        $to = array();
        foreach ($users as $u) {
            $to []= $u->getPrenom().' '.$u->getNom() . '<' . $u->getEmail() . '>';
        }
        
        return implode(',', $to);
    }
}
