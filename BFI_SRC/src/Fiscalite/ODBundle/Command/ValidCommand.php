<?php

namespace Fiscalite\ODBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('od:sendMail:valid')
            ->setDescription('Envoi les mails aux valideurs, sur les opérations à valider.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');

        $operations = $em->getRepository('FiscaliteODBundle:Operation')->findBy(
            array('statut' => 'VAL', 'isDeleted' => 0)
        );
        
        
        if (count($operations)) {
            // Envoie des mails
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

            mail(
                $this->getDestMailErreur(),
                "OD en attente de validation",
                $this->getContainer()->get('templating')->render('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                    'parts' => array(
                        array(
                            'title' => 'Des opérations diverses sont en attente de validation',
                            'content' => $this->getContainer()->get('templating')->render(
                                'FiscaliteODBundle:Mailing:valid.html.twig',
                                array('operations' => $operations)
                            )
                        )
                    )
                )),
                $headers
            );
            
            // Ecriture
            $output->writeln(sprintf('Le mail vient d\'être envoyé aux destinataires.'));
        } else {
            // Ecriture
            $output->writeln(sprintf('Aucune opération à valider. Aucun mail n\'est donc envoyé.'));
        }
    }
    
    private function getDestMailErreur()
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $users = $em->getRepository('BackOfficeUserBundle:Profil')->search(array('role' => 'ROLE_SUPER_COMPTABLE'));

        $to = array();
        foreach ($users as $u) {
            $to []= $u->getPrenom().' '.$u->getNom() . '<' . $u->getEmail() . '>';
        }
        
        return implode(',', $to);
    }
}
