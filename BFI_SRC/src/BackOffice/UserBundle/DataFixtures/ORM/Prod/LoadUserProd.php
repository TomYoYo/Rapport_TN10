<?php

namespace BackOffice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use BackOffice\UserBundle\Entity\Profil;

class LoadUserProd implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Log Manager
        return;
        $lm = $this->container->get('backoffice_monitoring.logManager');

        /** USER SUPER_ADMIN * */
        $userSA = new Profil();

        $userSA
            ->setLogin("bfiSuperAdmin")
            ->setUsername("bfiSAdmin")
            ->setNom("bfi")
            ->setPrenom("sadmin")
            ->setCodeUser("")
            ->setEmail("julien.chevausset@fiducial.net")
            ->setPlainPassword('321$sdf_tt')
            ->setCodeEtabl("0000")
            ->setCodeAgence("0000")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_SUPER_ADMIN");

        $manager->persist($userSA);

        /** USER ADMIN * */
        $userA = new Profil();

        $userA
            ->setLogin("bfiAdmin")
            ->setUsername("bfiAdmin")
            ->setNom("bfi")
            ->setPrenom("admin")
            ->setCodeUser("")
            ->setEmail("julien.chevausset@fiducial.net")
            ->setPlainPassword('3-$£sdff_tt')
            ->setCodeEtabl("0000")
            ->setCodeAgence("0000")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_ADMIN");

        $manager->persist($userA);

        $manager->flush();

        $lm->addAlert(
            'Génération effectuée des utilisateurs de base pour prod. 2 enregistrements ajoutés.',
            'BackOffice > User',
            'Génération de données persistantes.'
        );
    }
}
