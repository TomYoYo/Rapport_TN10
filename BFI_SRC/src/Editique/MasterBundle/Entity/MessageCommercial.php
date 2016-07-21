<?php

namespace Editique\MasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageCommercial
 *
 * @ORM\Table("Editique_Message_Commercial")
 * @ORM\Entity(repositoryClass="Editique\MasterBundle\Entity\MessageCommercialRepository")
 */
class MessageCommercial
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
     * @ORM\Column(name="message", type="string", length=110)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="datetime")
     */
    private $dateFin;

    /**
     * @ORM\ManyToOne(targetEntity="\BackOffice\UserBundle\Entity\Profil", cascade={"persist"})
     * @ORM\JoinColumn(name="createur", referencedColumnName="id")
     */
    protected $createur;

    /**
     * @ORM\ManyToOne(targetEntity="\BackOffice\UserBundle\Entity\Profil", cascade={"persist"})
     * @ORM\JoinColumn(name="editeur", referencedColumnName="id")
     */
    protected $editeur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;


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
     * Set message
     *
     * @param string $message
     *
     * @return MessageCommercial
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return MessageCommercial
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return MessageCommercial
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return MessageCommercial
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return MessageCommercial
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set createur
     *
     * @param \BackOffice\UserBundle\Entity\Profil $createur
     *
     * @return MessageCommercial
     */
    public function setCreateur(\BackOffice\UserBundle\Entity\Profil $createur = null)
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * Get createur
     *
     * @return \BackOffice\UserBundle\Entity\Profil
     */
    public function getCreateur()
    {
        return $this->createur;
    }

    /**
     * Set editeur
     *
     * @param \BackOffice\UserBundle\Entity\Profil $editeur
     *
     * @return MessageCommercial
     */
    public function setEditeur(\BackOffice\UserBundle\Entity\Profil $editeur = null)
    {
        $this->editeur = $editeur;

        return $this;
    }

    /**
     * Get editeur
     *
     * @return \BackOffice\UserBundle\Entity\Profil
     */
    public function getEditeur()
    {
        return $this->editeur;
    }

    public function isActive()
    {
        $now = new \DateTime();
        if ($now >= $this->dateDebut && $now <= $this->dateFin) {
            return true ;
        }

        return false;
    }
}
