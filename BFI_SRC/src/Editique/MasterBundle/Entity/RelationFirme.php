<?php

namespace Editique\MasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelationFirme
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Editique\MasterBundle\Entity\RelationFirmeRepository")
 */
class RelationFirme
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
     * @var Profil
     *
     * @ORM\ManyToOne(targetEntity="BackOffice\UserBundle\Entity\Profil")
     */
    private $idBfi;

    /**
     * @var array
     *
     * @ORM\Column(name="id_tiers", type="array")
     */
    private $idTiers;

    /**
     * @var string
     *
     * @ORM\Column(name="informations", type="string", length=255, nullable=true)
     */
    private $informations;


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
     * Set idTiers
     *
     * @param array $idTiers
     *
     * @return RelationFirme
     */
    public function setIdTiers($idTiers)
    {
        $this->idTiers = $idTiers;
    
        return $this;
    }

    /**
     * Get idTiers
     *
     * @return array
     */
    public function getIdTiers()
    {
        return $this->idTiers;
    }

    /**
     * Set informations
     *
     * @param string $informations
     *
     * @return RelationFirme
     */
    public function setInformations($informations)
    {
        $this->informations = $informations;
    
        return $this;
    }

    /**
     * Get informations
     *
     * @return string
     */
    public function getInformations()
    {
        return $this->informations;
    }

    /**
     * Set idBfi
     *
     * @param \BackOffice\UserBundle\Entity\Profil $idBfi
     *
     * @return RelationFirme
     */
    public function setIdBfi(\BackOffice\UserBundle\Entity\Profil $idBfi = null)
    {
        $this->idBfi = $idBfi;
    
        return $this;
    }

    /**
     * Get idBfi
     *
     * @return \BackOffice\UserBundle\Entity\Profil
     */
    public function getIdBfi()
    {
        return $this->idBfi;
    }
}
