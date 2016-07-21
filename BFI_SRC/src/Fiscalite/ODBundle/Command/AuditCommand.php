<?php

namespace Fiscalite\ODBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AuditCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('od:generate:audit')
            ->setDescription('Génére la piste d\'audit du jour.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('bfi');
        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');

        $actions = $em->getRepository('FiscaliteODBundle:Action')->getActionsPourAudit();

        if (count($actions)) {
            $directory = $this->getContainer()->getParameter('dirSortieModuleODAudit');
            $fileName = "AUDITACTION_ODMULTI_".date('Ymd').".txt";

            $handle = fopen($directory.$fileName, 'w');

            // On parse toutes les actions
            foreach ($actions as $action) {
                $print =
                    $action->getIdAction()."/".
                    $action->getDateAction()->format('d-m-Y')."/".
                    $action->getLibelleAction()."/".
                    $action->getProfil()."/".
                    $action->getOperation()."\n";

                fputs($handle, $print);
            }

            $lm->addSuccess(
                'Fichier généré avec '.count($actions).' lignes.',
                'OD > Module OD',
                'Génération du fichier audit'
            );
            $output->writeln(sprintf('Le fichier d\'audit du <comment>%s</comment> a été généré.', date('d-m-Y')));
        } else {
            $lm->addInfo(
                'Fichier non généré, car sans action.',
                'OD > Module OD',
                'Génération du fichier audit'
            );
            $output->writeln(
                sprintf(
                    'Le fichier d\'audit du <comment>%s</comment> n\'a pas été généré (Aucune action ce jour).',
                    date('d-m-Y')
                )
            );
        }
    }
}
