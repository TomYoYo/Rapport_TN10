<?php

namespace BackOffice\EvolutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evolution
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BackOffice\EvolutionBundle\Entity\EvolutionRepository")
 */
class Evolution
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
     * @ORM\Column(name="versionNumber", type="string", length=25)
     */
    private $versionNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="preprodRealisation", type="datetime", nullable=true)
     */
    private $preprodRealisation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prodRealisation", type="datetime", nullable=true)
     */
    private $prodRealisation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimatedDate", type="datetime", nullable=true)
     */
    private $estimatedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


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
     * Set versionNumber
     *
     * @param string $versionNumber
     *
     * @return Evolution
     */
    public function setVersionNumber($versionNumber)
    {
        $this->versionNumber = $versionNumber;
    
        return $this;
    }

    /**
     * Get versionNumber
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

    /**
     * Set preprodRealisation
     *
     * @param \DateTime $preprodRealisation
     *
     * @return Evolution
     */
    public function setPreprodRealisation($preprodRealisation)
    {
        $this->preprodRealisation = $preprodRealisation;
    
        return $this;
    }

    /**
     * Get preprodRealisation
     *
     * @return \DateTime
     */
    public function getPreprodRealisation()
    {
        return $this->preprodRealisation;
    }

    /**
     * Set prodRealisation
     *
     * @param \DateTime $prodRealisation
     *
     * @return Evolution
     */
    public function setProdRealisation($prodRealisation)
    {
        $this->prodRealisation = $prodRealisation;
    
        return $this;
    }

    /**
     * Get prodRealisation
     *
     * @return \DateTime
     */
    public function getProdRealisation()
    {
        return $this->prodRealisation;
    }

    /**
     * Set estimatedDate
     *
     * @param \DateTime $estimatedDate
     *
     * @return Evolution
     */
    public function setEstimatedDate($estimatedDate)
    {
        $this->estimatedDate = $estimatedDate;
    
        return $this;
    }

    /**
     * Get estimatedDate
     *
     * @return \DateTime
     */
    public function getEstimatedDate()
    {
        return $this->estimatedDate;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Evolution
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
