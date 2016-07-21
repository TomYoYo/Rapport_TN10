<?php

namespace Editique\MasterBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use atoum\AtoumBundle\Test\Units;

class EditiqueManager extends Units\Test
{
    public $kernel;
    public $manager;
    public $container;
    public $entityManager;
    public $logManager;
    public $ecritureManager;
    public $lectureManager;
    public $fileManager;
    public $windowsFTPManager;
    public $pdfManager;
    public $tplManager;

    public $dirSortie = '/home/FIDUCIAL/spool_fichiers/test/';

    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     *
     */
    public function beforeTestMethod()
    {
        $this->kernel = new \AppKernel('dev', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager('bfi2');

        $this->logManager = $this->container->get('backoffice_monitoring.logManager');
        $this->ecritureManager = $this->container->get('backoffice_parser.ecritureManager');
        $this->lectureManager = $this->container->get('backoffice_parser.lectureManager');
        $this->fileManager = $this->container->get('backoffice_file.fileManager');
        $this->windowsFTPManager = $this->container->get('back_office_connexion.windowsFTP');
        $this->pdfManager = $this->container->get('ps_pdf.facade');
        $this->tplManager = $this->container->get('templating');

        $this->manager = new \mock\Editique\MasterBundle\Manager\EditiqueManager();
        $this->manager->setEcritureManager($this->ecritureManager);
        $this->manager->setLectureManager($this->lectureManager);
        $this->manager->setEntityManager($this->entityManager);
        $this->manager->setLogManager($this->logManager);
        $this->manager->setFileManager($this->fileManager);
        //$this->manager->setWindowsFTPManager($this->windowsFTPManager);
        $this->manager->setPDFManager($this->pdfManager);
        $this->manager->setTplManager($this->tplManager);
    }

    public function testSetEcritureManager()
    {
        $this
            ->if($this->manager->setEcritureManager($this->ecritureManager))
            ->variable($this->manager->ecritureManager)
            ->isNotNull()
        ;
    }

    public function testSetLectureManager()
    {
        $this
            ->if($this->manager->setLectureManager($this->lectureManager))
            ->variable($this->manager->lectureManager)
            ->isNotNull()
        ;
    }

    public function testSetLogManager()
    {
        $this
            ->if($this->manager->setLogManager($this->logManager))
            ->variable($this->manager->logManager)
            ->isNotNull()
        ;
    }

    public function testSetEntityManager()
    {
        $this
            ->if($this->manager->setEntityManager($this->entityManager))
            ->variable($this->manager->entityManager)
            ->isNotNull()
        ;
    }

    public function testSetWindowsFTPManager()
    {
        $this
            ->if($this->manager->setWindowsFTPManager($this->windowsFTPManager))
            ->variable($this->manager->windowsFTPManager)
            ->isNotNull()
        ;
    }

    public function testSetFileManager()
    {
        $this
            ->if($this->manager->setFileManager($this->fileManager))
            ->variable($this->manager->fileManager)
            ->isNotNull()
        ;
    }

    public function testSetPDFManager()
    {
        $this
            ->if($this->manager->setPDFManager($this->pdfManager))
            ->variable($this->manager->pdfManager)
            ->isNotNull()
        ;
    }

    public function testSetTplManager()
    {
        $this
            ->if($this->manager->setTplManager($this->tplManager))
            ->variable($this->manager->tplManager)
            ->isNotNull()
        ;
    }

    public function testAddError()
    {
        $this
            ->if($this->manager->addError('erreur test'))
            ->array($tabError = $this->manager->getErrors())
            ->isNotEmpty($tabError)
            ->strictlyContains('erreur test')
        ;
    }

    public function testAddFatalError()
    {
        $this
            ->if($this->manager->addFatalError('toto'))
            ->array($this->manager->error)
            ->hasSize(1)
            ->boolean($this->manager->getFatalError())
            ->isTrue()
        ;
    }
}
