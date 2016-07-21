<?php

namespace Fiscalite\ODBundle\Tests\Units\Entity;

use Fiscalite\ODBundle\Entity\Mouvement as BaseMouvement;
use atoum\AtoumBundle\Test\Units;

class Mouvement extends Units\Test
{
    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     */
    public function beforeTestMethod()
    {
        $this->mvmt = new BaseMouvement();
    }
    
    // Teste que les getters retournent null si rien n'est initialisé
    public function testNull()
    {
        $this
            ->variable($this->mvmt->getIdOpe())
                ->isNull()
            ->variable($this->mvmt->getNumMvmt())
                ->isNull()
            ->variable($this->mvmt->getCompte())
                ->isNull()
            ->variable($this->mvmt->getMontant())
                ->isNull()
            ->variable($this->mvmt->getCodeBudget())
                ->isNull()
            ->variable($this->mvmt->getLibelle())
                ->isNull()
            ->variable($this->mvmt->getOperation())
                ->isNull()
        ;
    }
    
    // Teste que les setters fonctionnent et que les getters retournent la valeur donnée
    public function testSettersAndGetters()
    {
        $this
            ->object($this->mvmt->setIdOpe('ABC214D'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->string($this->mvmt->getIdOpe())
                ->isNotNull()
                ->isEqualTo('ABC214D')
            ->object($this->mvmt->setNumMvmt('10'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->string($this->mvmt->getNumMvmt())
                ->isNotNull()
                ->isEqualTo('10')
            ->object($this->mvmt->setCompte('15471D'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->string($this->mvmt->getCompte())
                ->isNotNull()
                ->isEqualTo('15471D')
            ->object($this->mvmt->setMontant('150.50'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->string($this->mvmt->getMontant())
                ->isNotNull()
                ->isEqualTo('150.50')
            ->object($this->mvmt->setCodeBudget('5871'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->string($this->mvmt->getCodeBudget())
                ->isNotNull()
                ->isEqualTo('5871')
            ->object($this->mvmt->setLibelle('Test'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->string($this->mvmt->getLibelle())
                ->isNotNull()
                ->isEqualTo('Test')
            ->object($this->mvmt->setOperation(new \mock\Fiscalite\ODBundle\Entity\Operation()))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Mouvement')
            ->object($this->mvmt->getOperation())
                ->isNotNull()
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
        ;
    }
}
