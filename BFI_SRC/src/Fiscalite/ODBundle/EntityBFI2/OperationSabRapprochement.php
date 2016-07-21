<?php

namespace Fiscalite\ODBundle\EntityBFI2;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table("ZMOUANA0")
 * @ORM\Entity(repositoryClass="Fiscalite\ODBundle\EntityBFI2\OperationSabRapprochementRepository")
 */
class OperationSabRapprochement
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
     * @ORM\Column(name="MOUANACOM", type="string", length=20)
     */
    private $numcompte;

    /**
     * @var string
     *
     * @ORM\Column(name="MOUANAOPE", type="string", length=3)
     */
    private $codeop;

     /**
     * @var string
     *
     * @ORM\Column(name="MOUANAEVE", type="string", length=3)
     */
    private $codevenement;
    
    /**
     * @var float
     *
     * @ORM\Column(name="MOUANAMON", type="float")
     */
    private $montantmouvement;

    /**
     * @var string
     *
     * @ORM\Column(name="MOUANADOP", type="string", length=7)
     */
    private $dateoperation;
    
     /**
     * @var string
     *
     * @ORM\Column(name="MOUANADCO", type="string", length=7)
     */
    private $datecomptable;
    
    
     /**
     * @var integer
     *
     * @ORM\Column(name="MOUANAPIE", type="integer")
     */
    private $numeropiece;
    
    
     /**
     * @var integer
     *
     * @ORM\Column(name="MOUANANUM", type="integer")
     */
    private $numerooperation;
   

     /**
     * @var integer
     *
     * @ORM\Column(name="MOUANAECR", type="integer")
     */
    private $numeroecriture;
    
    

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
     * Set numcompte
     *
     * @param string $numcompte
     *
     * @return OperationSabRapprochement
     */
    public function setNumcompte($numcompte)
    {
        $this->numcompte = $numcompte;
    
        return $this;
    }

    /**
     * Get numcompte
     *
     * @return string 
     */
    public function getNumcompte()
    {
        return $this->numcompte;
    }

    /**
     * Set codeop
     *
     * @param string $codeop
     *
     * @return OperationSabRapprochement
     */
    public function setCodeop($codeop)
    {
        $this->codeop = $codeop;
    
        return $this;
    }

    /**
     * Get codeop
     *
     * @return string 
     */
    public function getCodeop()
    {
        return $this->codeop;
    }

    /**
     * Set codevenement
     *
     * @param string $codevenement
     *
     * @return OperationSabRapprochement
     */
    public function setCodevenement($codevenement)
    {
        $this->codevenement = $codevenement;
    
        return $this;
    }

    /**
     * Get codevenement
     *
     * @return string 
     */
    public function getCodevenement()
    {
        return $this->codevenement;
    }

    /**
     * Set montantmouvement
     *
     * @param integer $montantmouvement
     *
     * @return OperationSabRapprochement
     */
    public function setMontantmouvement($montantmouvement)
    {
        $this->montantmouvement = $montantmouvement;
    
        return $this;
    }

    /**
     * Get montantmouvement
     *
     * @return integer 
     */
    public function getMontantmouvement()
    {
        return $this->montantmouvement;
    }

    /**
     * Set dateoperation
     *
     * @param integer $dateoperation
     *
     * @return OperationSabRapprochement
     */
    public function setDateoperation($dateoperation)
    {
        $this->dateoperation = $dateoperation;
    
        return $this;
    }

    /**
     * Get dateoperation
     *
     * @return integer 
     */
    public function getDateoperation()
    {
        return $this->dateoperation;
    }

    /**
     * Set datecomptable
     *
     * @param integer $datecomptable
     *
     * @return OperationSabRapprochement
     */
    public function setDatecomptable($datecomptable)
    {
        $this->datecomptable = $datecomptable;
    
        return $this;
    }

    /**
     * Get datecomptable
     *
     * @return integer 
     */
    public function getDatecomptable()
    {
        return $this->datecomptable;
    }

    /**
     * Set numeropiece
     *
     * @param integer $numeropiece
     *
     * @return OperationSabRapprochement
     */
    public function setNumeropiece($numeropiece)
    {
        $this->numeropiece = $numeropiece;
    
        return $this;
    }

    /**
     * Get numeropiece
     *
     * @return integer 
     */
    public function getNumeropiece()
    {
        return $this->numeropiece;
    }
    
     /**
     * Set numerooperation
     *
     * @param integer $numerooperation
     *
     * @return OperationSabRapprochement
     */
    public function setNumerooperation($numerooperation)
    {
        $this->numerooperation = $numerooperation;
    
        return $this;
    }

    /**
     * Get numerooperation
     *
     * @return integer 
     */
    public function getNumerooperation()
    {
        return $this->numerooperation;
    }
    
    
    /**
     * Set numeroecriture
     *
     * @param integer $numeroecriture
     *
     * @return OperationSabRapprochement
     */
    public function setNumeroecriture($numeroecriture)
    {
        $this->numeroecriture = $numeroecriture;
    
        return $this;
    }

    /**
     * Get numeroecriture
     *
     * @return integer 
     */
    public function getNumeroecriture()
    {
        return $this->numeroecriture;
    }
}
