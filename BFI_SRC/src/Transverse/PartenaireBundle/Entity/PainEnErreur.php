<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table("TRANSVERSE_PAIN")
 * @ORM\Entity
 */
class PainEnErreur
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
     * @ORM\Column(name="urlPain", type="string", length=255)
     */
    private $urlPain;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePain", type="datetime")
     */
    private $datePain;

    /**
     * @var array
     *
     * @ORM\Column(name="BicsEnErreur", type="array")
     */
    private $BicsEnErreur;


    public function __construct()
    {
        $this->datePain = new \DateTime();
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
     * Set urlPain
     *
     * @param string $urlPain
     *
     * @return PainEnErreur
     */
    public function setUrlPain($urlPain)
    {
        $this->urlPain = $urlPain;

        return $this;
    }


    /**
     * Get urlPain
     *
     * @return string
     */
    public function getUrlPain()
    {
        return $this->urlPain;
    }


    /**
     * Set datePain
     *
     *
     * @return PainEnErreur
     */
    public function setDatePain($datePain)
    {
        $this->datePain = new \Datetime();

        return $this;
    }

    /**
     * Get datePain
     *
     * @return \DateTime
     */
    public function getDatePain()
    {
        return $this->datePain;
    }

    /**
     * Set BicsEnErreur
     *
     * @param array $BicsEnErreur
     *
     * @return PainEnErreur
     */
    public function setBicsEnErreur($BicsEnErreur)
    {
        $this->BicsEnErreur = $BicsEnErreur;

        return $this;
    }

    /**
     * Get BicsEnErreur
     *
     * @return array
     */
    public function getBicsEnErreur()
    {
        return $this->BicsEnErreur;
    }
}
