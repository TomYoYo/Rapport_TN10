<?php

namespace Editique\FiscalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foyer
 *
 * @ORM\Table(name="ZIFUADM0")
 * @ORM\Entity()
 */
class Admin
{
    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMFIS", type="string", length=7)
     * @ORM\Id
     */
    private $numFiscal;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMNOM", type="string", length=32)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMPRE", type="string", length=32)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMAP1", type="string", length=32)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMAP2", type="string", length=32)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMAP3", type="string", length=32)
     */
    private $adresse3;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMAP4", type="string", length=32)
     */
    private $adresse4;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMDAN", type="integer")
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMSIR", type="string", length=14)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMCOA", type="integer")
     */
    private $communeNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMLIN", type="string", length=32)
     */
    private $nomCommuneNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMDEN", type="string", length=2)
     */
    private $departementNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMNMA", type="string", length=32)
     */
    private $nomMarital;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUADMANE", type="integer")
     */
    private $annee;

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
     * Set numFiscal
     *
     * @param string $numFiscal
     *
     * @return Admin
     */
    public function setNumFiscal($numFiscal)
    {
        $this->numFiscal = $numFiscal;
    
        return $this;
    }

    /**
     * Get numFiscal
     *
     * @return string
     */
    public function getNumFiscal()
    {
        return $this->numFiscal;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Admin
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Admin
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    
        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return Admin
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
     * @return Admin
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
     * @return Admin
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
     * Set adresse4
     *
     * @param string $adresse4
     *
     * @return Admin
     */
    public function setAdresse4($adresse4)
    {
        $this->adresse4 = $adresse4;
    
        return $this;
    }

    /**
     * Get adresse4
     *
     * @return string
     */
    public function getAdresse4()
    {
        return $this->adresse4;
    }

    /**
     * Set dateNaissance
     *
     * @param integer $dateNaissance
     *
     * @return Admin
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
    
        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return integer
     */
    public function getDateNaissance()
    {
        if ($this->dateNaissance) {
            return substr($this->dateNaissance, 4, 2) . '/' .
                substr($this->dateNaissance, 2, 2) . '/19' .
                substr($this->dateNaissance, 0, 2);
        }
        
        return null;
    }

    /**
     * Set siret
     *
     * @param string $siret
     *
     * @return Admin
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;
    
        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set communeNaissance
     *
     * @param integer $communeNaissance
     *
     * @return Admin
     */
    public function setCommuneNaissance($communeNaissance)
    {
        $this->communeNaissance = $communeNaissance;
    
        return $this;
    }

    /**
     * Get communeNaissance
     *
     * @return integer
     */
    public function getCommuneNaissance()
    {
        if ($this->communeNaissance == 0) {
            return null;
        }

        return $this->communeNaissance;
    }

    /**
     * Set nomCommuneNaissance
     *
     * @param string $nomCommuneNaissance
     *
     * @return Admin
     */
    public function setNomCommuneNaissance($nomCommuneNaissance)
    {
        $this->nomCommuneNaissance = $nomCommuneNaissance;
    
        return $this;
    }

    /**
     * Get nomCommuneNaissance
     *
     * @return string
     */
    public function getNomCommuneNaissance()
    {
        return $this->nomCommuneNaissance;
    }

    /**
     * Set departementNaissance
     *
     * @param string $departementNaissance
     *
     * @return Admin
     */
    public function setDepartementNaissance($departementNaissance)
    {
        $this->departementNaissance = $departementNaissance;
    
        return $this;
    }

    /**
     * Get departementNaissance
     *
     * @return string
     */
    public function getDepartementNaissance()
    {
        return $this->departementNaissance;
    }

    /**
     * Set nomMarital
     *
     * @param string $nomMarital
     *
     * @return Admin
     */
    public function setNomMarital($nomMarital)
    {
        $this->nomMarital = $nomMarital;
    
        return $this;
    }

    /**
     * Get nomMarital
     *
     * @return string
     */
    public function getNomMarital()
    {
        return $this->nomMarital;
    }

    /**
     * Set annee
     *
     * @param string $annee
     *
     * @return Foyer
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return string
     */
    public function getAnnee()
    {
        return $this->annee;
    }
}
