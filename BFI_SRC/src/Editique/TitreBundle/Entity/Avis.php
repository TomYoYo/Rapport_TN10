<?php

namespace Editique\TitreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avis
 *
 * @ORM\Table(name="SABPBS.FDEUFCH00_PELENT_LENT")
 * @ORM\Entity(repositoryClass="Editique\TitreBundle\Entity\AvisRepository")
 */
class Avis
{
    /**
     * @var string
     *
     * @ORM\Column(name="NUPORT", type="string", length=7)
     * @ORM\Id
     */
    private $numPort;
    /**
     * @var string
     *
     * @ORM\Column(name="NOMDES", type="string", length=38)
     */
    private $raisonSociale1;
    /**
     * @var string
     *
     * @ORM\Column(name="NOMDES1", type="string", length=38)
     */
    private $raisonSociale2;
    /**
     * @var string
     *
     * @ORM\Column(name="RUECPT", type="string", length=38)
     */
    private $adresseRue;
    /**
     * @var string
     *
     * @ORM\Column(name="BATIMT", type="string", length=38)
     */
    private $adresseBat;
    /**
     * @var string
     *
     * @ORM\Column(name="CODPOS", type="string", length=10)
     */
    private $codePostal;
    /**
     * @var string
     *
     * @ORM\Column(name="VILLET", type="string", length=38)
     */
    private $ville;
    /**
     * @var string
     *
     * @ORM\Column(name="RACINE", type="string", length=6)
     */
    private $idClient;
    /**
     * @var string
     *
     * @ORM\Column(name="REFEXT", type="string", length=20)
     */
    private $numCompte;
    /**
     * @var string
     *
     * @ORM\Column(name="DATCRE", type="string", length=8)
     */
    private $jourCreation;
    /**
     * @var string
     *
     * @ORM\Column(name="HEUCRE", type="string", length=8)
     */
    private $heureCreation;
    /**
     * @var string
     *
     * ////ORM\Column(name="idEsab", type="string", length=12)
     */
    private $idEsab;
    /**
     * @var string
     *
     * ////ORM\Column(name="details", type="array")
     */
    private $details;

    /**
     * Set raisonSociale1
     *
     * @param string $raisonSociale1
     *
     * @return Portefeuille
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
        return $this->raisonSociale1;
    }

    /**
     * Set raisonSociale2
     *
     * @param string $raisonSociale2
     *
     * @return Portefeuille
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
        return $this->raisonSociale2;
    }
    
    public function getRaisonSociale()
    {
        $raisonSociale = $this->raisonSociale1 . " " . $this->raisonSociale2;
        
        return array(substr($raisonSociale, 0, 60), substr($raisonSociale, 60, 17));
    }

    /**
     * Set adresseRue
     *
     * @param string $adresseRue
     *
     * @return Portefeuille
     */
    public function setAdresseRue($adresseRue)
    {
        $this->adresseRue = $adresseRue;
    
        return $this;
    }

    /**
     * Get adresseRue
     *
     * @return string
     */
    public function getAdresseRue()
    {
        return $this->adresseRue;
    }

    /**
     * Set adresseBat
     *
     * @param string $adresseBat
     *
     * @return Portefeuille
     */
    public function setAdresseBat($adresseBat)
    {
        $this->adresseBat = $adresseBat;
    
        return $this;
    }

    /**
     * Get adresseBat
     *
     * @return string
     */
    public function getAdresseBat()
    {
        return $this->adresseBat;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Portefeuille
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
    
        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Portefeuille
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    
        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }
    
    public function getAdresse()
    {
        $i = 0;
        $adr = array(0 => null, 1 => null, 2 => null);
        
        if ($this->adresseRue != null) {
            $adr[$i] = $this->adresseRue;
            $i++;
        }
        
        if ($this->adresseBat != null) {
            $adr[$i] = $this->adresseBat;
            $i++;
        }
        
        $adr[$i] = $this->codePostal . " " . $this->ville;
        
        return $adr;
    }

    /**
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Portefeuille
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
        while (strlen($this->idClient) < 7) {
            $this->idClient = "0" . $this->idClient;
        }
        
        return $this->idClient;
    }

    /**
     * Set numCompte
     *
     * @param string $numCompte
     *
     * @return Portefeuille
     */
    public function setNumCompte($numCompte)
    {
        $this->numCompte = $numCompte;
    
        return $this;
    }

    /**
     * Get numCompte
     *
     * @return string
     */
    public function getNumCompte()
    {
        return trim($this->numCompte);
    }

    /**
     * Set numPort
     *
     * @param string $numPort
     *
     * @return Portefeuille
     */
    public function setNumPort($numPort)
    {
        $this->numPort = $numPort;
    
        return $this;
    }

    /**
     * Get numPort
     *
     * @return string
     */
    public function getNumPort()
    {
        return $this->numPort;
    }
    
    public function setTotalPort($totalPort)
    {
        $this->totalPort = $totalPort;
    
        return $this;
    }
    
    public function getTotalPort()
    {
        return number_format($this->totalPort, 2, ',', ' ');
    }
    
    public function setIdEsab($idEsab)
    {
        $this->idEsab = $idEsab;
    
        return $this;
    }
    
    public function getIdEsab()
    {
        return $this->idEsab;
    }
    
    public function setValeurs($valeurs)
    {
        $this->valeurs = $valeurs;
    
        return $this;
    }
    
    public function getValeurs()
    {
        return $this->valeurs;
    }
    
    public function setCategories($categories)
    {
        $this->categories = $categories;
    
        return $this;
    }
    
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set jourCreation
     *
     * @param string $jourCreation
     *
     * @return Avis
     */
    public function setJourCreation($jourCreation)
    {
        $this->jourCreation = $jourCreation;
    
        return $this;
    }

    /**
     * Get jourCreation
     *
     * @return string
     */
    public function getJourCreation()
    {
        return $this->jourCreation;
    }

    /**
     * Set heureCreation
     *
     * @param string $heureCreation
     *
     * @return Avis
     */
    public function setHeureCreation($heureCreation)
    {
        $this->heureCreation = $heureCreation;
    
        return $this;
    }

    /**
     * Get heureCreation
     *
     * @return string
     */
    public function getHeureCreation()
    {
        return $this->heureCreation;
    }
    
    public function setDetails($details)
    {
        $this->details = $details;
    
        return $this;
    }
    
    public function getDetails()
    {
        return $this->details;
    }
}
