<?php

namespace Editique\LettreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateImpayeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:generate:impaye')
            ->setDescription('Génére les txt et pdf après être aller chercher les données en BDD.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init Managers
        $lettreManager = $this->getContainer()->get('editique.lettreManager');
        
        // Sortie
        $directory = $this->getContainer()->getParameter('dirSortieEditique');
        
        // init
        $this->em2 = $this->getContainer()->get('doctrine')->getManager('bfi2');
        $lettreManager->setEntityManagerPers($this->em2);
        
        $this->entities = $lettreManager->getImpayes();
        
        foreach ($this->entities as $this->entity) {
            $res = $lettreManager->prepareLettre($this->entity->getId(), 'IMP');
            if ($res) {
                $lettreManager->ecrireSortie($directory, 'IMP');
            }
        }
    }
}
