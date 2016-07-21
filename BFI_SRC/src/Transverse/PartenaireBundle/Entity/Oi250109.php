<?php

namespace Transverse\PartenaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transverse\PartenaireBundle\Entity\Oi2504;

/**
 * OI25_0109
 *
 * @ORM\Table("TRANSVERSE_OI25_0109")
 * @ORM\Entity
 */
class Oi250109
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
     * @ORM\Column(name="nomFichier", type="string", length=8, nullable=true)
     */
    private $nomFichier;

    /**
     * @var string
     *
     * @ORM\Column(name="nomFichierUrl", type="string", length=255, nullable=true)
     */
    private $nomFichierUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="dateJourneeEchange", type="string", length=8, nullable=true)
     */
    private $dateJourneeEchange;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroSequenceFichier", type="string", length=6, nullable=true)
     */
    private $numeroSequenceFichier;

    /**
     * @var string
     *
     * @ORM\Column(name="idEtablissementDestinataire", type="string", length=5, nullable=true)
     */
    private $idEtablissementDestinataire;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreOperationDebit", type="string", length=255, nullable=true)
     */
    private $nombreOperationDebit;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreOperationCredit", type="string", length=255, nullable=true)
     */
    private $nombreOperationCredit;



    /**
     * @var string
     *
     * @ORM\Column(name="cumulMontantDebit", type="string", length=255, nullable=true)
     */
    private $cumulMontantDebit;

    /**
     * @var string
     *
     * @ORM\Column(name="cumulMontantCredit", type="string", length=255, nullable=true)
     */
    private $cumulMontantCredit;


    /**
     * @ORM\OneToMany(targetEntity="Oi2504", mappedBy="OI250109", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $oI2504s;

    public function __construct()
    {
        $this->oI2504s = new ArrayCollection();
    }



    public function addOi2504(Oi2504 $OI2504)
    {
        $this->oI2504s[] = $OI2504;
        $OI2504->setOi250109($this);
        return $this;
    }

    public function removeOi2504(Oi2504 $OI2504)
    {
        $this->oI2504s->removeElement($OI2504);
        $OI2504->setOi250109(null);
    }



    public function getoI2504s()
    {
        return $this->oI2504s;
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
     * Set nomFichier
     *
     * @param string $nomFichier
     *
     * @return Oi250109
     */
    public function setNomFichier($nomFichier)
    {
        $this->nomFichier = $nomFichier;
    
        return $this;
    }

    /**
     * Get nomFichier
     *
     * @return string 
     */
    public function getNomFichier()
    {
        return $this->nomFichier;
    }


    /**
     * Set nomFichierURL
     *
     * @param string $nomFichierURL
     *
     * @return Oi250109
     */
    public function setNomFichierUrl($nomFichierUrl)
    {
        $this->nomFichierUrl = $nomFichierUrl;

        return $this;
    }

    /**
     * Get nomFichierURL
     *
     * @return string
     */
    public function getNomFichierUrl()
    {
        return $this->nomFichierUrl;
    }

    /**
     * Set dateJourneeEchange
     *
     * @param string $dateJourneeEchange
     *
     * @return OI25_0109
     */
    public function setDateJourneeEchange($dateJourneeEchange)
    {
        $this->dateJourneeEchange = $dateJourneeEchange;
    
        return $this;
    }

    /**
     * Get dateJourneeEchange
     *
     * @return \DateTime 
     */
    public function getDateJourneeEchange()
    {
        return $this->dateJourneeEchange;
    }

    /**
     * Set numeroSequenceFichier
     *
     * @param string $numeroSequenceFichier
     *
     * @return OI25_0109
     */
    public function setNumeroSequenceFichier($numeroSequenceFichier)
    {
        $this->numeroSequenceFichier = $numeroSequenceFichier;
    
        return $this;
    }

    /**
     * Get numeroSequenceFichier
     *
     * @return string 
     */
    public function getNumeroSequenceFichier()
    {
        return $this->numeroSequenceFichier;
    }

    /**
     * Set idEtablissementDestinataire
     *
     * @param string $idEtablissementDestinataire
     *
     * @return OI25_0109
     */
    public function setIdEtablissementDestinataire($idEtablissementDestinataire)
    {
        $this->idEtablissementDestinataire = $idEtablissementDestinataire;
    
        return $this;
    }

    /**
     * Get idEtablissementDestinataire
     *
     * @return string 
     */
    public function getIdEtablissementDestinataire()
    {
        return $this->idEtablissementDestinataire;
    }

    /**
     * Set nombreOperationDebit
     *
     * @param string $nombreOperationDebit
     *
     * @return OI25_0109
     */
    public function setNombreOperationDebit($nombreOperationDebit)
    {
        $this->nombreOperationDebit = $nombreOperationDebit;
    
        return $this;
    }

    /**
     * Get nombreOperationDebit
     *
     * @return string 
     */
    public function getNombreOperationDebit()
    {
        return $this->nombreOperationDebit;
    }

    /**
     * Set nombreOperationCredit
     *
     * @param string $nombreOperationCredit
     *
     * @return OI25_0109
     */
    public function setNombreOperationCredit($nombreOperationCredit)
    {
        $this->nombreOperationCredit = $nombreOperationCredit;
    
        return $this;
    }

    /**
     * Get nombreOperationCredit
     *
     * @return string 
     */
    public function getNombreOperationCredit()
    {
        return $this->nombreOperationCredit;
    }



    /**
     * Get montantImputeTotalDebit
     *
     * @return string 
     */
    public function getMontantImputeTotalDebit()
    {
        return $this->montantImputeTotalDebit;
    }


    /**
     * Get montantImputeTotalCredit
     *
     * @return string 
     */
    public function getMontantImputeTotalCredit()
    {
        return $this->montantImputeTotalCredit;
    }

    /**
     * Set cumulMontantDebit
     *
     * @param string $cumulMontantDebit
     *
     * @return OI25_0109
     */
    public function setCumulMontantDebit($cumulMontantDebit)
    {
        $this->cumulMontantDebit = $cumulMontantDebit;
    
        return $this;
    }

    /**
     * Get cumulMontantDebit
     *
     * @return string 
     */
    public function getCumulMontantDebit()
    {
        return $this->cumulMontantDebit;
    }

    /**
     * Set cumulMontantCredit
     *
     * @param string $cumulMontantCredit
     *
     * @return OI25_0109
     */
    public function setCumulMontantCredit($cumulMontantCredit)
    {
        $this->cumulMontantCredit = $cumulMontantCredit;
    
        return $this;
    }

    /**
     * Get cumulMontantCredit
     *
     * @return string 
     */
    public function getCumulMontantCredit()
    {
        return $this->cumulMontantCredit;
    }
}

