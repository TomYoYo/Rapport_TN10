<?php

namespace Editique\MasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TauxCredit
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Editique\MasterBundle\Entity\TauxCreditRepository")
 */
class TauxCredit
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
     * @ORM\Column(name="date_val", type="datetime")
     */
    private $dateVal;

    /**
     * @var string
     *
     * @ORM\Column(name="EONIA", type="string", length=255, nullable=true)
     */
    private $eonia;

    /**
     * @var string
     *
     * @ORM\Column(name="EURJ1M", type="string", length=255, nullable=true)
     */
    private $eurj1m;

    /**
     * @var string
     *
     * @ORM\Column(name="EURJ3M", type="string", length=255, nullable=true)
     */
    private $eurj3m;

    /**
     * @var string
     *
     * @ORM\Column(name="EURJ6M", type="string", length=255, nullable=true)
     */
    private $eurj6m;

    /**
     * @var string
     *
     * @ORM\Column(name="EURJ9M", type="string", length=255, nullable=true)
     */
    private $eurj9m;

    /**
     * @var string
     *
     * @ORM\Column(name="EURJ1A", type="string", length=255, nullable=true)
     */
    private $eurj1a;

    /**
     * @var string
     *
     * @ORM\Column(name="date_enr", type="datetime")
     */
    private $dateEnr;
    
    /**
     * @var string
     *
     * @ORM\Column(name="date_edit", type="datetime", nullable=true)
     */
    private $dateEdit;


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
     * Set dateVal
     *
     * @param string $dateVal
     *
     * @return TauxCredit
     */
    public function setDateVal($dateVal)
    {
        $this->dateVal = $dateVal;
    
        return $this;
    }

    /**
     * Get dateVal
     *
     * @return string
     */
    public function getDateVal()
    {
        return $this->dateVal;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return TauxCredit
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set taux
     *
     * @param string $taux
     *
     * @return TauxCredit
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;
    
        return $this;
    }

    /**
     * Get taux
     *
     * @return string
     */
    public function getTaux()
    {
        return $this->taux;
    }

    /**
     * Set dateEnr
     *
     * @param string $dateEnr
     *
     * @return TauxCredit
     */
    public function setDateEnr($dateEnr)
    {
        $this->dateEnr = $dateEnr;
    
        return $this;
    }

    /**
     * Get dateEnr
     *
     * @return string
     */
    public function getDateEnr()
    {
        return $this->dateEnr;
    }

    /**
     * Set eonia
     *
     * @param string $eonia
     *
     * @return TauxCredit
     */
    public function setEonia($eonia)
    {
        $this->eonia = $eonia;
    
        return $this;
    }

    /**
     * Get eonia
     *
     * @return string
     */
    public function getEonia()
    {
        return $this->eonia;
    }

    /**
     * Set eurj1m
     *
     * @param string $eurj1m
     *
     * @return TauxCredit
     */
    public function setEurj1m($eurj1m)
    {
        $this->eurj1m = $eurj1m;
    
        return $this;
    }

    /**
     * Get eurj1m
     *
     * @return string
     */
    public function getEurj1m()
    {
        return $this->eurj1m;
    }

    /**
     * Set eurj3m
     *
     * @param string $eurj3m
     *
     * @return TauxCredit
     */
    public function setEurj3m($eurj3m)
    {
        $this->eurj3m = $eurj3m;
    
        return $this;
    }

    /**
     * Get eurj3m
     *
     * @return string
     */
    public function getEurj3m()
    {
        return $this->eurj3m;
    }

    /**
     * Set eurj6m
     *
     * @param string $eurj6m
     *
     * @return TauxCredit
     */
    public function setEurj6m($eurj6m)
    {
        $this->eurj6m = $eurj6m;
    
        return $this;
    }

    /**
     * Get eurj6m
     *
     * @return string
     */
    public function getEurj6m()
    {
        return $this->eurj6m;
    }

    /**
     * Set eurj9m
     *
     * @param string $eurj9m
     *
     * @return TauxCredit
     */
    public function setEurj9m($eurj9m)
    {
        $this->eurj9m = $eurj9m;
    
        return $this;
    }

    /**
     * Get eurj9m
     *
     * @return string
     */
    public function getEurj9m()
    {
        return $this->eurj9m;
    }

    /**
     * Set eurj1a
     *
     * @param string $eurj1a
     *
     * @return TauxCredit
     */
    public function setEurj1a($eurj1a)
    {
        $this->eurj1a = $eurj1a;
    
        return $this;
    }

    /**
     * Get eurj1a
     *
     * @return string
     */
    public function getEurj1a()
    {
        return $this->eurj1a;
    }

    /**
     * Set dateEdit
     *
     * @param string $dateEdit
     *
     * @return TauxCredit
     */
    public function setDateEdit($dateEdit)
    {
        $this->dateEdit = $dateEdit;
    
        return $this;
    }

    /**
     * Get dateEdit
     *
     * @return string
     */
    public function getDateEdit()
    {
        return $this->dateEdit;
    }
}
