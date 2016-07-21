<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Spool
 *
 * @ORM\Table("TRANSVERSE_SPOOL")
 * @ORM\Entity
 */
class Spool
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
     * @ORM\Column(name="UrlSpool", type="string", length=255)
     */
    private $urlSpool;

    
    /**
    * @var string
    *
    * @ORM\Column(name="nomSpool", type="string", length=255)
    */
    private $nomSpool;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="dateSpool", type="integer")
     */
    private $dateSpool;

   /**
    * @ORM\ManyToMany(targetEntity="Transverse\PartenaireBundle\Entity\Parametrage", mappedBy="spools")
    */
    private $parametrages;
    

    public function __construct()
    {
        $this->parametrages = new ArrayCollection();
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
     * Set urlSpool
     *
     * @param string $urlSpool
     *
     * @return Spool
     */
    public function setUrlSpool($urlSpool)
    {
        $this->urlSpool = $urlSpool;
    
        return $this;
    }

    
    /**
     * Get urlSpool
     *
     * @return string 
     */
    public function getUrlSpool()
    {
        return $this->urlSpool;
    }

    
    /**
     * Set nomSpool
     *
     * @param string $nomSpool
     *
     * @return Spool
     */
    public function setNomSpool($nomSpool)
    {
        $this->nomSpool = $nomSpool;
    
        return $this;
    }

    
    /**
     * Get nomSpool
     *
     * @return string 
     */
    public function getNomSpool()
    {
        return $this->nomSpool;
    }
    
    /**
     * Set dateSpool
     *
     * 
     *
     * @return Spool
     */
    public function setDateSpool($dateSpool)
    {
        $this->dateSpool = $dateSpool;
    
        return $this;
    }

    /**
     * Get dateSpool
     *
     * @return integer 
     */
    public function getDateSpool()
    {
        return $this->dateSpool;
    }
    
    
    public function getParametrages()
    {
        return $this->parametrages;
    }
}
