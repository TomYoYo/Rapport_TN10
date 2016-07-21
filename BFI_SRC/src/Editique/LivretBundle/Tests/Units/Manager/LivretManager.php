<?php

namespace Editique\LivretBundle\Tests\Units\Manager;

use \Editique\MasterBundle\Tests\Units\Manager\EditiqueManager;
use Editique\LivretBundle\Entity\Livret;

class LivretManager extends EditiqueManager
{
    public $idLVAFixe = 38;
    public $idLDDFixe = 46;
    public $idCSLFixe = 22;


    public function beforeTestMethod()
    {
        parent::beforeTestMethod();

        $this->manager = new \mock\Editique\LivretBundle\Manager\LivretManager();
        $this->manager->setEcritureManager($this->ecritureManager);
        $this->manager->setLectureManager($this->lectureManager);
        $this->manager->setEntityManager($this->entityManager);
        $this->manager->setLogManager($this->logManager);
        $this->manager->setFileManager($this->fileManager);
        //$this->manager->setWindowsFTPManager($this->windowsFTPManager);
        $this->manager->setPDFManager($this->pdfManager);
        $this->manager->setTplManager($this->tplManager);

        $this->manager->oLivret = new Livret();
    }

    public function testGetOLivret()
    {
        $this
            ->object($this->manager->getOLivret())
            ->isInstanceOf('Editique\LivretBundle\Entity\Livret')
            ;
    }

    public function testEcrireSortieLVAFixe()
    {
        $this
            ->string($fileName = $this->manager->ecrireSortie($this->dirSortie, $this->idLVAFixe))
            ->boolean(file_exists($this->dirSortie . $fileName))
            ->isTrue()
            ;
    }

    public function testEcrireSortieLDDFixe()
    {
        $this
            ->string($fileName = $this->manager->ecrireSortie($this->dirSortie, $this->idLDDFixe))
            ->boolean(file_exists($this->dirSortie . $fileName))
            ->isTrue()
            ;
    }

    public function testEcrireSortieCSLFixe()
    {
        $this
            ->string($fileName = $this->manager->ecrireSortie($this->dirSortie, $this->idCSLFixe))
            ->boolean(file_exists($this->dirSortie . $fileName))
            ->isTrue()
            ;
    }

    public function testGetFileName()
    {
        $this
            ->if($this->manager->oLivret->setIdClient('123'))
            ->and($this->manager->oLivret->setTypeCptERE('LDD'))
            ->and($this->manager->oLivret->setNumCptERE('456'))
            ->string($this->manager->getFileName('txt'))
            ->contains(
                "Avis_" .
                trim($this->manager->oLivret->getIdClient()) .
                "_" .
                trim($this->manager->oLivret->getTypeCptERE()) .
                "_" .
                trim($this->manager->oLivret->getNumCptERE()) .
                "_"
            )
        ;
    }

    public function testGetIdClient()
    {
        $this->manager->oLivret->setNumCptERE($this->idLDDFixe);
        $this
            ->if($this->manager->ecrireSortie($this->dirSortie, $this->idLDDFixe))
            ->string($this->manager->getIdClient())
            ->isEqualTo('0000086')
            ->boolean($this->manager->getFatalError())
            ->isFalse()
        ;
    }
}
