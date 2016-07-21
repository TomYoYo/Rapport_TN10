<?php

namespace BackOffice\ConnexionBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use atoum\AtoumBundle\Test\Units;

class SFTPManager extends Units\Test
{
    public $param = array();
    public $remotePathExterne;
    public $fichierLocalTest = '';
    public $fichierDistantTest = '';

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

        $this->remotePathExterne = $this->container->getParameter('sabCore.dirSortie2');
        $this->fichierLocalTest = $this->container->getParameter('fichierLocalTest');
        $this->fichierDistantTest = $this->remotePathExterne.'ZXCPTOD0_jd.dat';

        $this->param['hostname'] = $this->container->getParameter('sabCore.server');
        $this->param['username'] = $this->container->getParameter('sabCore.user');
        $this->param['public_key_url'] = $this->container->getParameter('sabCore.clePublic');
        $this->param['private_key_url'] = $this->container->getParameter('sabCore.clePrivee');
        $this->manager = new \mock\BackOffice\ConnexionBundle\Manager\SFTPManager($this->param);

        $this->logManager = $this->container->get('backoffice_monitoring.logManager');
        $this->manager->setLogManager($this->logManager);

        $this->manager->login();
        $this->manager->upload($this->fichierLocalTest, $this->fichierDistantTest);
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
        $this
            ->if($this->manager->logout())
            ->boolean($this->manager->isConnected)
            ->isFalse()
            ;
    }

    public function testConstruct()
    {
        $this
            ->if($this->manager = new \mock\BackOffice\ConnexionBundle\Manager\SFTPManager($this->param))
            ->string($this->manager->hostname)
            ->isEqualTo($this->param['hostname'])
            ;
    }

    public function testUploadOk()
    {
        // chemion distant KO
        $fileDistant = $this->remotePathExterne.'/test.txt';

        // fichier en local de retour
        $fileLocal2 = '/home/FIDUCIAL/spool_fichiers/test/testDownloadFTP'.time().'.txt';

        // test upload
        $this
            ->boolean($this->manager->upload($this->fichierLocalTest, $fileDistant))
            ->isTrue()
            ->boolean($this->manager->download($fileDistant, $fileLocal2))
            ->isTrue()
            ->boolean($this->manager->download($fileLocal2, $fileLocal2))
            ->isFalse()
            ->boolean($this->manager->delete($fileDistant))
            ->isTrue()
            ->boolean($this->manager->upload($this->fichierLocalTest, $fileDistant))
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
            ->boolean($this->manager->delete($fileDistant))
            ->isTrue()
            ->boolean($this->manager->upload($fileDistant, $fileDistant))
            ->isFalse()
            ;
    }

    public function testDownload()
    {
        $this->manager->login();

        // chemin distant KO
        $fileDistant = '/coco/lapin/notHere.txt';

        // test download ko
        $this
            ->boolean($this->manager->download($this->fichierLocalTest, $fileDistant))
            ->isFalse()
            ->boolean($this->manager->download($fileDistant, $fileDistant))
            ->isFalse()
            ;

        $fileDistant = $this->remotePathExterne.'ZXCPTOD0_jd.dat';
        $this
            ->boolean($this->manager->chmod($fileDistant, '664'))
            ->isTrue()
            ->boolean($this->manager->upload($this->fichierLocalTest, $fileDistant))
            ->isTrue()
            ->boolean($this->manager->chmod($fileDistant, '777'))
            ->isTrue()
            ->boolean($this->manager->download($fileDistant, $this->fichierLocalTest.time()))
            ->isTrue()
            ->boolean($this->manager->chmod($fileDistant, '333'))
            ->isTrue()
            ->boolean($this->manager->download($fileDistant, $this->fichierLocalTest.time()))
            ->isFalse()
            ;
    }

    public function testDelete()
    {
        $this
            ->boolean($this->manager->chmod($this->fichierDistantTest, '664'))
            ->isTrue()
            ->boolean($this->manager->upload($this->fichierLocalTest, $this->fichierDistantTest))
            ->isTrue()
            ->boolean($this->manager->chmod($this->fichierDistantTest, '664'))
            ->isTrue()
            ->boolean($this->manager->upload($this->fichierLocalTest, $this->fichierDistantTest, '000'))
            ->isTrue()
            ->boolean($this->manager->chmod($this->fichierDistantTest, '666'))
            ->isTrue()
            ->boolean($this->manager->delete($this->fichierDistantTest))
            ->isTrue()
            ->boolean($this->manager->chmod($this->fichierDistantTest, '664'))
            ->isTrue()
            ->boolean($this->manager->upload($this->fichierLocalTest, $this->fichierDistantTest))
            ->isTrue()

            ;
    }

    public function testKoFlux()
    {
        $this->manager->conn_sftp = null;
        $this
            ->boolean($this->manager->chmod('toto', '644'))
            ->isFalse()
            ->boolean($this->manager->upload('toto', 'titi'))
            ->isFalse()
            ->boolean($this->manager->download('toto', 'titi'))
            ->isFalse()
            ->boolean($this->manager->mv('toto', 'titi'))
            ->isFalse()
            ->boolean($this->manager->delete('toto', 'titi'))
            ->isFalse()
            ->boolean($this->manager->listFiles('toto', 'titi'))
            ->isFalse()
            ;
    }

    public function testMv()
    {
        $this
            ->boolean($this->manager->chmod($this->fichierDistantTest, '664'))
            ->isTrue()
            ->boolean($this->manager->upload($this->fichierLocalTest, $this->fichierDistantTest))
            ->isTrue()
            ->boolean($this->manager->chmod($this->fichierDistantTest, '666'))
            ->isTrue()
            ->boolean($this->manager->mv($this->fichierDistantTest, $this->remotePathExterne.'/coco/'))
            ->isFalse()
            ->boolean($this->manager->mv($this->fichierDistantTest, $this->remotePathExterne))
            ->isTrue()
            ;
    }

    public function testListFiles()
    {
        $this
            ->array($this->manager->listFiles('/coco/'))
            ->isEmpty()
            ->array($this->manager->listFiles($this->remotePathExterne))
            ->isNotEmpty()
            ;
    }

    public function testFichierMasque()
    {
        $this
            ->array($this->manager->fichiersMasques($this->remotePathExterne, "/\.txt$/"))
            ->isNotEmpty()
            ->array($this->manager->fichiersMasques($this->remotePathExterne, "/\.cococo$/"))
            ->isEmpty()
            ;
    }

    public function testFichiersSousDossiersMasques()
    {
        $this->manager->login();
        $this
            ->array($this->manager->fichiersSousDossiersMasques($this->remotePathExterne, '/CTO/'))
            ->isNotEmpty()
            ->array($this->manager->fichiersSousDossiersMasques($this->remotePathExterne, '/CTO/', "/\.cococo$/"))
            ->isEmpty()
            ;
    }
}
