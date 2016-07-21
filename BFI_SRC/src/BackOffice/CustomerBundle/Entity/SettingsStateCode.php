<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SettingsStateCode
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SettingsStateCode
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
     * @ORM\OneToMany(targetEntity="Customer",mappedBy="codeEtat")
     */
    private $customers;

    /**
     * @var array
     *
     * @ORM\Column(name="juridiqueForme", type="array",nullable = true)
     */
    private $juridiqueForme;

    /**
     * @var string
     *
     * @ORM\Column(name="abrege", type="string", length=4)
     */
    private $abrege;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
       return $this->getIntitule();
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return SettingsStateCode
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
     * Set juridiqueForme
     *
     * @param array $juridiqueForme
     *
     * @return SettingsStateCode
     */
    public function setJuridiqueForme($juridiqueForme)
    {
        $this->juridiqueForme = $juridiqueForme;
    
        return $this;
    }

    /**
     * Get juridiqueForme
     *
     * @return array 
     */
    public function getJuridiqueForme()
    {
        return $this->juridiqueForme;
    }

    /**
     * Set abrege
     *
     * @param string $abrege
     *
     * @return SettingsStateCode
     */
    public function setAbrege($abrege)
    {
        $this->abrege = $abrege;
    
        return $this;
    }

    /**
     * Get abrege
     *
     * @return string 
     */
    public function getAbrege()
    {
        return $this->abrege;
    }

    public function addForme($forme)
    {
        if($this->juridiqueForme == null)
        {
            $this->juridiqueForme = array();
        }
        if(!in_array($forme,$this->juridiqueForme))
        {
            $this->juridiqueForme[] = $forme;
        }
        else
        {
            return false;
        }

        return $this;
    }

    public function delForme($forme)
    {
        unset($this->juridiqueForme[array_search($forme,$this->juridiqueForme)]);
    }
}

