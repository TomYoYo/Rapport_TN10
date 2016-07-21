<?php

namespace Editique\LettreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chequier
 *
 * @ORM\Table(name="ZADRESS0")
 * @ORM\Entity()
 */
class Lettre
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
     * @var string
     *
     * @ORM\Column(name="ADRESSRA1", type="string", length=38)
     */
    private $raisonSociale1;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRESSRA2", type="string", length=38)
     */
    private $raisonSociale2;

    /**
     * @var string
     *
     * ////@ORM\Column(name="ADRESSAD1", type="string", length=38)
     */
    private $adresse1;

    /**
     * @var string
     *
     * ////@ORM\Column(name="ADRESSAD2", type="string", length=38)
     */
    private $adresse2;

    /**
     * @var string
     *
     * ////@ORM\Column(name="ADRESSAD3", type="string", length=38)
     */
    private $adresse3;

    /**
     * @var string
     *
     * ////@ORM\Column(name="ADRESSCOP", type="string", length=12)
     */
    private $adresseCP;

    /**
     * @var string
     *
     * ////@ORM\Column(name="ADRESSVIL", type="string", length=32)
     */
    private $adresseVil;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ADRESSNUM", type="string", length=7)
     */
    private $idClient;


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
     * Set raisonSociale1
     *
     * @param string $raisonSociale1
     *
     * @return Chequier
     */
    public function setRaisonSociale1($raisonSociale1)
    {
        $this->raisonSociale1 = $raisonSociale1;

        return $this;
    }

    /**
     * Get raisonSociale1
     *
     * @return string
     */
    public function getRaisonSociale1()
    {
        return trim($this->raisonSociale1);
    }

    /**
     * Set raisonSociale2
     *
     * @param string $raisonSociale2
     *
     * @return Chequier
     */
    public function setRaisonSociale2($raisonSociale2)
    {
        $this->raisonSociale2 = $raisonSociale2;
    
        return $this;
    }

    /**
     * Get raisonSociale2
     *
     * @return string
     */
    public function getRaisonSociale2()
    {
        return trim($this->raisonSociale2);
    }
    
    public function getRaisonSociale()
    {
        if (!trim($this->raisonSociale1) && !trim($this->raisonSociale2)) {
            return null;
        }
        
        $raisonSociale = $this->raisonSociale1 . " " . $this->raisonSociale2;
        
        return array(substr($raisonSociale, 0, 60), substr($raisonSociale, 60, 17));
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return Chequier
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;
    
        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     *
     * @return Chequier
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;
    
        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * Set adresse3
     *
     * @param string $adresse3
     *
     * @return Chequier
     */
    public function setAdresse3($adresse3)
    {
        $this->adresse3 = $adresse3;
    
        return $this;
    }

    /**
     * Get adresse3
     *
     * @return string
     */
    public function getAdresse3()
    {
        return $this->adresse3;
    }

    /**
     * Set adresseCP
     *
     * @param string $adresseCP
     *
     * @return Chequier
     */
    public function setAdresseCP($adresseCP)
    {
        $this->adresseCP = $adresseCP;
    
        return $this;
    }

    /**
     * Get adresseCP
     *
     * @return string
     */
    public function getAdresseCP()
    {
        return $this->adresseCP;
    }

    /**
     * Set adresseVil
     *
     * @param string $adresseVil
     *
     * @return Chequier
     */
    public function setAdresseVil($adresseVil)
    {
        $this->adresseVil = $adresseVil;
    
        return $this;
    }

    /**
     * Get adresseVil
     *
     * @return string
     */
    public function getAdresseVil()
    {
        return $this->adresseVil;
    }
    
    public function getAdresseFinale()
    {
        $i = 0;
        $adrFinale = array(0 => null, 1 => null, 2 => null, 3 => null);
        
        if (trim($this->adresse2) == '' && trim($this->adresse3) == '') {
            $i++;
        }
        
        if (trim($this->adresse1) != '') {
            $adrFinale[$i] = $this->adresse1;
            $i++;
        }
        
        if (trim($this->adresse2) != '') {
            $adrFinale[$i] = $this->adresse2;
            $i++;
        }
        
        if (trim($this->adresse3) != '') {
            $adrFinale[$i] = $this->adresse3;
            $i++;
        }
        
        $adrFinale[$i] = $this->adresseCP . " " . $this->adresseVil;
        
        return $adrFinale;
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Lettre
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;
    
        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }
}
