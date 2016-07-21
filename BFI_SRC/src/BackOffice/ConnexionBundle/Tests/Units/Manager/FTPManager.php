<?php

namespace BackOffice\ConnexionBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use atoum\AtoumBundle\Test\Units;

class FTPManager extends Units\Test
{
    public $param = array();
    public $fichierLocalTest = '';

    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     *
     */
    public function beforeTestMethod()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->fichierLocalTest = $this->container->getParameter('fichierLocalTest');
        $this->param['hostname'] = $this->container->getParameter('svWin.server');
        $this->param['username'] = $this->container->getParameter('svWin.user');
        $this->param['password'] = $this->container->getParameter('svWin.pass');
        $this->manager = new \mock\BackOffice\ConnexionBundle\Manager\FTPManager($this->param);

        $this->logManager = $this->container->get('backoffice_monitoring.logManager');
        $this->manager->setLogManager($this->logManager);

        $this->manager->login();
    }

    public function testSetLogManager()
    {
        $this
            ->if($this->manager->setLogManager($this->logManager))
            ->variable($this->manager->logManager)
            ->isNotNull()
            ;
    }

    public function testLogin()
    {
        $this
            ->boolean($this->manager->login())
            ->isTrue()
            ->boolean($this->manager->isConnected)
            ->isTrue()
            ;

        $this->manager->logout();
        $this->manager->username = 'truc';
        $this
            ->boolean($this->manager->login())
            ->isFalse()
            ->boolean($this->manager->isConnected)
            ->isFalse()
            ;

        $this->manager->logout();
        $this->manager->hostname = 'truc';
        $this
            ->boolean($this->manager->login())
            ->isFalse()
            ->boolean($this->manager->isConnected)
            ->isFalse()
            ;
    }

    public function testLogout()
    {
        //$this->manager->login();

        $this
            ->if($this->manager->logout())
            ->boolean($this->manager->isConnected)
            ->isFalse()
            ;
    }

    public function testConstruct()
    {
        $this
            ->if($this->manager = new \mock\BackOffice\ConnexionBundle\Manager\FTPManager($this->param))
            ->string($this->manager->hostname)
            ->isEqualTo($this->param['hostname'])
            ;
    }

    public function testUploadOk()
    {
        // chemion distant KO
        $fileDistant = 'test.txt';

        // fichier en local de retour
        $fileLocal2 = '/home/FIDUCIAL/spool_fichiers/test/testDownloadFTP'.time().'.txt';

        // test upload
        $this
            ->boolean($this->manager->upload($this->fichierLocalTest, $fileDistant))
            ->isTrue()
            ;

        // test download
        $this
            ->boolean($this->manager->download($fileDistant, $fileLocal2))
            ->isTrue()
            ;
        // test download echec sur fichier distant
        $this
            ->boolean($this->manager->download($fileLocal2, $fileLocal2))
            ->isFalse()
            ;

        // suppression local et distant
        $this
            ->boolean($this->manager->delete($fileDistant))
            ->isTrue()
            ;
    }

    public function testUploadKo()
    {
        // chemion distant KO
        $fileDistant = '/coco/lapin/notHere.txt';

        // test upload
        $this
            ->boolean($this->manager->upload($this->fichierLocalTest, $fileDistant))
            ->isFalse()
            ;

        // suppression local et distant
        $this
            ->boolean($this->manager->delete($fileDistant))
            ->isFalse()
            ;

        // melange file distant et local
        $this
            ->boolean($this->manager->upload($fileDistant, $fileDistant))
            ->isFalse()
            ;

        $this->manager->isConnected = false;
        // test upload
        $this
            ->boolean($this->manager->upload($this->fichierLocalTest, $fileDistant))
            ->isFalse()
            ;

        // suppression local et distant
        $this
            ->boolean($this->manager->delete($fileDistant))
            ->isFalse()
            ;
    }


    public function testDownloadKo()
    {
        // chemion distant KO
        $fileDistant = '/coco/lapin/notHere.txt';

        // test upload
        $this
            ->boolean($this->manager->download($this->fichierLocalTest, $fileDistant))
            ->isFalse()
            ;

        // melange file distant et local
        $this
            ->boolean($this->manager->download($fileDistant, $fileDistant))
            ->isFalse()
            ;

        $this->manager->isConnected = false;
        $this
            ->boolean($this->manager->download($this->fichierLocalTest, $fileDistant))
            ->isFalse()
            ;
    }
}
