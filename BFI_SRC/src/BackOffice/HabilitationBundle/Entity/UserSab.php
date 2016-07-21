<?php

namespace BackOffice\HabilitationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mageekguy\atoum\tests\units\asserters\integer;

/**
 * UserSab
 *
 * @ORM\Table()
 * @ORM\Entity
 */

class UserSab
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="idsab", type="integer")
     */
    private $idsab;

    /**
     * @var string
     *
     * @ORM\Column(name="groupe2", type="string", length=255)
     */
    private $groupe2;

    /**
     * @var string
     *
     * @ORM\Column(name="groupe3", type="string", length=255)
     */
    private $groupe3;

    /**
     * @var string
     *
     * @ORM\Column(name="groupe4", type="string", length=255)
     */
    private $groupe4;

    /**
     * @var string
     *
     * @ORM\Column(name="files", type="string", length=255)
     */
   private $files;

    /**
     * @var string
     *
     * @ORM\Column(name="menu", type="string", length=1)
     */
    private $menu;

    /**
     * @var integer
     *
     * @ORM\Column(name="agence", type="integer")
     */
    private $agence;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=255)
     */
    private $service;

    /**
     * @var string
     *
     * @ORM\Column(name="sous_service", type="string", length=255)
     */
    private $sousService;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get idsab
     *
     * @return integer
     */
    public function getIdSab()
    {
        return $this->idsab;
    }

    /**
     * Set idsab
     *
     * @param integer $idsab
     *
     * @return UserSab
     */
    public function setIdSab($idsab)
    {
        $this->idsab = $idsab;

        return $this;
    }

    /**
     * Set groupe2
     *
     * @param string $groupe2
     *
     * @return UserSab
     */
    public function setGroupe2($groupe2)
    {
        $this->groupe2 = $groupe2;
    
        return $this;
    }

    /**
     * Get groupe2
     *
     * @return string 
     */
    public function getGroupe2()
    {
        return $this->groupe2;
    }

    /**
     * Set groupe3
     *
     * @param string $groupe3
     *
     * @return UserSab
     */
    public function setGroupe3($groupe3)
    {
        $this->groupe3 = $groupe3;
    
        return $this;
    }

    /**
     * Get groupe3
     *
     * @return string 
     */
    public function getGroupe3()
    {
        return $this->groupe3;
    }

    /**
     * Set groupe4
     *
     * @param string $groupe4
     *
     * @return UserSab
     */
    public function setGroupe4($groupe4)
    {
        $this->groupe4 = $groupe4;
    
        return $this;
    }

    /**
     * Get groupe4
     *
     * @return string 
     */
    public function getGroupe4()
    {
        return $this->groupe4;
    }

    /**
     * Set files
     *
     * @param string $files
     *
     * @return UserSab
     */
    public function setFile($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return string
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set menu
     *
     * @param string $menu
     *
     * @return UserSab
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return string 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set agence
     *
     * @param integer $agence
     *
     * @return UserSab
     */
    public function setAgence($agence)
    {
        $this->agence = $agence;
    
        return $this;
    }

    /**
     * Get agence
     *
     * @return integer 
     */
    public function getAgence()
    {
        return $this->agence;
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return UserSab
     */
    public function setService($service)
    {
        $this->service = $service;
    
        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set sousService
     *
     * @param string $sousService
     *
     * @return UserSab
     */
    public function setSousService($sousService)
    {
        $this->sousService = $sousService;
    
        return $this;
    }

    /**
     * Get sousService
     *
     * @return string 
     */
    public function getSousService()
    {
        return $this->sousService;
    }
}

