<?php

namespace Fiscalite\ODBundle\Tests\Units\Entity;

use Fiscalite\ODBundle\Entity\Operation as BaseOperation;
use atoum\AtoumBundle\Test\Units;

class Operation extends Units\Test
{
    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     */
    public function beforeTestMethod()
    {
        $this->operation = new BaseOperation();
    }
    
    // Teste que les getters retournent null si rien n'est initialisé
    public function testNull()
    {
        $this
            ->variable($this->operation->getNumPiece())
                ->isNull()
            ->variable($this->operation->getCodeOpe())
                ->isNull()
            ->variable($this->operation->getCodeEve())
                ->isNull()
            ->variable($this->operation->getTiers())
                ->isNull()
            ->variable($this->operation->getDateCpt())
                ->isNull()
            ->variable($this->operation->getDateSai())
                ->isNull()
            ->string($this->operation->getDevise())
                ->isNotNull()
                ->isEqualTo('EUR')
            ->variable($this->operation->getDateVal())
                ->isNull()
            ->variable($this->operation->getRefLet())
                ->isNull()
            ->variable($this->operation->getRefAnalytique())
                ->isNull()
            ->variable($this->operation->getDateStatut())
                ->isNull()
            ->variable($this->operation->getDateStatutPrec())
                ->isNull()
            ->string($this->operation->getIsDeleted())
                ->isEqualTo('0')
            ->string($this->operation->getIsComplementaryDay())
                ->isEqualTo('0')
            ->variable($this->operation->getStatut())
                ->isNull()
            ->variable($this->operation->getStatutPrec())
                ->isNull()
            ->variable($this->operation->getProfil())
                ->isNull()
            ->object($this->operation->getActions())
                ->isNotNull()
                ->hasSize(0)
                ->isInstanceOf('Doctrine\Common\Collections\ArrayCollection')
            ->object($this->operation->getMouvements())
                ->isNotNull()
                ->hasSize(0)
                ->isInstanceOf('Doctrine\Common\Collections\ArrayCollection')
        ;
    }
    
    // Teste que les setters fonctionnent et que les getters retournent la valeur donnée
    public function testSettersAndGetters()
    {
        $this
            ->object($this->operation->setCodeOpe('OPE'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->string($this->operation->getCodeOpe())
                ->isNotNull()
                ->isEqualTo('OPE')
            ->object($this->operation->setCodeEve('EVE'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->string($this->operation->getCodeEve())
                ->isNotNull()
                ->isEqualTo('EVE')
            ->object($this->operation->setTiers('0000124'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->string($this->operation->getTiers())
                ->isNotNull()
                ->isEqualTo('0000124')
            ->object($this->operation->setDateCpt(new \Datetime('2000-12-21')))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->dateTime($this->operation->getDateCpt())
                ->isNotNull()
                ->hasDate('2000', '12', '21')
            ->object($this->operation->setDateSai(new \Datetime('2002-10-25')))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->dateTime($this->operation->getDateSai())
                ->isNotNull()
                ->hasDate('2002', '10', '25')
            ->object($this->operation->setDevise('YEN'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->string($this->operation->getDevise())
                ->isNotNull()
                ->isEqualTo('YEN')
            ->object($this->operation->setDateVal(new \Datetime('2005-08-26')))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->dateTime($this->operation->getDateVal())
                ->isNotNull()
                ->hasDate('2005', '08', '26')
            ->object($this->operation->setRefLet('54741D'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->string($this->operation->getRefLet())
                ->isNotNull()
                ->isEqualTo('54741D')
            ->object($this->operation->setRefAnalytique('001'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->string($this->operation->getRefAnalytique())
                ->isNotNull()
                ->isEqualTo('001')
            ->object($this->operation->setDateStatut(new \Datetime('2009-02-28')))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->dateTime($this->operation->getDateStatut())
                ->isNotNull()
                ->hasDate('2009', '02', '28')
            ->object($this->operation->setDateStatutPrec(new \Datetime('2013-01-30')))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->dateTime($this->operation->getDateStatutPrec())
                ->isNotNull()
                ->hasDate('2013', '01', '30')
            ->object($this->operation->setIsDeleted(true))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->boolean($this->operation->getIsDeleted())
                ->isNotNull()
                ->isTrue()
            ->object($this->operation->setIsComplementaryDay(true))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->boolean($this->operation->getIsComplementaryDay())
                ->isNotNull()
                ->isTrue()
            ->object($this->operation->setStatut(new \Fiscalite\ODBundle\Entity\Statut()))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->object($this->operation->getStatut())
                ->isNotNull()
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Statut')
            ->object($this->operation->setStatutPrec(new \Fiscalite\ODBundle\Entity\Statut()))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->object($this->operation->getStatutPrec())
                ->isNotNull()
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Statut')
            ->object($this->operation->setProfil(new \BackOffice\UserBundle\Entity\Profil()))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
            ->object($this->operation->getprofil())
                ->isNotNull()
                ->isInstanceOf('BackOffice\UserBundle\Entity\Profil')
        ;
    }
    
    // Teste les autres méthodes (non get ou set)
    public function testOthersMethods()
    {
        $this
            ->variable($this->operation->__toString())
                ->isNull()
            ->if($this->operation->__construct())
            ->object($this->operation->getActions())
                ->isNotNull()
                ->hasSize(0)
                ->isInstanceOf('Doctrine\Common\Collections\ArrayCollection')
            ->object($this->operation->getMouvements())
                ->isNotNull()
                ->hasSize(0)
                ->isInstanceOf('Doctrine\Common\Collections\ArrayCollection')
        ;
    }
}
