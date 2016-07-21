<?php

namespace Editique\FiscalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foyer
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Compteur
{
    /**
     * @var string
     *
     * @ORM\Column(name="numFiscal", type="string", length=7)
     * @ORM\Id
     */
    private $numFiscal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ifmpv", type="decimal")
     */
    private $ifmpv;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ee21", type="decimal")
     */
    private $ee21;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ee22", type="decimal")
     */
    private $ee22;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dc2", type="decimal")
     */
    private $dc2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tr2", type="decimal")
     */
    private $tr2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="bh2", type="decimal")
     */
    private $bh2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ca2", type="decimal")
     */
    private $ca2;

    /**
     * @var string
     *
     * @ORM\Column(name="ck2", type="decimal")
     */
    private $ck2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="vg3", type="decimal")
     */
    private $vg3;
    
    /**
     * @var string
     *
     * @ORM\Column(name="vh3", type="decimal")
     */
    private $vh3;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mtcvm", type="decimal")
     */
    private $mtcvm;

    /**
     * Set numFiscal
     *
     * @param string $numFiscal
     *
     * @return Compteur
     */
    public function setNumFiscal($numFiscal)
    {
        $this->numFiscal = $numFiscal;
    
        return $this;
    }

    /**
     * Get numFiscal
     *
     * @return string
     */
    public function getNumFiscal()
    {
        return $this->numFiscal;
    }

    /**
     * Set ifmpv
     *
     * @param string $ifmpv
     *
     * @return Compteur
     */
    public function setIfmpv($ifmpv)
    {
        $this->ifmpv = $ifmpv;
    
        return $this;
    }

    /**
     * Get ifmpv
     *
     * @return string
     */
    public function getIfmpv()
    {
        return $this->ifmpv;
    }

    /**
     * Set ee21
     *
     * @param string $ee21
     *
     * @return Compteur
     */
    public function setEe21($ee21)
    {
        $this->ee21 = $ee21;
    
        return $this;
    }

    /**
     * Get ee21
     *
     * @return string
     */
    public function getEe21()
    {
        return $this->ee21;
    }

    /**
     * Set ee22
     *
     * @param string $ee22
     *
     * @return Compteur
     */
    public function setEe22($ee22)
    {
        $this->ee22 = $ee22;
    
        return $this;
    }

    /**
     * Get ee22
     *
     * @return string
     */
    public function getEe22()
    {
        return $this->ee22;
    }

    /**
     * Set dc2
     *
     * @param string $dc2
     *
     * @return Compteur
     */
    public function setDc2($dc2)
    {
        $this->dc2 = $dc2;
    
        return $this;
    }

    /**
     * Get dc2
     *
     * @return string
     */
    public function getDc2()
    {
        return $this->dc2;
    }

    /**
     * Set tr2
     *
     * @param string $tr2
     *
     * @return Compteur
     */
    public function setTr2($tr2)
    {
        $this->tr2 = $tr2;
    
        return $this;
    }

    /**
     * Get tr2
     *
     * @return string
     */
    public function getTr2()
    {
        return $this->tr2;
    }

    /**
     * Set bh2
     *
     * @param string $bh2
     *
     * @return Compteur
     */
    public function setBh2($bh2)
    {
        $this->bh2 = $bh2;
    
        return $this;
    }

    /**
     * Get bh2
     *
     * @return string
     */
    public function getBh2()
    {
        return $this->bh2;
    }

    /**
     * Set ca2
     *
     * @param string $ca2
     *
     * @return Compteur
     */
    public function setCa2($ca2)
    {
        $this->ca2 = $ca2;
    
        return $this;
    }

    /**
     * Get ca2
     *
     * @return string
     */
    public function getCa2()
    {
        return $this->ca2;
    }

    /**
     * Set ck2
     *
     * @param string $ck2
     *
     * @return Compteur
     */
    public function setCk2($ck2)
    {
        $this->ck2 = $ck2;

        return $this;
    }

    /**
     * Get ck2
     *
     * @return string
     */
    public function getCk2()
    {
        return $this->ck2;
    }

    /**
     * Set vg3
     *
     * @param string $vg3
     *
     * @return Compteur
     */
    public function setVg3($vg3)
    {
        $this->vg3 = $vg3;
    
        return $this;
    }

    /**
     * Get vg3
     *
     * @return string
     */
    public function getVg3()
    {
        return $this->vg3;
    }

    /**
     * Set vh3
     *
     * @param string $vh3
     *
     * @return Compteur
     */
    public function setVh3($vh3)
    {
        $this->vh3 = $vh3;
    
        return $this;
    }

    /**
     * Get vh3
     *
     * @return string
     */
    public function getVh3()
    {
        return $this->vh3;
    }

    /**
     * Set mtcvm
     *
     * @param string $mtcvm
     *
     * @return Compteur
     */
    public function setMtcvm($mtcvm)
    {
        $this->mtcvm = $mtcvm;
    
        return $this;
    }

    /**
     * Get mtcvm
     *
     * @return string
     */
    public function getMtcvm()
    {
        return $this->mtcvm;
    }
}
