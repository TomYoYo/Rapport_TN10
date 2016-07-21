<?php

namespace Monetique\CardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * P30S
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Monetique\CardBundle\Entity\P30SRepository")
 */
class P30S
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_enr", type="datetime")
     */
    private $dateEnr;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_client", type="integer")
     */
    private $nbClient;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_line", type="integer")
     */
    private $nbLine;

    /**
     * @var string
     *
     * @ORM\Column(name="directory", type="string", length=255)
     */
    private $directory;


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
     * Set dateEnr
     *
     * @param \DateTime $dateEnr
     *
     * @return P30S
     */
    public function setDateEnr($dateEnr)
    {
        $this->dateEnr = $dateEnr;
    
        return $this;
    }

    /**
     * Get dateEnr
     *
     * @return \DateTime
     */
    public function getDateEnr()
    {
        return $this->dateEnr;
    }

    /**
     * Set nbClient
     *
     * @param integer $nbClient
     *
     * @return P30S
     */
    public function setNbClient($nbClient)
    {
        $this->nbClient = $nbClient;
    
        return $this;
    }

    /**
     * Get nbClient
     *
     * @return integer
     */
    public function getNbClient()
    {
        return $this->nbClient;
    }

    /**
     * Set nbLine
     *
     * @param integer $nbLine
     *
     * @return P30S
     */
    public function setNbLine($nbLine)
    {
        $this->nbLine = $nbLine;
    
        return $this;
    }

    /**
     * Get nbLine
     *
     * @return integer
     */
    public function getNbLine()
    {
        return $this->nbLine;
    }

    /**
     * Set directory
     *
     * @param string $directory
     *
     * @return P30S
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    
        return $this;
    }

    /**
     * Get directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
