<?php

namespace BackOffice\CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mageekguy\atoum\asserters\integer;

/**
 * Anomalie
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Anomalie
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
     * @ORM\Column(name="idcustomer", type="string", length=255,nullable=false)
     */
    private $idCustomer;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255,nullable=false)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="cause", type="string", length=255,nullable=false)
     */

    private $cause;


    /**
     * @var integer
     *
     * @ORM\Column(name="statut",type="integer",nullable = false)
     */
    private $statut;

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return Anomalie
     */
    public function setstatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getstatut()
    {
        return $this->statut;
    }


    /**
     * Set cause
     *
     * @param string $cause
     *
     * @return Anomalie
     */
    public function setcause($cause)
    {
        $this->cause = $cause;

        return $this;
    }

    /**
     * Get cause
     *
     * @return string
     */
    public function getcause()
    {
        return $this->cause;
    }


    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return Anomalie
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getdesignation()
    {
        return $this->designation;
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
     * Set idCustomer
     *
     * @param string $idCustomer
     *
     * @return Anomalie
     */
    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = $idCustomer;
    
        return $this;
    }

    /**
     * Get idCustomer
     *
     * @return string 
     */
    public function getIdCustomer()
    {
        return $this->idCustomer;
    }
}

