<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 * @ORM\Entity
 * @ORM\Table("SAB139.ZEUPG5C0")
 * @ORM\Entity(repositoryClass="Transverse\PartenaireBundle\Entity\BicRepository")
 *
 */
class Bic
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
     * @ORM\Column(name="EUPG5CBIC", type="string", length=11)
     */
    private $bic;


    /**
     * @var string
     *
     * @ORM\Column(name="EUPG5CINT", type="string", length=11)
     */
    private $bic_intermediaire;


    /**
     * @var string
     *
     * @ORM\Column(name="EUPG5CNAM", type="string", length=105)
     */
    private $nom_institutionel;


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
     * Set bic
     *
     * @param string $bic
     *
     * @return Bic
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set bic_intermediaire
     *
     * @param string $bic_intermediaire
     *
     * @return Bic
     */
    public function setBicIntermediaire($bic_intermediaire)
    {
        $this->bic_intermediaire = $bic_intermediaire;

        return $this;
    }

    /**
     * Get bic_intermediaire
     *
     * @return string
     */
    public function getBicIntermediaire()
    {
        return $this->bic_intermediaire;
    }


    /**
     * Set nom_institutionel
     *
     * @param string $nom_institutionel
     *
     * @return Bic
     */
    public function setNomInstitutionel($nom_institutionel)
    {
        $this->nom_institutionel = $nom_institutionel;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string
     */
    public function getNomInstitutionel()
    {
        return $this->nom_institutionel;
    }
}
