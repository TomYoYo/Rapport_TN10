<?php
/**
 * Created by PhpStorm.
 * User: t.pueyo
 * Date: 08/03/2016
 * Time: 16:08
 */

namespace BackOffice\HabilitationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PasswordCommand extends ContainerAwareCommand
{
    private $em;
    protected function configure()
    {
        $this
            ->setName('monitoring:reset:password')
            ->setDescription('Réinitialiser les mots de passe des utilisateurs ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager('bfi2');
        $manager = $this->getContainer()->get('backoffice.userManager');
        $manager->setEntityManager($this->em);
        $currentDate = new \DateTime('NOW');
        $interval = new \DateInterval('P7Y11M17D');
        $currentDate->add($interval);
        $users = $manager->getAllUsersLoginPass();
        $output->writeln(date_format($currentDate,'Y-m-d H:i:s'));
        foreach($users as $user) {
            $date = $manager->getDatePassword($user['MNUPWDUSR']);
            $dateJour = date('d-m-Y');
            $datetimeday =  new \DateTime($dateJour);
            $datetime1 = new \DateTime($date['MNUPWDEPD']);
            if($datetime1 < $currentDate && $datetime1 > $datetimeday){
                $output->writeln($user['MNUPWDUSR']);
                $manager->reinitiatePassword($user['MNUPWDUSR']);
            }
        }
        $output->writeln('Réinitialisation des mots de passe');
    }
}