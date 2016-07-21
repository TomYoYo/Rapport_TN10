<?php

namespace Fiscalite\ODBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statut
 *
 * @ORM\Table("OD_Statut")
 * @ORM\Entity()
 */
class Statut
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_statut", type="string", length=3)
     * @ORM\Id
     */
    private $idStatut;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_statut", type="string", length=20)
     */
    private $libelleStatut;

    /**
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="statut", cascade={"remove", "persist"})
     */
    protected $operations;

    /**
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="statutPrec", cascade={"remove", "persist"})
     */
    protected $operationsPrecedentes;

    /**
     * Set idStatut
     *
     * @param string $idStatut
     * @return Statut
     */
    public function setIdStatut($idStatut)
    {
        $this->idStatut = $idStatut;

        return $this;
    }

    /**
     * Get idStatut
     *
     * @return string
     */
    public function getIdStatut()
    {
        return $this->idStatut;
    }

    /**
     * Set libelleStatut
     *
     * @param string $libelleStatut
     * @return Statut
     */
    public function setLibelleStatut($libelleStatut)
    {
        $this->libelleStatut = $libelleStatut;

        return $this;
    }

    /**
     * Get libelleStatut
     *
     * @return string
     */
    public function getLibelleStatut()
    {
        return $this->libelleStatut;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }

    public function __toString()
    {
        return $this->libelleStatut;
    }
}
