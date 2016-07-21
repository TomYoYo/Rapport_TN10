<?php

namespace Fiscalite\ODBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreCommand extends ContainerAwareCommand
{
    public $print = "";

    protected function configure()
    {
        $this
            ->setName('od:generate:cre')
            ->setDescription('Genere le fichier CRE du jour.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('fiscalite.CREManager');
        $sabSFTP = $this->getContainer()->get('back_office_connexion.SabSFTP');
        $manager->setSabSFTP($sabSFTP);

        if ($manager->generate()) {
            $output->writeln(sprintf('Le CRE du <comment>%s</comment> a ete genere avec succes.', date('d-m-Y')));
        } else {
            $output->writeln(sprintf(
                'Le CRE du <comment>%s</comment> n\'a pas ete genere (aucune operation saisie).',
                date('d-m-Y')
            ));
        }
    }
}
