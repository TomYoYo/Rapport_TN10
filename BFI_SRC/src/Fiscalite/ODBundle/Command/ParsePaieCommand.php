<?php

namespace Fiscalite\ODBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fiscalite\ODBundle\Entity\Operation;
use Fiscalite\ODBundle\Entity\Mouvement;

class ParsePaieCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('od:parse:paie')
            ->setDescription('Parse le fichier de paie Talencia pour l\'importer en base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Log manager
        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');

        $countFiles   = 0;
        $correctFiles = 0;

        // Lecture du dossier d'entrée
        $path = $this->getContainer()->getParameter('dirEntreeSIRH');
        $dir = opendir($path);

        // Parsing des fichiers présents
        while ($file = readdir($dir)) {
            if ($file != '.' && $file != '..' && !is_dir($path.$file)) {
                $countFiles++;
                $filePath = $path.$file;

                $isCorrect = $this->createObjects($filePath);
                if ($isCorrect) {
                    $correctFiles++;
                }
                $this->moveFile($filePath, $isCorrect);
            }
        }

        if ($countFiles > 0) {
            if ($correctFiles > 0) {
                $lm->addSuccess(
                    $correctFiles.' fichier(s) importés correctement dans le module OD',
                    'OD > Module OD',
                    'Importation du fichier PAIE Talencia'
                );
            }
        } else {
            $lm->addAlert('Aucun fichier à importer.', 'OD > Module OD', 'Importation du fichier PAIE Talencia');
        }

        // Ecriture
        $output->writeln(sprintf('Le fichier de paie Talencia a été parsé avec succès.'));
    }

    public function createObjects($file)
    {
        //Log manager
        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');

        // Initialisations diverses
        $em           = $this->getContainer()->get('doctrine')->getManager('bfi');
        $statutSaisie = $em->getRepository('FiscaliteODBundle:Statut')->findOneBy(array('idStatut' => 'SAI'));
        $fileContent  = file_get_contents($file);

        // Explosion des lignes en tableau
        $lines = explode("\n", $fileContent);

        // Création de l'objet Operation
        $operation = new Operation();

        $operation
            ->setCodeOpe("*PA")
            ->setCodeEve("001")
            ->setTiers("")
            ->setDateCpt(new \Datetime())
            ->setDateSai(new \Datetime())
            ->setDevise("EUR")
            ->setDateVal(new \Datetime())
            ->setRefLet("")
            ->setRefAnalytique("")
            ->setStatut($statutSaisie)
            ->setDateStatut(new \Datetime());

        $em->persist($operation);
        $em->flush();

        // Création des objets Mouvement
        foreach ($lines as $num => $line) {
            if ($line != "") {
                $numMvmt = $num+1;
                $idOpe = $operation->getCodeOpe().$operation->getCodeEve().$operation->getNumPiece().$numMvmt;

                if (!$compte = substr($line, 62, 6)) {
                    return false;
                } else {
                    $compteObject = $em
                        ->getRepository('FiscaliteODBundle:CorrespondanceComptes')
                        ->findOneBy(array('numCompteExterne' => $compte));
                    if (!$compteObject) {
                        $numCompte = $compte;
                        $lm->addAlert(
                            'Correspondance de compte non trouvée. Mapping non effectué pour le compte '.$compte,
                            'OD > Module OD',
                            'Importation du fichier PAIE Talencia'
                        );
                    } else {
                        $numCompte = $compteObject->getNumCompteInterne();
                    }
                }
                if (!$libelle = substr($line, 8, 12)) {
                    return false;
                }
                if (!$codeBudget = substr($line, 73, 4)) {
                    $codeBudget = "";
                }
                if (!$typeMontant = substr($line, 28, 1)) {
                    $typeMontant = "D";
                }
                if (!$montant = ltrim(substr($line, 29, 21), "0")) {
                    return false;
                }

                if ($typeMontant == "D") {
                    $montant = "-".$montant;
                }

                $mouvement = new Mouvement();

                $mouvement
                    ->setIdOpe($idOpe)
                    ->setNumMvmt($numMvmt)
                    ->setCompte($numCompte)
                    ->setMontant($montant)
                    ->setCodeBudget($codeBudget)
                    ->setLibelle($libelle)
                    ->setOperation($operation);

                $em->persist($mouvement);
                $em->flush();
            }
        }

        return true;
    }

    public function moveFile($file, $isCorrect = true)
    {
        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');
        $fm = $this->getContainer()->get('backoffice_file.fileManager');

        if ($isCorrect) {
            $directory = $this->getContainer()->getParameter('dirTreatedSIRH');
        } else {
            $lm->addError(
                'Le fichier '.$file.' comporte des erreurs de format. Il n\'a pas été importé.',
                'OD > Module OD',
                'Importation du fichier PAIE Talencia'
            );
            $directory = $this->getContainer()->getParameter('dirErrorSIRH');
        }

        $fm->deplacerFichier($file, $directory.basename($file));
    }
}
