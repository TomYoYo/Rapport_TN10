<?php

namespace BackOffice\ParserBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use BackOffice\ParserBundle\Tests\Units\Manager\ParserManager;
use BackOffice\ParserBundle\Tests\Units\Manager\EcritureManager;

class EcritureManager extends ParserManager
{
    public $logManager;
    public $manager;

    public function beforeTestMethod()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->logManager = $container->get('backoffice_monitoring.logManager');
        $this->manager = new \mock\BackOffice\ParserBundle\Manager\EcritureManager($this->logManager);
    }

    public function testGetContent()
    {
        $this->manager->content = array('test');

        $this
            ->array($this->manager->getContent())
            ->hasSize(1)
        ;
    }

    public function testEcrireLigneSortie()
    {
        $this
            ->if($this->manager->ecrireLigneSortie('test', 2))
            ->string($this->manager->content[2])
            ->isEqualTo('test')
            ->if($this->manager->ecrireLigneSortie('test', 3, 2, 1))
            ->string($this->manager->content[3])
            ->isEqualTo(' t')
        ;
    }

    public function testEcrireMontant()
    {
        $this
            ->if($this->manager->ecrireMontant('1 200,20', 2, 0, 10))
            ->string($this->manager->content[2])
            ->isEqualTo('  1 200,20')
            ;
    }

    public function testEcrireDateFromSAB()
    {
        $this
            ->if($this->manager->ecrireDateFromSAB('1140203', 4, 0, 10))
            ->string($this->manager->content[4])
            ->isEqualTo('03/02/2014')
            ;
    }

    public function testAddCaractere()
    {
        $this
            ->string($this->manager->addCaractere('test', 10, '0', true))
            ->isEqualTo('000000test')
            ->string($this->manager->addCaractere('test', 10, '0', false))
            ->isEqualTo('test000000')
            ->string($this->manager->addZero('test', 10))
            ->isEqualTo('000000test')
            ;
    }

    public function testGetContentHTML()
    {
        $this->manager->content = array(' hello', ' dolly');
        $this
            ->string($this->manager->getContentHTML())
            ->isEqualTo("<i style=\"color:#AAA;\">#</i>hello\n<i style=\"color:#AAA;\">#</i>dolly")
            ;
    }

    public function testEcrireNbPage()
    {
        $this->manager->content = array('%nb%', 'dolly%nb%');
        $this
            ->if($this->manager->ecrireNbPage(1, '%nb%'))
            ->string($this->manager->content[0])
            ->isEqualTo("001")
            ;
    }

    public function testCentrerEspace()
    {
        $this
            ->string($this->manager->centrerEspace('test', 10))
            ->isEqualTo("   test   ")
            ->string($this->manager->centrerEspace('test', 2))
            ->isEqualTo("te")
            ;
    }

    public function testAsLetters()
    {
        $this
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1000'))
            ->isEqualTo("mille")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('-1000'))
            ->isEqualTo("moins mille")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1.2'))
            ->isEqualTo("un point deux")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1,2'))
            ->isEqualTo("un virgule deux")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('15'))
            ->isEqualTo("quinze")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('18'))
            ->isEqualTo("dix-huit")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('22'))
            ->isEqualTo("vingt-deux")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('30'))
            ->isEqualTo("trente")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('31'))
            ->isEqualTo("trente-et-un")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('71'))
            ->isEqualTo("soixante-et-onze")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('81'))
            ->isEqualTo("quatre-vingt-un")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('91'))
            ->isEqualTo("quatre-vingt-onze")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('63'))
            ->isEqualTo("soixante-trois")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('75'))
            ->isEqualTo("soixante-quinze")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('95'))
            ->isEqualTo("quatre-vingt-quinze")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('100'))
            ->isEqualTo("cent")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('110'))
            ->isEqualTo("cent dix")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('510'))
            ->isEqualTo("cinq cent dix")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1000'))
            ->isEqualTo("mille")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1110'))
            ->isEqualTo("mille cent dix ")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('5110'))
            ->isEqualTo("cinq mille cent dix")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1000000'))
            ->isEqualTo("un million")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('1000010'))
            ->isEqualTo("un million dix")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('3000010'))
            ->isEqualTo("trois millions dix")
            ->string(\mock\BackOffice\ParserBundle\Manager\EcritureManager::asLetters('3000000'))
            ->isEqualTo("trois millions")
            ;
    }
}
