<?php

namespace BackOffice\CleanBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class AnnuelCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('nettoyage:annuel')
            ->setDescription('Nettoyage et archivage mensuels des données BFI.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Si demandée, la tache discutera')
            ->addOption('nolog', null, InputOption::VALUE_NONE, 'Si demandée, la tache n\'écrira pas de log')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lm = $this->getContainer()->get('backoffice_monitoring.logManager');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->debug = $input->getOption('debug');
        $this->nolog = $input->getOption('nolog');

        $this
            ->nettoyerLog()
            ->nettoyerSIRH()
            ->nettoyerArchiveFichier()
        ;

        $this->lm->addSuccess('Nettoyage annuel mené avec succès', 'BackOffice > Nettoyage', 'Annuel');
    }

    private function nettoyerLog()
    {
        $nbDay = 370;

        $this->deleteOldData('BackOffice\MonitoringBundle\Entity\ArchiveLog', 'datetime', $nbDay);
        $this->logIt('Suppression des logs archivés en base de + de '.$nbDay.' jours', 'data');

        return $this;
    }

    private function nettoyerSIRH()
    {
        // compresse le dossier "SIRH_compta" en entier
        exec('tar -zcf /app/archivage/SIRH_compta/'.date('Y_m').'_SIRH_compta.tar.gz /app/exchange/SIRH_compta');
        $this->logIt('Compression des fichiers SIRH_compta');

        // on parcourt et supprime les fichiers de plus de 1 an
        $this->deleteOldFile('/app/exchange/SIRH_compta', 370);
        $this->logIt('Suppression des fichiers SIRH_compta de + de 370 jours');

        return $this;
    }

    private function nettoyerArchiveFichier()
    {
        // suppression des archives de plus de 10 ans
        $this->deleteOldFile('/app/archivage', 10, 'year');
        $this->logIt('Suppression des archives de + de 10 ans');

        return $this;

    }
}
