<?php

namespace BackOffice\MonitoringBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Performance
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BackOffice\MonitoringBundle\Entity\PerformanceRepository")
 *
 */
class Performance
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
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbClient", type="integer")
     */
    private $nbClient;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbProduit", type="integer")
     */
    private $nbProduit;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbOperation", type="integer")
     */
    private $nbOperation;

    /**
     * @var float
     *
     * @ORM\Column(name="dureeBdd", type="float", nullable=true)
     */
    private $dureeBdd;

    /**
     * @var float
     *
     * @ORM\Column(name="dureeJour", type="float", nullable=true)
     */
    private $dureeJour;

    /**
     * @var float
     *
     * @ORM\Column(name="dureeJourBD", type="float", nullable=true)
     */
    private $dureeJourBD;



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
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return Performance
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set nbClient
     *
     * @param integer $nbClient
     *
     * @return Performance
     */
    public function setNbClient($nbClient)
    {
        $this->nbClient = $nbClient;

        return $this;
    }

    /**
     * Get nbClient
     *
     * @return integer
     */
    public function getNbClient()
    {
        return $this->nbClient;
    }

    /**
     * Set nbProduit
     *
     * @param integer $nbProduit
     *
     * @return Performance
     */
    public function setNbProduit($nbProduit)
    {
        $this->nbProduit = $nbProduit;

        return $this;
    }

    /**
     * Get nbProduit
     *
     * @return integer
     */
    public function getNbProduit()
    {
        return $this->nbProduit;
    }

    /**
     * Set nbOperation
     *
     * @param integer $nbOperation
     *
     * @return Performance
     */
    public function setNbOperation($nbOperation)
    {
        $this->nbOperation = $nbOperation;

        return $this;
    }

    /**
     * Get nbOperation
     *
     * @return integer
     */
    public function getNbOperation()
    {
        return $this->nbOperation;
    }

    /**
     * Set duree
     *
     * @param float $duree
     *
     * @return Performance
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return float
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set dureeBdd
     *
     * @param float $dureeBdd
     *
     * @return Performance
     */
    public function setDureeBdd($dureeBdd)
    {
        $this->dureeBdd = $dureeBdd;

        return $this;
    }

    /**
     * Get dureeBdd
     *
     * @return float
     */
    public function getDureeBdd()
    {
        return $this->dureeBdd;
    }

    /**
     * Set dureeJour
     *
     * @param float $dureeJour
     *
     * @return Performance
     */
    public function setDureeJour($dureeJour)
    {
        $this->dureeJour = $dureeJour;

        return $this;
    }

    /**
     * Get dureeJour
     *
     * @return float
     */
    public function getDureeJour()
    {
        return $this->dureeJour;
    }

    /**
     * Set dureeJourBD
     *
     * @param float $dureeJourBD
     *
     * @return Performance
     */
    public function setDureeJourBD($dureeJourBD)
    {
        $this->dureeJourBD = $dureeJourBD;

        return $this;
    }

    /**
     * Get dureeJourBD
     *
     * @return float
     */
    public function getDureeJourBD()
    {
        return $this->dureeJourBD;
    }
}
