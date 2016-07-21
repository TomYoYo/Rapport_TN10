<?php

namespace Editique\LettreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chequier
 *
 * @ORM\Table(name="ZCHQHIS0")
 * @ORM\Entity()
 */
class Chequier
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
     * @ORM\Column(name="CHQHISTIT", type="string", length=7)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="CHQHISCOM", type="string", length=20)
     */
    private $numCompte;


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
     * Set idClient
     *
     * @param string $idClient
     *
     * @return Chequier
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;
    
        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }
    
    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClientRemastered()
    {
        $idClientRemastered = ' '.$this->idClient;
        while (strlen($idClientRemastered) < 20) {
            $idClientRemastered = $idClientRemastered. ' ';
        }
        
        return $idClientRemastered;
    }

    /**
     * Set numCompte
     *
     * @param string $numCompte
     *
     * @return Chequier
     */
    public function setNumCompte($numCompte)
    {
        $this->numCompte = $numCompte;
    
        return $this;
    }

    /**
     * Get numCompte
     *
     * @return string
     */
    public function getNumCompte()
    {
        return $this->numCompte;
    }
}
