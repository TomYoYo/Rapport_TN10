<?php

namespace Editique\MasterBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Editique\MasterBundle\Entity\Editique;

class LoadEditique implements FixtureInterface, ContainerAwareInterface
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
        
        $exemple = new Editique();
        $exemple
            ->setIdClient('0000004')
            ->setTypeDoc('rib')
            ->setDtGeneration(new \DateTime())
            ->setFilePath('/data/exchange/editique/out/Avis_0000004_RIB_00000400009_20140220.pdf')
            ->setIdUtilisateur(0)
            ->setNumCpt('00000400009');

        $manager->persist($exemple);
        $manager->flush();
        
        $lm->addAlert(
            'Génération effectuée. 2 enregistrements ajoutés.',
            'Editique > Master',
            'Génération de données persistantes.'
        );
    }
}
