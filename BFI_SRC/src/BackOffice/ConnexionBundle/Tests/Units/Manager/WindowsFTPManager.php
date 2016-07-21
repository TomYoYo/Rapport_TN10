<?php

namespace BackOffice\ConnexionBundle\Tests\Units\Manager;

use BackOffice\ConnexionBundle\Tests\Units\Manager\FTPManager;

class WindowsFTPManager extends FTPManager
{

    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     *
     */
    public function beforeTestMethod()
    {
        parent::beforeTestMethod();

        $this->param['dirBanqueClient'] = $this->container->getParameter('svWin.dirBanqueClient');
        $this->param['maskNonClasse'] = $this->container->getParameter('svWin.maskNonClasse');
        $this->param['dirDepotPDF'] = $this->container->getParameter('svWin.dirDepotPDF');
        $this->param['dirDepotPDFPerdu'] = $this->container->getParameter('svWin.dirDepotPDFPerdu');

        $this->manager = new \mock\BackOffice\ConnexionBundle\Manager\WindowsFTPManager(
            $this->param['hostname'],
            $this->param['username'],
            $this->param['password'],
            $this->param['dirBanqueClient'],
            $this->param['maskNonClasse'],
            $this->param['dirDepotPDF'],
            $this->param['dirDepotPDFPerdu']
        );

        $this->manager->setLogManager($this->logManager);

        $this->manager->login();

    }

    public function testConstruct()
    {
        $h = $this->param['hostname'];
        $this
            ->if($this->manager = new \mock\BackOffice\ConnexionBundle\Manager\WindowsFTPManager($h))
            ->string($this->manager->hostname)
            ->isEqualTo($this->param['hostname'])
            ;
    }

    public function testCopieRepClient()
    {
        $this
            ->boolean($this->manager->copieRepClient('0000057', $this->fichierLocalTest))
            ->isTrue()
            ;

        $this
            ->boolean($this->manager->copieRepClient('0000057', $this->fichierLocalTest.'999'))
            ->isFalse()
            ;

        $this->manager->repBqClient = 'toto';
        $this
            ->boolean($this->manager->copieRepClient('0000057', $this->fichierLocalTest))
            ->isFalse()
            ;
    }
}
