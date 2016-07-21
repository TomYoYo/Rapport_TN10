<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SettingsCivility
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SettingsCivility
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
     * @ORM\OneToMany(targetEntity="Customer",mappedBy="codeCivilite")
     */
    private $customers;

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=255)
     */
    private $intitule;

    /**
     * @var string
     *
     * @ORM\Column(name="civilityCode", type="string", length=3)
     */
    private $civilityCode;

    /**
     * @var string
     *
     * @ORM\Column(name="customerCode", type="string", length=4)
     */
    private $customerCode;

    /**
     * @var string
     *
     * @ORM\Column(name="juridiqueForm", type="array",nullable=true)
     */
    private $juridiqueForm;


    public function __construct()
    {
        $this->juridiqueForm = array();
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

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return SettingsCivility
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;
    
        return $this;
    }

    public function addCustomer(Customer $customer)
    {
        $this->customers[] = $customer;
    }

  /*  public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getIntitule();
    }*/

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
     * Set civilityCode
     *
     * @param string $civilityCode
     *
     * @return SettingsCivility
     */
    public function setCivilityCode($civilityCode)
    {
        $this->civilityCode = $civilityCode;
    
        return $this;
    }

    /**
     * Get civilityCode
     *
     * @return string 
     */
    public function getCivilityCode()
    {
        return $this->civilityCode;
    }

    /**
     * Set customerCode
     *
     * @param string $customerCode
     *
     * @return SettingsCivility
     */
    public function setCustomerCode($customerCode)
    {
        $this->customerCode = $customerCode;
    
        return $this;
    }

    /**
     * Get customerCode
     *
     * @return string 
     */
    public function getCustomerCode()
    {
        return $this->customerCode;
    }

    /**
     * Set juridiqueForm
     *
     * @param array $juridiqueForm
     *
     * @return SettingsCivility
     */
    public function setJuridiqueForm($juridiqueForm)
    {
        $this->juridiqueForm = $juridiqueForm;
    
        return $this;
    }

    /**
     * Get juridiqueForm
     *
     * @return array
     */
    public function getJuridiqueForm()
    {
        return $this->juridiqueForm;
    }

    /**
     * add Forme
     * @param string $forme
     */
    public function addForme($forme)
    {
        if($this->juridiqueForm == null)
        {
            $this->juridiqueForm = array();
        }
        if(!in_array($forme,$this->juridiqueForm))
        {
            $this->juridiqueForm[] = $forme;
        }
        else
        {
            return false;
        }

        return $this;
    }

    public function delForme($forme)
    {
            unset($this->juridiqueForm[array_search($forme,$this->juridiqueForm)]);
    }
}

