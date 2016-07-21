<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackOffice\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Luis Cordova <cordoval@gmail.com>
 */
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'Le nom de l\'utilisateur'),
                new InputArgument('first_name', InputArgument::REQUIRED, 'Le prénom de l\'utilisateur'),
                new InputArgument('code_user', InputArgument::REQUIRED, 'Le code utilisateur'),
                new InputArgument('email', InputArgument::REQUIRED, 'L\'email de l\'utilisateur'),
                new InputArgument('password', InputArgument::REQUIRED, 'Un mot de passe'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Définir cet utilisateur "super-admin"'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Définir cet utilisateur comme "inactif"'),
            ));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lm = $this->getContainer()->get('backoffice_monitoring.logManager');
        
        $name       = $input->getArgument('name');
        $first_name = $input->getArgument('first_name');
        $code_user  = $input->getArgument('code_user');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $manipulator = $this->getContainer()->get('backoffice.util.user_manipulator');
        $manipulator->create($name, $first_name, $code_user, $password, $email, !$inactive, $superadmin);

        $lm->addInfo(
            'Un nouvel utilisateur () a été créé, depuis la ligne de commandes.',
            'BackOffice > User',
            'Commande "Création d\'un nouvel utilisateur"'
        );
        $output->writeln(sprintf('Utilisateur créé avec succès : <comment>%s</comment>', $name." ".$first_name));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('name')) {
            $name = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Indiquez le nom de l\'utilisateur :',
                function ($name) {
                    if (empty($name)) {
                        throw new \Exception('Le nom ne peut être vide');
                    }

                    return $name;
                }
            );
            $input->setArgument('name', $name);
        }
        
        if (!$input->getArgument('first_name')) {
            $first_name = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Indiquez le prénom de l\'utilisateur :',
                function ($first_name) {
                    if (empty($first_name)) {
                        throw new \Exception('Le prénom ne peut être vide');
                    }

                    return $first_name;
                }
            );
            $input->setArgument('first_name', $first_name);
        }
        
        if (!$input->getArgument('code_user')) {
            $code_user = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Indiquez le code utilisateur de l\'utilisateur :',
                function ($code_user) {
                    if (empty($code_user)) {
                        throw new \Exception('Le code utilisateur ne peut être vide');
                    }

                    return $code_user;
                }
            );
            $input->setArgument('code_user', $code_user);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Indiquez l\'email de l\'utilisateur:',
                function ($email) {
                    if (empty($email)) {
                        throw new \Exception('L\'email ne peut être vide');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Indiquez un mot de passe pour l\'utilisateur:',
                function ($password) {
                    if (empty($password)) {
                        throw new \Exception('Le mot de passe ne peut être vide');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}
