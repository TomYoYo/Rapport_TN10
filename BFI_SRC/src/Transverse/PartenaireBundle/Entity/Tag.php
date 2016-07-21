<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table("TRANSVERSE_TAG")
 * @ORM\Entity
 */
class Tag
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
     * @ORM\Column(name="nomTag", type="string", length=255, nullable=true)
     */
    private $nomTag;

    /**
     * @var string
     *
     * @ORM\Column(name="mailTag", type="string", length=255)
     */
    private $mailTag;


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
     * Set nomTag
     *
     * @param string $nomTag
     *
     * @return Tag
     */
    public function setNomTag($nomTag)
    {
        $this->nomTag = $nomTag;
    
        return $this;
    }

    /**
     * Get nomTag
     *
     * @return string 
     */
    public function getNomTag()
    {
        return $this->nomTag;
    }

    /**
     * Set mailTag
     *
     * @param string $mailTag
     *
     * @return Tag
     */
    public function setMailTag($mailTag)
    {
        $this->mailTag = $mailTag;
    
        return $this;
    }

    /**
     * Get mailTag
     *
     * @return string 
     */
    public function getMailTag()
    {
        return $this->mailTag;
    }
}
