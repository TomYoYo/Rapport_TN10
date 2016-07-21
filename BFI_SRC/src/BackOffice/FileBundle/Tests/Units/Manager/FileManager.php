<?php

namespace BackOffice\FileBundle\Tests\Units\Manager;

require_once dirname(__DIR__).'/../../../../../app/AppKernel.php';

use atoum\AtoumBundle\Test\Units;

class FileManager extends Units\Test
{
    public $logManager;
    public $manager;
    public $fichierLocalTest;
    public $fichierLocalTestVide;
    public $repLocalTest;

    public function beforeTestMethod()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();
        $this->fichierLocalTest = $container->getParameter('fichierLocalTest');
        $this->fichierLocalTestVide = $container->getParameter('fichierLocalTestVide');
        $this->repLocalTest = $container->getParameter('repLocalTest');

        $this->logManager = $container->get('backoffice_monitoring.logManager');
        $this->manager = new \mock\BackOffice\FileBundle\Manager\FileManager($this->logManager);
    }

    public function init()
    {
        touch($this->fichierLocalTest);
        chmod($this->fichierLocalTest, 0777);
        $fp = fopen($this->fichierLocalTest, 'w');
        fwrite($fp, 'hello');
        fclose($fp);
        chmod($this->fichierLocalTest, 0777);
    }

    public function testConstruct()
    {
        $this
            ->if($this->manager = new \mock\BackOffice\FileBundle\Manager\FileManager($this->logManager))
            ->object($this->manager->logManager)
            ->isEqualTo($this->logManager)
            ;
    }

    public function testAddLog()
    {
        $this
            ->if($this->manager->addLogEcriture('test', 'error'))
            ->mock($this->manager)
            ->call('addLog')->once()
            ->if($this->manager->addLogLecture('test', 'alert'))
            ->mock($this->manager)
            ->call('addLog')->exactly(2)
            ->if($this->manager->addLogDeplacement('test', 'info'))
            ->mock($this->manager)
            ->call('addLog')->exactly(3)
            ->if($this->manager->addLogDeplacement('test', 'success'))
            ->mock($this->manager)
            ->call('addLog')->exactly(4)
            ;
    }

    public function testLireFichier()
    {
        $this->init();
        $this
            ->string($this->manager->lireFichier($this->fichierLocalTest))
            ->isEqualTo(file_get_contents($this->fichierLocalTest))
            ->string($this->manager->lireFichier('cocolapin'))
            ->isEmpty()
            ->string($this->manager->lireFichier($this->fichierLocalTestVide))
            ->isEmpty()
            ;
    }

    public function testEcrireFichier()
    {
        touch($this->repLocalTest . 'toto.txt');
        chmod($this->repLocalTest . 'toto.txt', 0777);
        $this
            ->boolean($this->manager->ecrireFichier($this->repLocalTest, 'toto.txt', 'hello'))
            ->isTrue()
            ->mock($this->manager)
            ->call('addLogEcriture')
            ->once()
            ->boolean($this->manager->ecrireFichier('/titi/', 'toto.txt', 'hello'))
            ->isFalse()
            ->boolean(file_exists('/titi/' . 'toto.txt'))
            ->isFalse()
            ;

        chmod($this->repLocalTest . 'toto.txt', 0444);
        $this
            ->boolean($this->manager->ecrireFichier($this->repLocalTest, 'toto.txt', 'hello'))
            ->isFalse()
            ;
        chmod($this->repLocalTest . 'toto.txt', 0777);
    }

    public function testDeplacer()
    {
        touch($this->repLocalTest . 'tata.txt');
        $this
            ->boolean(
                $this->manager->deplacerFichier($this->repLocalTest . 'tata.txt', $this->repLocalTest . 'titi.txt')
            )
            ->isTrue() // tata devbient titi
            ->boolean(
                $this->manager->deplacerFichier(
                    $this->repLocalTest . 'titi.txt',
                    $this->repLocalTest . 'tata.txt',
                    false
                )
            )
            ->isTrue() // titi devient tata
            ->boolean(
                $this->manager->deplacerFichier($this->repLocalTest . 'tata.txt', $this->repLocalTest . 'titi.txt')
            )
            ->isTrue() // tata devbient titi
            ->boolean($this->manager->deplacerFichier($this->repLocalTest . 'titi.txt', $this->fichierLocalTest, false))
            ->isFalse() // fichier local existe deja on ne peut pas l'ecraser
            ->boolean($this->manager->deplacerFichier($this->repLocalTest . 'titi.txt', $this->fichierLocalTest, true))
            ->isTrue() // fichier local existe deja on peut l'ecraser
            ->boolean(
                $this->manager->deplacerFichier($this->repLocalTest . 'tete.txt', $this->repLocalTest . 'titi.txt')
            )
            ->isFalse() // tete.txt n'existe pas
            ->boolean($this->manager->deplacerFichier($this->fichierLocalTest, '/rep/inexistant/titi.txt'))
            ->isFalse() // le rep d'arrivee n'existe pas
            ;
    }
}
