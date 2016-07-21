<?php

namespace BackOffice\CleanBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BackOffice\ParserBundle\Manager\ParserManager;
use Symfony\Component\Console\Input\InputOption;

class MensuelCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('nettoyage:mensuel')
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
            ->nettoyerOD()
            ->nettoyerEditique()
            ->nettoyerActionsTrigger()
        ;

        $this->lm->addSuccess('Nettoyage mensuel mené avec succès', 'BackOffice > Nettoyage', 'Mensuel');
    }

    private function nettoyerLog()
    {
        $nbDay = 45;

        // copie dans table archivage_log
        $date = date('d/m/Y', strtotime("-$nbDay day"));
        $q = 'INSERT into archive_log SELECT * from log where '
            . 'TRUNC(datetime) < TO_DATE(\''.$date.'\',\'dd/mm/yyyy\')'
            ;

        $this
            ->em
            ->getConnection()
            ->prepare($q)
            ->execute()
            ;
        $this->logIt('Transfert des logs en archivage', 'data');

        $this->deleteOldData('BackOffice\MonitoringBundle\Entity\Mail', 'datetime', $nbDay);
        $this->logIt('Suppression des mails en base de + de '.$nbDay.' jours', 'data');

        $this->deleteOldData('BackOffice\MonitoringBundle\Entity\Log', 'datetime', $nbDay);
        $this->logIt('Suppression des logs en base de + de '.$nbDay.' jours', 'data');

        return $this;
    }

    private function nettoyerOD()
    {
        // compresse le dossier "module OD" en entier
        exec('tar -zcf /app/archivage/moduleOD/'.date('Y_m').'_moduleOD.tar.gz /app/exchange/moduleOD');
        $this->logIt('Compression des fichiers OD');

        // on parcourt et supprime les fichiers de plus de 30 jours
        $this->deleteOldFile('/app/exchange/moduleOD', 30);
        $this->logIt('Suppression des fichiers editique de + de 30 jours');

        // suppression des opérations diverses du plus de 1100 jours
        $this->deleteOldData('Fiscalite\ODBundle\Entity\Operation', 'dateSai', 1100);
        $this->logIt('Suppression des Opérations Diverses en base de + de 1100 jours', 'data');

        return $this;
    }

    private function nettoyerEditique()
    {
        $tps = 45;
        $unit = 'day';

        // compresse le dossier "editique" en entier
        exec('tar -zcf /app/archivage/editique/'.date('Y_m').'_editique.tar.gz /app/exchange/editique');
        $this->logIt('Compression des fichiers editique');

        // on parcourt et supprime les fichiers de plus de 45 jours
        $this->deleteOldFile('/app/exchange/editique', $tps);
        $this->logIt('Suppression des fichiers editique de + de '.$tps.' '.$unit);

        // suppression des editiques en base du plus de 45 jours
        $this->deleteOldData('Editique\MasterBundle\Entity\Editique', 'dtGeneration', $tps);
        $this->logIt('Suppression des données en base editique de + de '.$tps.' '.$unit, 'data');

        return $this;
    }

    private function nettoyerActionsTrigger()
    {
        $query = $this->em->createQueryBuilder('e');

        $query
            ->delete('BackOffice\ActionBundle\Entity\Action', 'a')
            ->where($query->expr()->lte("a.dtAction", ':time'))
            ->setParameter(
                'time',
                ParserManager::transformDate(date('d/m/Y', strtotime("-200 day"))),
                \Doctrine\DBAL\Types\Type::DATETIME
            )
            ->andWhere('a.etat like :ko')
            ->setParameter('ko', 'KO')
        ;

        $query->getQuery()->getResult();
        $this->logIt('Suppression des actions_trigger KO de + de 200 jours');


        $query = $this->em->createQueryBuilder('e');
        $query
            ->delete('BackOffice\ActionBundle\Entity\Action', 'a')
            ->where($query->expr()->lte("a.dtAction", ':time'))
            ->setParameter(
                'time',
                ParserManager::transformDate(date('d/m/Y', strtotime("-45 day"))),
                \Doctrine\DBAL\Types\Type::DATETIME
            )
            ->andWhere('a.etat like :ok')
            ->setParameter('ok', 'OK')
        ;

        $query->getQuery()->getResult();
        $this->logIt('Suppression des actions_trigger OK de + de 45 jours');

        return $this;
    }
}
