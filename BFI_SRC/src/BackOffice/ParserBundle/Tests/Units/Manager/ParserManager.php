<?php

namespace BackOffice\ParserBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use atoum\AtoumBundle\Test\Units;

class ParserManager extends Units\Test
{
    public $logManager;
    public $manager;

    public function beforeTestMethod()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->logManager = $container->get('backoffice_monitoring.logManager');
        $this->manager = new \mock\BackOffice\ParserBundle\Manager\ParserManager($this->logManager);
    }

    public function testConstruct()
    {
        $this
            ->if($this->manager = new \mock\BackOffice\ParserBundle\Manager\ParserManager($this->logManager))
            ->object($this->manager->logManager)
            ->isEqualTo($this->logManager)
            ;
    }

    public function testAddAlert()
    {
        $this
            ->if($this->manager->addAlert('test unitaire', 'test unitaire'))
            ->array($this->manager->error)
            ->isNotEmpty()
            ->array($this->manager->getErrors())
            ->isNotEmpty()
        ;
    }

    public function testTransformDate()
    {
        $this
            ->dateTime($this->manager->transformDate('01/02/2000'))
            ->hasDateAndTime('2000', '02', '01', '00', '00', '00')
            ->dateTime($this->manager->transformDate('01/02/2000', false))
            ->hasDateAndTime('2000', '02', '01', '23', '59', '00')
            ;
    }

    public function testEndsWith()
    {
        $this
            ->boolean($this->manager->endsWith('tototiti', 'titi'))
            ->isTrue()
            ;
    }
}
