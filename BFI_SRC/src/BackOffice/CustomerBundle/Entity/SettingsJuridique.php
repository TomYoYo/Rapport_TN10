<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SettingsJuridique
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SettingsJuridique
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
     * @ORM\Column(name="intitule", type="string", length=255)
     */
    private $intitule;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=4,unique = true)
     */
    private $code;

    /**
     * @var array
     *
     * @ORM\Column(name="nace", type="array")
     */
    private $nace;

    /**
     * @ORM\OneToMany(targetEntity="Customer",mappedBy="juridique")
     */
    private $customers;


    public function __construct()
    {
        $this->nace = array();
        $this->customers = new ArrayCollection();

    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    public function addCustomer(Customer $customer)
    {
        $this->customers[] = $customer;
    }


    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return SettingsJuridique
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;
    
        return $this;
    }

    /**
     * Get intitule
     *
     * @return string 
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return SettingsJuridique
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set nace
     *
     * @param array $nace
     *
     * @return SettingsJuridique
     */
    public function setNace($nace)
    {
        $this->nace = $nace;
    
        return $this;
    }

    /**
     * Get nace
     *
     * @return array 
     */
    public function getNace()
    {
        return $this->nace;
    }

    public function addNace($nace)
    {
        if($this->nace == null)
        {
            $this->nace = array();
        }
        if(!in_array($nace,$this->nace))
        {
            $this->nace[] = $nace;
        }
        else
        {
            return false;
        }

        return $this;
    }

    public function delnace($nace)
    {
        unset($this->nace[array_search($nace,$this->nace)]);
    }
}

