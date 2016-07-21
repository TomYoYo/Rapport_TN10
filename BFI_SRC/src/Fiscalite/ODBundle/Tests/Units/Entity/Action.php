<?php

namespace Fiscalite\ODBundle\Tests\Units\Entity;

use Fiscalite\ODBundle\Entity\Action as BaseAction;
use atoum\AtoumBundle\Test\Units;

class Action extends Units\Test
{
    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     */
    public function beforeTestMethod()
    {
        $this->action = new BaseAction();
    }
    
    // Teste que les getters retournent null si rien n'est initialisé
    public function testNull()
    {
        $this
            ->variable($this->action->getIdAction())
                ->isNull()
            ->variable($this->action->getDateAction())
                ->isNull()
            ->variable($this->action->getLibelleAction())
                ->isNull()
            ->variable($this->action->getProfil())
                ->isNull()
            ->variable($this->action->getOperation())
                ->isNull()
        ;
    }
    
    // Teste que les setters fonctionnent et que les getters retournent la valeur donnée
    public function testSettersAndGetters()
    {
        $this
            ->object($this->action->setDateAction())
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Action')
            ->object($this->action->getDateAction())
                ->isNotNull()
                ->isInstanceOf('\Datetime')
            ->object($this->action->setLibelleAction('Test'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Action')
            ->string($this->action->getLibelleAction())
                ->isNotNull()
                ->isEqualTo('Test')
            ->object($this->action->setProfil(new \mock\BackOffice\UserBundle\Entity\Profil()))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Action')
            ->object($this->action->getProfil())
                ->isNotNull()
                ->isInstanceOf('BackOffice\UserBundle\Entity\Profil')
            ->object($this->action->setOperation(new \mock\Fiscalite\ODBundle\Entity\Operation()))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Action')
            ->object($this->action->getOperation())
                ->isNotNull()
                ->isInstanceOf('Fiscalite\ODBundle\Entity\Operation')
        ;
    }
}
