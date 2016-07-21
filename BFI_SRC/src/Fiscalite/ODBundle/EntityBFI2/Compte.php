<?php

namespace Fiscalite\ODBundle\EntityBFI2;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table("ZCOMPTE0")
 * @ORM\Entity(repositoryClass="Fiscalite\ODBundle\EntityBFI2\CompteRepository")
 */
class Compte
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
     * @ORM\Column(name="COMPTECOM", type="string", length=6)
     */
    private $num;

    /**
     * @var string
     *
     * @ORM\Column(name="COMPTEINT", type="string", length=20)
     */
    private $lib;


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
     * Set num
     *
     * @param string $num
     *
     * @return Compte
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return string
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set lib
     *
     * @param string $lib
     *
     * @return Compte
     */
    public function setLib($lib)
    {
        $this->lib = $lib;

        return $this;
    }

    /**
     * Get lib
     *
     * @return string
     */
    public function getLib()
    {
        return $this->lib;
    }
}
