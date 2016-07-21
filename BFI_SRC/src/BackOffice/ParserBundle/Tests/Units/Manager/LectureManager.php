<?php

namespace BackOffice\ParserBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use  BackOffice\ParserBundle\Tests\Units\Manager\ParserManager;

class LectureManager extends ParserManager
{
    public $logManager;
    public $manager;

    public function beforeTestMethod()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->logManager = $container->get('backoffice_monitoring.logManager');
        $this->manager = new \mock\BackOffice\ParserBundle\Manager\LectureManager($this->logManager);
    }

    public function testConstruct()
    {
        $this
            ->if($this->manager = new \mock\BackOffice\ParserBundle\Manager\LectureManager($this->logManager))
            ->object($this->manager->logManager)
            ->isEqualTo($this->logManager)
            ;
    }

    public function testSetCurrentLine()
    {
        $this
            ->if($this->manager->setCurrentLine('content', 3))
            ->string($this->manager->currentLine)
            ->isEqualTo('content')
            ->integer($this->manager->numLine)
            ->isEqualTo(3)
        ;
    }

    public function testGetDate()
    {
        $this
            ->if($this->manager->setCurrentLine('content', 3))
            ->string($this->manager->getDate())
            ->isEqualTo('00/00/0000')
            ->if($this->manager->setCurrentLine('01/01/2000', 3))
            ->string($this->manager->getDate())
            ->isEqualTo('01/01/2000')
            ;
    }
}
