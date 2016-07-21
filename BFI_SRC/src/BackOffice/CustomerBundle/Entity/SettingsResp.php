<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SettingsResp
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SettingsResp
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
     * @ORM\Column(name="codeResponsable", type="string", length=3, unique = true)
     */
    private $codeResponsable;

    /**
     * @ORM\OneToMany(targetEntity="Customer",mappedBy="responsable")
     */
    private $customers;

    /**
     * @var array
     *
     * @ORM\Column(name="departement", type="array")
     */
    private $departement;


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
     * Set codeResponsable
     *
     * @param string $codeResponsable
     *
     * @return SettingsResp
     */
    public function setCodeResponsable($codeResponsable)
    {
        $this->codeResponsable = $codeResponsable;
    
        return $this;
    }

    /**
     * Get codeResponsable
     *
     * @return string 
     */
    public function getCodeResponsable()
    {
        return $this->codeResponsable;
    }

    /**
     * Set departement
     *
     * @param array $departement
     *
     * @return SettingsResp
     */
    public function setDepartement($departement)
    {
        $this->departement = $departement;
    
        return $this;
    }

    /**
     * Get departement
     *
     * @return array 
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }


    public function addCustomer(Customer $customer)
    {
        $this->customers[] = $customer;
    }

    public function __toString()
    {
        return $this->getCodeResponsable();
    }
}

