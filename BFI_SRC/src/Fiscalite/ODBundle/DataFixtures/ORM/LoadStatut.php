<?php

namespace Fiscalite\ODBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fiscalite\ODBundle\Entity\Statut;

class LoadStatut implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Log Manager
        $lm = $this->container->get('backoffice_monitoring.logManager');
        
        // Statut "Pré-saisie"
        $statutSaisie = new Statut();
        
        $statutSaisie
            ->setIdStatut('SAI')
            ->setLibelleStatut('Pré-saisie');
        
        $manager->persist($statutSaisie);
        
        // Statut "Enregistrée"
        $statutEnregistree = new Statut();
        
        $statutEnregistree
            ->setIdStatut('ENR')
            ->setLibelleStatut('Enregistrée');
        
        $manager->persist($statutEnregistree);
        
        // Statut "Envoyée"
        $statutEnvoyee = new Statut();
        
        $statutEnvoyee
            ->setIdStatut('ENV')
            ->setLibelleStatut('Envoyée');
        
        $manager->persist($statutEnvoyee);
        
        $manager->flush();
        
        $lm->addAlert(
            'Génération effectuée. 3 enregistrements ajoutés.',
            'OD > Module OD',
            'Génération de données persistantes.'
        );
    }
}
