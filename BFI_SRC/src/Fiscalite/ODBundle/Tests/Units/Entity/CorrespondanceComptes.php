<?php

namespace Fiscalite\ODBundle\Tests\Units\Entity;

use Fiscalite\ODBundle\Entity\CorrespondanceComptes as BaseMapping;
use atoum\AtoumBundle\Test\Units;

class CorrespondanceComptes extends Units\Test
{
    /**
     * Initialise
     *      l'objet releveManager à tester
     *      les ecriture et lecture manager qui vont avec
     */
    public function beforeTestMethod()
    {
        $this->mapping = new BaseMapping();
    }
    
    // Teste que les getters retournent null si rien n'est initialisé
    public function testNull()
    {
        $this
            ->variable($this->mapping->getNumCompteInterne())
                ->isNull()
            ->variable($this->mapping->getNumCompteExterne())
                ->isNull()
        ;
    }
    
    // Teste que les setters fonctionnent et que les getters retournent la valeur donnée
    public function testSettersAndGetters()
    {
        $this
            ->object($this->mapping->setNumCompteInterne('ABCDE'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\CorrespondanceComptes')
            ->string($this->mapping->getNumCompteInterne())
                ->isNotNull()
                ->isEqualTo('ABCDE')
            ->object($this->mapping->setNumCompteExterne('FGHIJK'))
                ->isInstanceOf('Fiscalite\ODBundle\Entity\CorrespondanceComptes')
            ->string($this->mapping->getNumCompteExterne())
                ->isNotNull()
                ->isEqualTo('FGHIJK')
        ;
    }
    
    // Teste les autres méthodes (non get ou set)
    public function testOthersMethods()
    {
        $this
            ->variable($this->mapping->__toString())
                ->isNull()
            ->if($this->mapping->setNumCompteInterne('ABCDE'))
            ->string($this->mapping->__toString())
                ->isNotNull()
                ->isEqualTo('ABCDE')
        ;
    }
}
