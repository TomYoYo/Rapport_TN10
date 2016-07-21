<?php

namespace BackOffice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use BackOffice\UserBundle\Entity\Profil;

class LoadUser implements FixtureInterface, ContainerAwareInterface
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
        $lm = $this->container->get('backoffice_monitoring.logManager');
        
        /** USER PERSO DAVID : SUPER_ADMIN **/
        $userDavid = new Profil();
        
        $userDavid
            ->setLogin("d.briand")
            ->setUsername("d.briand")
            ->setNom("Briand")
            ->setPrenom("David")
            ->setCodeUser("4581")
            ->setEmail("david.briand-exterieur@fiducial.net")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_SUPER_ADMIN");

        $manager->persist($userDavid);
        
        /** USER PERSO PERRINE : SUPER_ADMIN **/
        $userPerrine = new Profil();
        
        $userPerrine
            ->setLogin("p.leost")
            ->setUsername("p.leost")
            ->setNom("Leost")
            ->setPrenom("Perrine")
            ->setCodeUser("9114")
            ->setEmail("perrine.leost@fiducial.net")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_SUPER_ADMIN");

        $manager->persist($userPerrine);
        
        /** USER TEST COMPTABLE : COMPTABLE **/
        $userComptable = new Profil();
        
        $userComptable
            ->setLogin("comptable")
            ->setUsername("comptable")
            ->setNom("Comptable")
            ->setPrenom("Test")
            ->setCodeUser("1240")
            ->setEmail("comptable@lezb.fr")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_COMPTABLE");

        $manager->persist($userComptable);
        
        /** USER TEST AGENCE : AGENCE **/
        $userAgence = new Profil();
        
        $userAgence
            ->setLogin("agence")
            ->setUsername("agence")
            ->setNom("Agence")
            ->setPrenom("Test")
            ->setCodeUser("1240")
            ->setEmail("agence@lezb.fr")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_AGENCE");

        $manager->persist($userAgence);
        
        /** USER TEST ADMIN : ADMIN **/
        $userAdmin = new Profil();
        
        $userAdmin
            ->setLogin("admin")
            ->setUsername("admin")
            ->setNom("Admin")
            ->setPrenom("Test")
            ->setCodeUser("9571")
            ->setEmail("admin@lezb.fr")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_ADMIN");

        $manager->persist($userAdmin);
        
        /** USER TEST USER : USER **/
        $userSimple = new Profil();
        
        $userSimple
            ->setLogin("user")
            ->setUsername("user")
            ->setNom("User")
            ->setPrenom("Test")
            ->setCodeUser("2310")
            ->setEmail("user@lezb.fr")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_USER");

        $manager->persist($userSimple);
        
        /** USER TEST COMMERCIAL : COMMERCIAL **/
        $userCommercial = new Profil();
        
        $userCommercial
            ->setLogin("commercial")
            ->setUsername("commercial")
            ->setNom("Commercial")
            ->setPrenom("Test")
            ->setCodeUser("2310")
            ->setEmail("commercial@lezb.fr")
            ->setPlainPassword("x1234x")
            ->setCodeEtabl("0001")
            ->setCodeAgence("0102")
            ->setCodeService("RC")
            ->setCodeSsService("RC")
            ->setEnabled(true)
            ->addRole("ROLE_COMMERCIAL");

        $manager->persist($userCommercial);
        
        // Flush
        $manager->flush();
        
        $lm->addAlert(
            'Génération effectuée. 6 enregistrements ajoutés.',
            'BackOffice > User',
            'Génération de données persistantes.'
        );
    }
}
