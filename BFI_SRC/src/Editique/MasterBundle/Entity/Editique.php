<?php

namespace Editique\MasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Editique
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Editique\MasterBundle\Entity\EditiqueRepository")
 */
class Editique
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
     * @ORM\Column(name="idClient", type="string", length=10)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="typeDoc", type="string", length=30)
     */
    private $typeDoc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtGeneration", type="datetime")
     */
    private $dtGeneration;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUtilisateur", type="integer")
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="filePath", type="string", length=255)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="numCpt", type="string", length=30)
     */
    private $numCpt;

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
     * @return Editique
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
     * Set typeDoc
     *
     * @param string $typeDoc
     *
     * @return Editique
     */
    public function setTypeDoc($typeDoc)
    {
        $this->typeDoc = $typeDoc;

        return $this;
    }

    /**
     * Get typeDoc
     *
     * @return string
     */
    public function getTypeDoc()
    {
        return $this->typeDoc;
    }
    
    /**
     * Get typeDoc
     *
     * @return string
     */
    public function getTypeDocToString()
    {
        switch ($this->typeDoc) {
            case 'dat':
                return 'DAT';
            case 'echeancier':
                return 'Echéancier';
            case 'lettre_chequier':
                return 'Lettre chéquier';
            case 'mdp':
                return 'Lettre mot de passe';
            case 'livret':
                return 'Livret';
            case 'releve':
                return 'Relevé de compte mensuel';
            case 'releve_quotidien':
                return 'Relevé de compte quotidien';
            case 'rib':
                return 'RIB';
            case 'club':
                return 'Lettre adhésion club';
            case 'souscription':
                return 'Souscription de compte courant';
            case 'souscription_credit':
                return 'Souscription de crédit';
            default:
                return $this->typeDoc;
        }
    }

    /**
     * Set dtGeneration
     *
     * @param \DateTime $dtGeneration
     *
     * @return Editique
     */
    public function setDtGeneration($dtGeneration)
    {
        $this->dtGeneration = $dtGeneration;

        return $this;
    }

    /**
     * Get dtGeneration
     *
     * @return \DateTime
     */
    public function getDtGeneration()
    {
        return $this->dtGeneration;
    }

    /**
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return Editique
     */
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur
     *
     * @return integer
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Set filePath
     *
     * @param string $filePath
     *
     * @return Editique
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set numCpt
     *
     * @param string $numCpt
     *
     * @return Editique
     */
    public function setNumCpt($numCpt)
    {
        $this->numCpt = $numCpt;

        return $this;
    }

    /**
     * Get numCpt
     *
     * @return string
     */
    public function getNumCpt()
    {
        return $this->numCpt;
    }

    public function getFileName()
    {
        return basename($this->filePath);
    }
}
