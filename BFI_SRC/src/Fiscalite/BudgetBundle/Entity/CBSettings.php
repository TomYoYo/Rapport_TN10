<?php

namespace Fiscalite\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CBSettings
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CBSettings
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
     * @ORM\Column(name="type", type="string", length=8)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="valueSab", type="string", length=20)
     */
    private $valueSab;

    /**
     * @var string
     *
     * @ORM\Column(name="valueFirme", type="string", length=20)
     */
    private $valueFirme;

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
     * Set type
     *
     * @param string $type
     *
     * @return CBSettings
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
     * Set valueSab
     *
     * @param string $valueSab
     *
     * @return CBSettings
     */
    public function setValueSab($valueSab)
    {
        $this->valueSab = $valueSab;
    
        return $this;
    }

    /**
     * Get valueSab
     *
     * @return string 
     */
    public function getValueSab()
    {
        return $this->valueSab;
    }

    /**
     * Set valueFirme
     *
     * @param string $valueFirme
     *
     * @return CBSettings
     */
    public function setValueFirme($valueFirme)
    {
        $this->valueFirme = $valueFirme;
    
        return $this;
    }

    /**
     * Get valueFirme
     *
     * @return string 
     */
    public function getValueFirme()
    {
        return $this->valueFirme;
    }
}
