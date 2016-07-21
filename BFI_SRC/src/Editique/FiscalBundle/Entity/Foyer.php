<?php

namespace Editique\FiscalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foyer
 *
 * @ORM\Table(name="ZIFUFOY0")
 * @ORM\Entity()
 */
class Foyer
{
    /**
     * @var string
     *
     * @ORM\Column(name="IFUFOYFIS", type="string", length=7)
     * @ORM\Id
     */
    private $numFiscal;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUFOYCOM", type="string", length=20)
     */
    private $numCompte;

    /**
     * @var string
     *
     * @ORM\Column(name="IFUFOYANE", type="integer")
     */
    private $annee;
    
    /**
     * @var string
     *
     * ///ORM\Column(name="compteur")
     */
    private $compteur;
    
    /**
     * @var string
     *
     * ///ORM\Column(name="admin")
     */
    private $admin;

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
     * @return Foyer
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
     * Set numCompte
     *
     * @param string $numCompte
     *
     * @return Foyer
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
        return $this->numCompte;
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
    
    public function setCompteur($compteur)
    {
        $this->compteur = $compteur;
        
        return $this;
    }
    
    public function getCompteur()
    {
        return $this->compteur;
    }
    
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        
        return $this;
    }
    
    public function getAdmin()
    {
        return $this->admin;
    }
}
