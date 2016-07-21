<?php

namespace Editique\LettreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chequier
 *
 * @ORM\Table(name="ZDWHOPE0")
 * @ORM\Entity()
 */
class Impaye
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
     * @ORM\Column(name="DWHOPECON", type="string", length=7)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="DWHOPETOI", type="float")
     */
    private $montantEch;

    /**
     * @var string
     *
     * @ORM\Column(name="DWHOPEDEI", type="string", length=10)
     */
    private $dateEch;

    /**
     * @var string
     *
     * @ORM\Column(name="DWHOPEDTX", type="string", length=10)
     */
    private $dateTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="DWHOPENBI", type="integer")
     */
    private $nbJour;

    /**
     * @var string
     *
     * @ORM\Column(name="DWHOPENAT", type="string", length=3)
     */
    private $nature;

    /**
     * @var string
     *
     * ////@ORM\Column(name="numCompte", type="string", length=11)
     */
    private $numCompte;

    /**
     * @var string
     *
     * @ORM\Column(name="DWHOPENDO", type="string", length=7)
     */
    private $numDossier;

    /**
     * @var string
     *
     * ////@ORM\Column(name="numPret", type="string", length=6)
     */
    private $numPret;


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
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Chequier
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
    
    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClientRemastered()
    {
        $idClientRemastered = ' '.$this->idClient;
        while (strlen($idClientRemastered) < 20) {
            $idClientRemastered = $idClientRemastered. ' ';
        }
        
        return $idClientRemastered;
    }

    /**
     * Set montantEch
     *
     * @param float $montantEch
     *
     * @return Impaye
     */
    public function setMontantEch($montantEch)
    {
        $this->montantEch = $montantEch;
    
        return $this;
    }

    /**
     * Get montantEch
     *
     * @return float
     */
    public function getMontantEch()
    {
        return number_format($this->montantEch, 2, ',', '');
    }

    /**
     * Set dateEch
     *
     * @param string $dateEch
     *
     * @return Impaye
     */
    public function setDateEch($dateEch)
    {
        $this->dateEch = $dateEch;
    
        return $this;
    }

    /**
     * Get dateEch
     *
     * @return string
     */
    public function getDateEch()
    {
        if ($this->dateEch) {
            $d = new \Datetime();
            $d->setDate(substr($this->dateEch, 0, 4), substr($this->dateEch, 4, 2), substr($this->dateEch, 6, 2));
            $d->modify('- 4 days');
            return $d->format('d/m/Y');
        }
        
        return null;
    }

    /**
     * Set nbJour
     *
     * @param integer $nbJour
     *
     * @return Impaye
     */
    public function setNbJour($nbJour)
    {
        $this->nbJour = $nbJour;
    
        return $this;
    }

    /**
     * Get nbJour
     *
     * @return integer
     */
    public function getNbJour()
    {
        return $this->nbJour;
    }

    /**
     * Set nature
     *
     * @param integer $nature
     *
     * @return Impaye
     */
    public function setNature($nature)
    {
        $this->nature = $nature;
    
        return $this;
    }

    /**
     * Get nature
     *
     * @return integer
     */
    public function getNature()
    {
        return $this->nature;
    }

    /**
     * Set numCompte
     *
     * @param string $numCompte
     *
     * @return Impaye
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
     * Set numDossier
     *
     * @param string $numDossier
     *
     * @return Impaye
     */
    public function setNumDossier($numDossier)
    {
        $this->numDossier = $numDossier;
    
        return $this;
    }

    /**
     * Get numDossier
     *
     * @return string
     */
    public function getNumDossier()
    {
        return $this->numDossier;
    }

    /**
     * Set numPret
     *
     * @param string $numPret
     *
     * @return Impaye
     */
    public function setNumPret($numPret)
    {
        $this->numPret = $numPret;
    
        return $this;
    }

    /**
     * Get numPret
     *
     * @return string
     */
    public function getNumPret()
    {
        return $this->numPret;
    }

    /**
     * Set dateTrait
     *
     * @param string $dateTrait
     *
     * @return Impaye
     */
    public function setDateTrait($dateTrait)
    {
        $this->dateTrait = $dateTrait;
    
        return $this;
    }

    /**
     * Get dateTrait
     *
     * @return string
     */
    public function getDateTrait()
    {
        return $this->dateTrait;
    }
}
