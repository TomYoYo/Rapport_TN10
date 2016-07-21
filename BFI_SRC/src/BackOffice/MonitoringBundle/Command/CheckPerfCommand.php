<?php

namespace BackOffice\MonitoringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use BackOffice\MonitoringBundle\Entity\Performance;

class CheckPerfCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('monitoring:check:perf')
            ->setDescription('Mesure des métriques de performanace de SAB et de sa base.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $perfManager = $this->getContainer()->get('back_office_connexion.performanceManager');

        list($nbProduitVendu, $exeTimeBdd) = $perfManager->getNbProduitVendu();

        $nbClient = $perfManager->getNbClient();
        $nbOpe = $perfManager->getNbOperation();

        $dureeJour = $perfManager->getPerfJOUR();
        $dureeJourBd = $perfManager->getPerfJOURBD();
        $dureeBdd = $exeTimeBdd;

        $newPerf = new Performance();
        $newPerf
            ->setDatetime(new \DateTime())
            ->setNbClient($nbClient)
            ->setNbOperation($nbOpe)
            ->setNbProduit($nbProduitVendu)
            ->setDureeJour($dureeJour)
            ->setDureeJourBD($dureeJourBd)
            ->setDureeBdd($dureeBdd)
            ;
        $em->persist($newPerf);

        $em->flush();

    }
}
