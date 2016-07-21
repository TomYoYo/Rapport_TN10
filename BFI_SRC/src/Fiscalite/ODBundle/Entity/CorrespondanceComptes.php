<?php

namespace Fiscalite\ODBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CorrespondanceComptes
 *
 * @ORM\Table("OD_Correspondance_Comptes")
 * @ORM\Entity(repositoryClass="Fiscalite\ODBundle\Entity\CorrespondanceComptesRepository")
 */
class CorrespondanceComptes
{
    /**
     * @var string
     *
     * Correspond au Compte SAB
     * @ORM\Column(name="num_compte_interne", type="string", length=255)
     */
    private $numCompteInterne;

    /**
     * @var string
     *
     * Correspond au Compte SIRH
     * @ORM\Column(name="num_compte_externe", type="string", length=255)
     * @ORM\Id
     */
    private $numCompteExterne;

    /**
     * Set numCompteInterne
     *
     * @param string $numCompteInterne
     *
     * @return CorrespondanceComptes
     */
    public function setNumCompteInterne($numCompteInterne)
    {
        $this->numCompteInterne = $numCompteInterne;

        return $this;
    }

    /**
     * Get numCompteInterne
     *
     * @return string
     */
    public function getNumCompteInterne()
    {
        return $this->numCompteInterne;
    }

    /**
     * Set numCompteExterne
     *
     * @param string $numCompteExterne
     *
     * @return CorrespondanceComptes
     */
    public function setNumCompteExterne($numCompteExterne)
    {
        $this->numCompteExterne = $numCompteExterne;

        return $this;
    }

    /**
     * Get numCompteExterne
     *
     * @return string
     */
    public function getNumCompteExterne()
    {
        return $this->numCompteExterne;
    }

    public function __toString()
    {
        return $this->numCompteInterne;
    }
}
