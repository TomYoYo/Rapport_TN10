<?php
/**
 * Created by PhpStorm.
 * User: t.pueyo
 * Date: 28/04/2016
 * Time: 09:43
 */

namespace BackOffice\CustomerBundle\Command;

use BackOffice\ConnexionBundle\Manager\SabSFTPManager;
use BackOffice\MonitoringBundle\Manager\LogManager;
use mageekguy\atoum\asserters\string;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monitoring:customer:upload')
            ->setDescription('Depot du fichier sur le serveu');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var SabSFTPManager $SFTPManager
         */
        $SFTPManager = $this->getContainer()->get('back_office_connexion.SabSFTP');
        /**
         * @var LogManager $logManager
         */
        $logManager = $this->getContainer()->get('backoffice_monitoring.logManager');
        if(file_exists($this->getContainer()->getParameter('dirSortieFIDINF').date('ymd').'/ZXPROBQE.dat'))
        {
            $SFTPManager->upload($this->getContainer()->getParameter('dirSortieFIDINF').date('ymd').'/ZXPROBQE.dat',$this->getContainer()->getParameter('sabCore.DAN.f_interface').'/entree/ZXPROBQE.dat');
            $logManager->addInfo("Fichier client téléchargé","BackOffice > Intégration des clients externes","Import données");
        }
        else{
            $logManager->addInfo("Pas de fichier de données client à télécharger","BackOffice > Intégration des clients externes","Import données");
            if (!file_exists($this->getContainer()->getParameter('dirSortieFIDINF').date('ymd').'/')) {
                mkdir($this->getContainer()->getParameter('dirSortieFIDINF').date('ymd').'/');
            }
            $fp = fopen($this->getContainer()->getParameter('dirSortieFIDINF').date('ymd').'/ZXPROBQE.dat','a+');
            fclose($fp);
            $SFTPManager->upload($this->getContainer()->getParameter('dirSortieFIDINF').date('ymd').'/ZXPROBQE.dat',$this->getContainer()->getParameter('sabCore.DAN.f_interface').'/entree/ZXPROBQE.dat');

        }
       // file_put_contents('/app/exchange/divers/out/'.date('ymd').'/ZXPROBQE.dat', '');
        $SFTPManager->logout();
    }
}
