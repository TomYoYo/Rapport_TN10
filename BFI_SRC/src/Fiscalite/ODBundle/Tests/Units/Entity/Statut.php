<?php

namespace Fiscalite\ODBundle\Tests\Units\Entity;

use Fiscalite\ODBundle\Entity\Statut as BaseStatut;
use atoum\AtoumBundle\Test\Units;

class Statut extends Units\Test
{
    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     */
    public function beforeTestMethod()
    {
        $this->statut = new BaseStatut();
    }
    
    // Teste que les getters retournent null si rien n'est initialisé
    public function testNull()
    {
        $this
            ->variable($this->statut->getIdStatut())
                ->isNull()
            ->variable($this->statut->getLibelleStatut())
                ->isNull()
            ->object($this->statut->getOperations())
                ->isNotNull()
                ->hasSize(0)
                ->isInstanceOf('Doctrine\Common\Collections\ArrayCollection')
        ;
    }
    
    // Teste que les setters fonctionnent et que les getters retournent la valeur donnée
    public function testSettersAndGetters()
    {
        $this
            ->object($this->statut->setIdStatut('NEW'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Statut')
            ->string($this->statut->getIdStatut())
                ->isNotNull()
                ->isEqualTo('NEW')
            ->object($this->statut->setLibelleStatut('Nouveau Statut'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Statut')
            ->string($this->statut->getLibelleStatut())
                ->isNotNull()
                ->isEqualTo('Nouveau Statut')
        ;
    }
    
    // Teste les autres méthodes (non get ou set)
    public function testOthersMethods()
    {
        $this
            ->variable($this->statut->__toString())
                ->isNull()
            ->if($this->statut->setLibelleStatut('Test Statut'))
            ->string($this->statut->__toString())
                ->isNotNull()
                ->isEqualTo('Test Statut')
            ->if($this->statut->__construct())
            ->object($this->statut->getOperations())
                ->isNotNull()
                ->hasSize(0)
                ->isInstanceOf('Doctrine\Common\Collections\ArrayCollection')
        ;
    }
}
