<?php

namespace Editique\MasterBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Editique\MasterBundle\Entity\CorrespondanceReleve;

class LoadCorrespondanceReleve implements FixtureInterface, ContainerAwareInterface
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
        
        $mapping = new CorrespondanceReleve();
        $mapping
            ->setLibelle('AUTRES SOMMES DUES SAISIES ARRET')
            ->setType('COPAR');

        $manager->persist($mapping);
        
        $mapping2 = new CorrespondanceReleve();
        $mapping2
            ->setLibelle('BANQUE FIDUCIAL')
            ->setType('COPAR');

        $manager->persist($mapping2);
        
        $mapping3 = new CorrespondanceReleve();
        $mapping3
            ->setLibelle('COMPTE COURANT')
            ->setType('COPAR');

        $manager->persist($mapping3);
        
        $mapping4 = new CorrespondanceReleve();
        $mapping4
            ->setLibelle('COMPTE COURANT ASSOCIATION')
            ->setType('COPAR');

        $manager->persist($mapping4);
        
        $mapping5 = new CorrespondanceReleve();
        $mapping5
            ->setLibelle('COMPTE COURANT FIRME')
            ->setType('COPAR');

        $manager->persist($mapping5);
        
        $mapping6 = new CorrespondanceReleve();
        $mapping6
            ->setLibelle('COMPTE BANQUE LORO')
            ->setType('COPRO');

        $manager->persist($mapping6);
        
        $mapping7 = new CorrespondanceReleve();
        $mapping7
            ->setLibelle('COMPTE BANQUE NOSTRO')
            ->setType('COPRO');

        $manager->persist($mapping7);
        
        $mapping8 = new CorrespondanceReleve();
        $mapping8
            ->setLibelle('COMPTE COURANT ENTREPRISE')
            ->setType('COPRO');

        $manager->persist($mapping8);
        
        $mapping9 = new CorrespondanceReleve();
        $mapping9
            ->setLibelle('COMPTE COURANT PROFESSIONNEL')
            ->setType('COPRO');

        $manager->persist($mapping9);
        
        $mapping10 = new CorrespondanceReleve();
        $mapping10
            ->setLibelle('COMPTE LIVRET FIDUCIAL BOOSTE')
            ->setType('EPARG');

        $manager->persist($mapping10);
        
        $mapping11 = new CorrespondanceReleve();
        $mapping11
            ->setLibelle('COMPTE SUR LIVRET A')
            ->setType('EPARG');

        $manager->persist($mapping11);
        
        $mapping12 = new CorrespondanceReleve();
        $mapping12
            ->setLibelle('COMPTE SUR LIVRET A ASSOCIATION')
            ->setType('EPARG');

        $manager->persist($mapping12);
        
        $mapping13 = new CorrespondanceReleve();
        $mapping13
            ->setLibelle('COMPTE SUR LIVRET FIDUCIAL')
            ->setType('EPARG');

        $manager->persist($mapping13);
        
        $mapping14 = new CorrespondanceReleve();
        $mapping14
            ->setLibelle('LIVRET DEV DURABLE (EX CODEVI)')
            ->setType('EPARG');

        $manager->persist($mapping14);
        
        $manager->flush();
        
        $lm->addAlert(
            'Génération effectuée. 14 enregistrements ajoutés.',
            'Editique > Master',
            'Génération de données persistantes.'
        );
    }
}
