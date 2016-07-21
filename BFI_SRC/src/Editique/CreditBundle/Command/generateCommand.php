<?php

namespace Editique\CreditBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:generate:echeancier')
            ->setDescription('Génére les txt et pdf après être aller chercher le fichiers source sur sabCore.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init Managers
        $echManager = $this->getContainer()->get('editique.echeancierManager');
        $logManager = $this->getContainer()->get('backoffice_monitoring.logManager');
        $fileManager = $this->getContainer()->get('backoffice_file.fileManager');
        $sabManager = $this->getContainer()->get('back_office_connexion.SabSFTP');

        // JD : c'est pas jojo ce que j'ai fait, il faudrait faire cela autrement... singleton ?
        $logManager->setProcessMode(true);
        $echManager->logManager->setProcessMode(true);
        $echManager->windowsFTPManager->logManager->setProcessMode(true);
        $fileManager->logManager->setProcessMode(true);
        $sabManager->logManager->setProcessMode(true);

        // Init directories
        $dirEntree = $this->getContainer()->getParameter('dirEntreeEditique');
        $dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        $dirError = $this->getContainer()->getParameter('dirErrorEditique');
        $dirTreated = $this->getContainer()->getParameter('dirTreatedEditique');
        $dirRemote = $this->getContainer()->getParameter('sabCore.dirPathXML');
        $dirRemote2 = $this->getContainer()->getParameter('sabCore.dirPathPrint');
        $subDir = $this->getContainer()->getParameter('sabCore.subDirXML');
        $dirSortieSab = $this->getContainer()->getParameter('sabCore.dirSortie');

        // Init variables
        $countXML = $countTXT = 0;
        $countOkXML = $countOkTXT = 0;
        $countKoXML = $countKoTXT = 0;
        $regexXML = "/^EcheancierCredit_\d{4}-\d{2}-\d{2}\ \d{2}-\d{2}-\d{2}-\d{3}.xml$/";
        $regexTXT = "/^CREGS601P1_\d+.txt$/";

        // connexion sabCore
        $filesXML = $sabManager->fichiersSousDossiersMasques($dirRemote, $subDir, $regexXML);
        $filesTXT = $sabManager->fichiersMasques($dirRemote2, $regexTXT);
        
        foreach ($filesXML as $pathDirectory => $files) {
            foreach ($files as $file) {
                $echManager->initCompte();
                $countXML++;

                // On copie tout les fichiers dans in sur bfi server
                $locpath = $dirEntree . $file; // Dossier en interne (exchange)
                $rempath = $pathDirectory . $file; // Dossier en externe (SAB)
                $sabManager->download($rempath, $locpath);

                // On lit le fichier
                $echManager->typeSpool = 'XML';
                $echManager->lireContent($locpath);
                
                // On écrit la sortie
                $echManager->ecrireSortie($dirSortie);
                
                // Récupération path et size fichier TXT
                $txt = $echManager->getFileName('txt');
                $sizeTXT = filesize($dirSortie . $txt);

                // Récupération path et size fichier PDF
                $pdf = $echManager->getFileName('pdf');
                $sizePDF = filesize($dirSortie . $pdf);
                
                if ($sizeTXT > 0 && $sizePDF > 0) {
                    $countOkXML++;
                    // Déplacement dans treated
                    $fileManager->deplacerFichier($locpath, $dirTreated . $file);
                    // On supprime le fichier de sabcore
                    $sabManager->delete($rempath);
                } else {
                    $countKoXML++;
                    $logManager->addAlert(
                        'Echéancier ' . $file . ' vide ; déplacé dans le dossier "error".',
                        'Editique > Crédit',
                        'Commande de génération des Echéanciers'
                    );
                    $fileManager->deplacerFichier($locpath, $dirError . $file);
                }
            }
        }
        
        foreach ($filesTXT as $file) {
            $echManager->initCompte();
            $countTXT++;

            // On copie tout les fichiers dans in sur bfi server
            $locpath = $dirEntree . $file; // Dossier en interne (exchange)
            $rempath = $dirRemote2 . $file; // Dossier en externe (SAB)
            $sabManager->download($rempath, $locpath);

            // On lit le fichier
            $echManager->typeSpool = 'TXT';
            $echManager->lireContent($locpath);
            
            if ($echManager->getFatalError() == false) {
                // On écrit la sortie
                $echManager->ecrireSortie($dirSortie);

                // Récupération path et size fichier TXT
                $txt = $echManager->getFileName('txt');
                $sizeTXT = filesize($dirSortie . $txt);

                // Récupération path et size fichier PDF
                $pdf = $echManager->getFileName('pdf');
                $sizePDF = filesize($dirSortie . $pdf);

                if ($sizeTXT > 0 && $sizePDF > 0) {
                    $countOkTXT++;
                    // Déplacement dans treated
                    $fileManager->deplacerFichier($locpath, $dirTreated . $file);
                } else {
                    $countKoTXT++;
                    $logManager->addAlert(
                        'Echéancier ' . $file . ' vide ; déplacé dans le dossier "error".',
                        'Editique > Crédit',
                        'Commande de génération des Echéanciers'
                    );
                    $fileManager->deplacerFichier($locpath, $dirError . $file);
                }
            } else {
                $countKoTXT++;
                $logManager->addError(
                    'Spool ' . $file . ' incorrect ; déplacé dans le dossier "error".',
                    'Editique > Crédit',
                    'Commande de génération des Echéanciers'
                );
                $fileManager->deplacerFichier($locpath, $dirError . $file);
            }
            // On supprime le fichier de sabcore
            $sabManager->delete($rempath);
        }
        
        $countTotal = $countXML+$countTXT;
        $countOkTotal = $countOkXML+$countOkTXT;
        $countKoTotal = $countKoXML+$countKoTXT;

        $content = sprintf(
            '<comment>' . $countTotal . ' fichiers</comment> ('.$countXML.' XML et '.$countTXT.' TXT) récupérés. ' .
            $countOkTotal . ' traité(s) en succès ('.$countOkXML.' XML et '.$countOkTXT.' TXT), ' .
            $countKoTotal . ' traité(s) en erreur ('.$countKoXML.' XML et '.$countKoTXT.' TXT).'
        );
        $output->writeln($content);
    }
}
