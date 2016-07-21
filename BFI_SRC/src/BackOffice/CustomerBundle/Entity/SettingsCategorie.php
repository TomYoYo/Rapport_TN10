<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SettingsCategorie
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SettingsCategorie
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
     * @ORM\Column(name="code", type="string", length=3,unique=true)
     */
    private $code;

    /**
     * @var array
     *
     * @ORM\Column(name="formes", type="array",nullable = true)
     */
    private $formes;

    /**
     * @ORM\OneToMany(targetEntity="Customer",mappedBy="juridique")
     */
    private $customers;


    public function __construct()
    {
        $this->formes = array();
        $this->customers = new ArrayCollection();

    }

    public function addCustomer(Customer $customer)
    {
        $this->customers[] = $customer;
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

    /**
     * Set code
     *
     * @param string $code
     *
     * @return SettingsCategorie
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
     * Set formes
     *
     * @param array $formes
     *
     * @return SettingsCategorie
     */
    public function setFormes($formes)
    {
        $this->formes = $formes;
    
        return $this;
    }

    /**
     * Get formes
     *
     * @return array 
     */
    public function getFormes()
    {
        return $this->formes;
    }

    /**
     * add Forme
     * @param string $forme
     */
    public function addForme($forme)
    {
        if($this->formes == null)
        {
            $this->formes = array();
        }
        if(!in_array($forme,$this->formes))
        {
            $this->formes[] = $forme;
        }
        else
        {
            return false;
        }

        return $this;
    }

    public function delForme($forme)
    {
        unset($this->formes[array_search($forme,$this->formes)]);
    }
}

