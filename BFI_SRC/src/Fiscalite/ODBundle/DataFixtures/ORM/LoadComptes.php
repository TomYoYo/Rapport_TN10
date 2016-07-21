<?php

namespace Fiscalite\ODBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fiscalite\ODBundle\Entity\CorrespondanceComptes;

class LoadComptes implements FixtureInterface, ContainerAwareInterface
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
        
        $array = array(
            '361620EUR000' => '365615',
            '365620EUR000' => '365616',
            '365625EUR000' => '365627',
            '365626EUR000' => '365617',
            '365627EUR000' => '365622',
            '365627EUR000' => '365624',
            '365627EUR000' => '365623',
            '365628EUR000' => '365614',
            '365629EUR000' => '365626',
            '611000EUR000NR' => '611001',
            '611003EUR000NR' => '611006',
            '611010EUR000NR' => '611007',
            '612100EUR000NR' => '612107',
            '612900EUR000NR' => '612901',
            '612910EUR000NR' => '612105',
            '612910EUR000NR' => '612104',
            '612910EUR000NR' => '612106',
            '614120EUR000NR' => '614004',
            '614130EUR000NR' => '614003',
            '709400EUR000NR' => '709410'
        );
        
        foreach ($array as $compteInterne => $compteExterne) {
            $compte = new CorrespondanceComptes();
            
            $compte
                ->setNumCompteInterne($compteInterne)
                ->setNumCompteExterne($compteExterne);

            $manager->persist($compte);
        }
        
        $manager->flush();
        
        $lm->addAlert(
            'Génération effectuée. '.count($array).' enregistrements ajoutés.',
            'OD > Module OD',
            'Génération de données persistantes.'
        );
    }
}
