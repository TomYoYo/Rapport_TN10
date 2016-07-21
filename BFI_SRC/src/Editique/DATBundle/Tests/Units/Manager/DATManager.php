<?php

namespace Editique\DATBundle\Tests\Units\Manager;

use \Editique\MasterBundle\Tests\Units\Manager\EditiqueManager;
use Editique\DATBundle\Entity\Dat;

class DATManager extends EditiqueManager
{
    public $idDatParticulierFixe = 2;
    public $idDatParticulierProg = 12;
    public $idDatProfessionnelFixe = 4;

    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     *
     */
    public function beforeTestMethod()
    {
        parent::beforeTestMethod();

        $this->manager = new \mock\Editique\DATBundle\Manager\DATManager();
        $this->manager->setEcritureManager($this->ecritureManager);
        $this->manager->setLectureManager($this->lectureManager);
        $this->manager->setEntityManager($this->entityManager);
        $this->manager->setLogManager($this->logManager);
        $this->manager->setFileManager($this->fileManager);
        $this->manager->setWindowsFTPManager($this->windowsFTPManager);
        $this->manager->setPDFManager($this->pdfManager);
        $this->manager->setTplManager($this->tplManager);

        $this->manager->oDat = new Dat();
    }

    public function testGetODat()
    {
        $this
            ->object($this->manager->getODat())
            ->isInstanceOf('\Editique\DATBundle\Entity\DAT')
            ;
    }

    public function testEcrireSortieParticulierFixe()
    {
        $this
            ->string($fileName = $this->manager->ecrireSortie($this->dirSortie, $this->idDatParticulierFixe))
            ->boolean(file_exists($this->dirSortie . $fileName))
            ->isTrue()
            ;
    }

    public function testEcrireSortieParticulierProg()
    {
        $this
            ->string($fileName = $this->manager->ecrireSortie($this->dirSortie, $this->idDatParticulierProg))
            ->boolean(file_exists($this->dirSortie . $fileName))
            ->isTrue()
            ;
    }

    public function testEcrireSortieProfessionnelFixe()
    {
        $this
            ->string($fileName = $this->manager->ecrireSortie($this->dirSortie, $this->idDatProfessionnelFixe))
            ->boolean(file_exists($this->dirSortie . $fileName))
            ->isTrue()
            ;
    }

    public function testGetFileName()
    {
        $this
            ->if($this->manager->oDat->setIdClient('123'))
            ->and($this->manager->oDat->setNumCompteSupport('456'))
            ->string($this->manager->getFileName('txt'))
            ->contains(
                "Avis_" .
                $this->manager->oDat->getIdClient() .
                "_DAT_" .
                $this->manager->oDat->getNumCompteSupport() .
                "_"
            )
        ;
    }
}
