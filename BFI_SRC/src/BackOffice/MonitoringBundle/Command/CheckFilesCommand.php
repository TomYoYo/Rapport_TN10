<?php

namespace BackOffice\MonitoringBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckFilesCommand extends ContainerAwareCommand
{
    private $type = null;

    protected function configure()
    {
        $this
            ->setName('monitoring:check:files')
            ->setDescription('Vérifier la présence du fichier SIT et son contenu.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cm = $this->getContainer()->get('back_office_connexion.SabSFTP');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $directory = $this->getContainer()->getParameter('sabCore.dirSortie2');
        $dirCASA = $this->getContainer()->getParameter('dirCASA');
        
        $this->sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');

        while (true) {
            $content = $cm->getContent($directory.'ZXSITRET0.dat');

            if ($content == "INEXISTANT") {
                $this->addLogSIT('alert');
            } elseif ($content) {
                // Initialisation des variables pour le fichier
                $generalType  = 'info';
                $counter      = 0;
                $counter2     = 0;
                $codesSurv    = $em->getRepository('BackOfficeMonitoringBundle:CodeOperation')
                                    ->findByTypePresence(true);
                $codesExcl    = $em->getRepository('BackOfficeMonitoringBundle:CodeOperation')
                                        ->findByTypePresence(false);
                
                $codesSurvArr = array();
                $codesExclArr = array();
                
                foreach ($codesSurv as $codeSurv) {
                    $codesSurvArr[] = $codeSurv->getCode();
                }
                
                foreach ($codesExcl as $codeExcl) {
                    $codesExclArr[] = $codeExcl->getCode();
                }
                
                foreach ($content as $line) {
                    if (substr($line, 6, 2) == '02') {
                        $code = substr($line, 8, 3);
                        
                        /*if (in_array($code, $codesSurvArr)) {
                            $generalType = 'error';
                            $counter++;
                        }*/
                        if (!in_array($code, $codesExclArr)) {
                            $generalType = 'error2';
                            $counter2++;
                        }
                    }
                }

                if ($this->type != $generalType) {
                    if ($generalType == 'error') {
                        // Récupération du fichier
                        $locpath = $dirCASA . 'ZXSITRET0_'.date('Ymd_His').'.dat';
                        $rempath = $directory . 'ZXSITRET0.dat';
                        $this->sabManager->download($rempath, $locpath);
                        
                        // Ajout d'un log
                        $this->addLogSIT('error', $counter);
                    } elseif ($generalType == 'error2') {
                        // Récupération du fichier
                        $locpath = $dirCASA . 'ZXSITRET0_'.date('Ymd_His').'.dat';
                        $rempath = $directory . 'ZXSITRET0.dat';
                        $this->sabManager->download($rempath, $locpath);
                        
                        // Ajout d'un log
                        $this->addLogSIT('error2', $counter2);
                    } else {
                        $this->addLogSIT();
                    }
                }
            }

            sleep(90);
        }
    }

    private function addLogSIT($type = 'info', $counter = null)
    {
        if ($this->type != $type) {
            $lm = $this->getContainer()->get('backoffice_monitoring.logManager');
            $this->type = $type;

            switch ($type) {
                case 'error':
                    $lm->addError(
                        'Un code d\'opération non traité a été trouvé dans le fichier SIT ('.$counter.' fois).',
                        'BackOffice > Monitoring',
                        'Vérification du fichier SIT',
                        'SIT'
                    );
                    break;
                case 'error2':
                    $lm->addError(
                        'Un code d\'opération non surveillé a été trouvé dans le fichier SIT ('.$counter.' fois).',
                        'BackOffice > Monitoring',
                        'Vérification du fichier SIT',
                        'SIT'
                    );
                    break;
                case 'info':
                    $lm->addInfo(
                        'Seuls les codes surveillés ont été trouvés dans le fichier SIT.',
                        'BackOffice > Monitoring',
                        'Vérification du fichier SIT'
                    );
                    break;
                case 'alert':
                    $lm->addAlert(
                        'Le fichier SIT n\'est pas présent.',
                        'BackOffice > Monitoring',
                        'Vérification du fichier SIT'
                    );
                    break;
            }
        }
    }
}
