<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filtre
 *
 * @ORM\Table("TRANSVERSE_FILTRE")
 * @ORM\Entity
 */
class Filtre
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
     * @ORM\Column(name="ExpressionAFiltrer", type="string", length=255)
     */
    private $expressionAFiltrer;
    

    

    /**
     * @ORM\ManyToOne(targetEntity="Transverse\PartenaireBundle\Entity\Parametrage", inversedBy="filtres", cascade={"persist"})
     * @ORM\JoinColumn(name="parametrage_id", referencedColumnName="id")
     */
    private $parametrage;
    
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
     * Set expressionAFiltrer
     *
     * @param string $expressionAFiltrer
     *
     * @return Filtre
     */
    public function setExpressionAFiltrer($expressionAFiltrer)
    {
        $this->expressionAFiltrer = $expressionAFiltrer;
        return $this;
    }

    /**
     * Get expressionAFiltrer
     *
     * @return string 
     */
    public function getExpressionAFiltrer()
    {
        return $this->expressionAFiltrer;
    }
    
    
    public function setParametrage(Parametrage $parametrage)
    {
        $this->parametrage = $parametrage;
        return $this;
    }


    public function getParametrage()
    {
        return $this->parametrage;
    }
}
