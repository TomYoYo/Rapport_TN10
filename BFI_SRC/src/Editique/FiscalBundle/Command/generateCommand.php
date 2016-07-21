<?php

namespace Editique\FiscalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('editique:generate:ifu')
            ->setDescription('Génére les txt et pdf après être aller chercher les données en BDD.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // init Managers
        $ifuManager = $this->getContainer()->get('editique.IFUManager');
        $em = $this->getContainer()->get('doctrine')->getManager('bfi2');
        
        $dirSortie = $this->getContainer()->getParameter('dirSortieEditique');
        
        $ifuManager->setEntityManager($em);
        $this->foyers = $ifuManager->getIFU();

        foreach ($this->foyers as $foyer) {
            $ifuManager->init();
            $ifuManager->setFoyer($foyer);
            $admin = $ifuManager->setAdmin();
            $totaux = $ifuManager->setTotaux();
            if ($admin && $totaux) {
                $ifuManager->ecrireSortie($dirSortie);
                $ifuManager->integrerPourImpression();
            }
        }
        
        $ifuManager->ecrirePDFGlobal($dirSortie);
    }
}
