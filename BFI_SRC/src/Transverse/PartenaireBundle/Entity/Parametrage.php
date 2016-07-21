<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Parametrage
 *
 * @ORM\Table("TRANSVERSE_PARAMETRAGE")
 * @ORM\Entity
 */
class Parametrage
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
     * @var integer
     *
     * @ORM\Column(name="priorite", type="smallint")
     */
    private $priorite;

    /**
     * @var string
     *
     * @ORM\Column(name="titreParam", type="string", length=255)
     */
    private $titreParam;
    

    /**
     * @var boolean
     *
     * @ORM\Column(name="isEmailChecked", type="boolean", nullable=true)
     */
    private $isEmailChecked;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;


    /**
     * @var string
     *
     * @ORM\Column(name="prefixeSpool", type="string", length=255)
     */
    private $prefixeSpool;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedAt", type="datetime")
     */
    private $publishedAt;
    
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isFiltreIncluded", type="boolean")
     */
    private $isFiltreIncluded;
    
    
    
    
    /**
    * @ORM\ManyToMany(targetEntity="Transverse\PartenaireBundle\Entity\Tag", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $tags;
    
    /**
    * @ORM\ManyToMany(targetEntity="Transverse\PartenaireBundle\Entity\Role", cascade={"persist"})
    */
    private $roles;

    /**
    * @ORM\ManyToMany(targetEntity="Transverse\PartenaireBundle\Entity\Spool", inversedBy="parametrages", cascade={"persist"})
    * @ORM\JoinTable(name="parametrage_spool")
    */
    private $spools;
    
    /**
    * @ORM\OneToMany(targetEntity="Transverse\PartenaireBundle\Entity\Filtre", mappedBy="parametrage", cascade={"persist", "remove"}, orphanRemoval=true)
    */
    private $filtres;
    
    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->tags = new ArrayCollection();
        $this->filtres = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->spools = new ArrayCollection();
    }

    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }
    
    public function addFiltre(Filtre $filtre)
    {
        $this->filtres[] = $filtre;
        $filtre->setParametrage($this);
        return $this;
    }

    public function removeFiltre(Filtre $filtre)
    {
        $this->filtres->removeElement($filtre);
        $filtre->setParametrage(null);
    }
    
    
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
        return $this;
    }
    
    public function addSpool(Spool $spool)
    {
        $this->spools[] = $spool;
        return $this;
    }

    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }

    
    public function getTags()
    {
        return $this->tags;
    }
    
    public function getRoles()
    {
        return $this->roles;
    }

    public function getSpools()
    {
        return $this->spools;
    }
    
    public function getFiltres()
    {
        return $this->filtres;
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
     * Set priorite
     *
     * @param integer $priorite
     *
     * @return Parametrage
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;
    
        return $this;
    }

    /**
     * Get priorite
     *
     * @return integer 
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set titreParam
     *
     * @param string $titreParam
     *
     * @return Parametrage
     */
    public function setTitreParam($titreParam)
    {
        $this->titreParam = $titreParam;
    
        return $this;
    }

    /**
     * Get titreParam
     *
     * @return string 
     */
    public function getTitreParam()
    {
        return $this->titreParam;
    }
    
    

    /**
     * Set isEmailChecked
     *
     * @param boolean $isEmailChecked
     *
     * @return Parametrage
     */
    public function setIsEmailChecked($isEmailChecked)
    {
        $this->isEmailChecked = $isEmailChecked;
    
        return $this;
    }

    /**
     * Get isEmailChecked
     *
     * @return boolean 
     */
    public function getIsEmailChecked()
    {
        return $this->isEmailChecked;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Parametrage
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    
        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string 
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }



    /**
     * Set prefixeSpool
     *
     * @param string $prefixeSpool
     *
     * @return Parametrage
     */
    public function setPrefixeSpool($prefixeSpool)
    {
        $this->prefixeSpool = $prefixeSpool;
    
        return $this;
    }

    /**
     * Get prefixeSpool
     *
     * @return string 
     */
    public function getPrefixeSpool()
    {
        return $this->prefixeSpool;
    }
    
    
    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Post
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }


    public function getPublishedAt()
    {
        return $this->publishedAt;
    }
    
    
        /**
     * Set isIncluded
     *
     * @param boolean $isFiltreIncluded
     *
     * @return Parametrage
     */
    public function setIsFiltreIncluded($isFiltreIncluded)
    {
        $this->isFiltreIncluded = $isFiltreIncluded;
    
        return $this;
    }

    /**
     * Get isFiltreIncluded
     *
     * @return boolean 
     */
    public function getIsFiltreIncluded()
    {
        return $this->isFiltreIncluded;
    }
}
