<?php

namespace BackOffice\MonitoringBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodeOperation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BackOffice\MonitoringBundle\Entity\CodeOperationRepository")
 */
class CodeOperation
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
     * @ORM\Column(name="code", type="string", length=3)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="typePresence", type="boolean", nullable=true)
     */
    private $typePresence;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;


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
     * @return CodeOperation
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return CodeOperation
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set typePresence
     *
     * @param boolean $typePresence
     *
     * @return CodeOperation
     */
    public function setTypePresence($typePresence)
    {
        $this->typePresence = $typePresence;
    
        return $this;
    }

    /**
     * Get typePresence
     *
     * @return boolean
     */
    public function getTypePresence()
    {
        return $this->typePresence;
    }
}
