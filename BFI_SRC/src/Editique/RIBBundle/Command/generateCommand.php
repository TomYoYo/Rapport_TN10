<?php

namespace Editique\RIBBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BackOffice\MonitoringBundle\Entity\Log;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:generate:rib')
            ->setDescription('Génére les txt et pdf après être aller chercher le fichiers source sur sabCore.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init Managers
        $ribManager = $this->getContainer()->get('editique.ribManager');
        $logManager = $this->getContainer()->get('backoffice_monitoring.logManager');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');

        // JD : c'est pas jojo ce que j'ai fait, il faudrait faire cela autrement... singleton ?
        $logManager->setProcessMode(true);
        $ribManager->logManager->setProcessMode(true);
        $ribManager->windowsFTPManager->logManager->setProcessMode(true);
        $fileManager->logManager->setProcessMode(true);
        $sabManager->logManager->setProcessMode(true);

        // Init directories
        $dirEntree = $this->getContainer()->getParameter('dirEntreeEditique');
        $dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        $dirError = $this->getContainer()->getParameter('dirErrorEditique');
        $dirTreated = $this->getContainer()->getParameter('dirTreatedEditique');
        $dirRemote = $this->getContainer()->getParameter('sabCore.dirPathXML');
        $subDir = $this->getContainer()->getParameter('sabCore.subDirXML');
        $dirSortieSab = $this->getContainer()->getParameter('sabCore.dirSortie');

        //Reinit RIB
        $ribManager->initEditique();
        $logManager->setFatalErrorMet(false);
        $ribManager->windowsFTPManager->logManager->setFatalErrorMet(false);
        $fileManager->logManager->setFatalErrorMet(false);
        $sabManager->logManager->setFatalErrorMet(false);
        
        // Init variables
        $count = 0;
        $countOk = 0;
        $countKo = 0;
        $regex = "/^ribeditique_\d{4}-\d{2}-\d{2}\ \d{2}-\d{2}-\d{2}-\d{3}.xml$/";

        // connexion sabCore
        $filesInDirectory = $sabManager->fichiersSousDossiersMasques($dirRemote, $subDir, $regex);
        
        foreach ($filesInDirectory as $pathDirectory => $files) {
            foreach ($files as $file) {
                $count++;

                // On copie tout les fichiers dans in sur bfi server
                $locpath = $dirEntree . $file;
                $rempath = $pathDirectory . $file;
                $sabManager->download($rempath, $locpath);

                // On lit le fichier
                $ribManager->readFile($locpath);

                // On écrit la sortie ou on génère une erreur (non fatal)
                if ($ribManager->getFatalError() == false) {
                    // Ecriture
                    $ribManager->ecrireSortie($dirSortie);

                    // Récupération path et size fichier TXT
                    $txt = $ribManager->getFileName('txt');
                    $sizeTXT = filesize($dirSortie . $txt);

                    // Récupération path et size fichier PDF
                    $pdf = $ribManager->getFileName('pdf');
                    $sizePDF = filesize($dirSortie . $pdf);

                    // Déplacement de in vers treated ou error
                    if ($sizeTXT > 0 && $sizePDF > 0) {
                        $countOk++;
                        // on balance le pdf sur sabcore en renommant
                        $newName = date('ymd') . 'CR' . $ribManager->getAccountNumber() . '.pdf';
                        $sabManager->upload($dirSortie . $pdf, $dirSortieSab . $newName);
                        // Déplacement dans treated
                        $fileManager->deplacerFichier($locpath, $dirTreated . $file);
                        // On supprime le fichier de sabcore
                        $sabManager->delete($rempath);
                    } else {
                        $countKo++;
                        $logManager->addAlert(
                            'RIB ' . $file . ' vide ; déplacé dans le dossier "error".',
                            'Editique > RIB',
                            'Commande de génération des RIB'
                        );
                        $fileManager->deplacerFichier($locpath, $dirError . $file);
                        // On supprime le fichier de sabcore
                        $sabManager->delete($rempath);
                    }
                } else {
                    $logManager->addError(
                        'RIB ' . $file . ' comporte des erreurs fatales. Le PDF n\'a pas été généré.',
                        'Editique > RIB',
                        'Commande de génération des RIB'
                    );
                    $fileManager->deplacerFichier($locpath, $dirError . $file);
                    $sabManager->rename($rempath, 'NonTraite_' . $file);
                }
            }
        }

        // si des erreurs se sont produite on envoi le mail recap
        if ($ribManager->getFatalError() != false or
                $logManager->fatalErrorMet == true or
                $ribManager->windowsFTPManager->logManager->fatalErrorMet == true or
                $fileManager->logManager->fatalErrorMet == true or
                $sabManager->logManager->fatalErrorMet == true
            ) {
            $log = new Log();
            $log
                ->setNiveau(Log::NIVEAU_ERREUR)
                ->setLibelle('Process de génération des RIB')
                ->setAction('Génération RIB')
                ->setModule('Editique > RIB')
                ->setUtilisateur($logManager->user);

            $logManager->persistAndFlush($log);

            $tab =
                $fileManager->logManager->tabErreurToSend +
                $ribManager->logManager->tabErreurToSend +
                $ribManager->windowsFTPManager->logManager->tabErreurToSend +
                $sabManager->logManager->tabErreurToSend;
            
            $logManager->sendMailProcessError($log, $tab, ' : editique-generate-rib');
        }

        if ($countOk > 0) {
            $logManager->addSuccess(
                $countOk . ' RIB générés avec succès.',
                'Editique > RIB',
                'Commande de génération des RIB'
            );
        }

        $content = sprintf(
            '<comment>' . $count . ' fichiers</comment> récupérés. ' .
            $countOk . ' traité(s) en succès, ' .
            $countKo . ' traité(s) en erreur.'
        );
        $output->writeln($content);
    }
}
