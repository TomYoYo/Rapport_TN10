<?php

namespace BackOffice\HabilitationBundle\Manager;

use BackOffice\MonitoringBundle\Manager\LogManager;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Faker\Provider\DateTime;
use mageekguy\atoum\asserters\string;
use mageekguy\atoum\tests\units\asserters\dateInterval;
use mageekguy\atoum\tests\units\asserters\hash;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
/**
 * Created by PhpStorm.
 * User: t.pueyo
 * Date: 25/02/2016
 * Time: 16:00
 * Manager pour la gestion des utilisateurs SAB
 */

class HabilitationManager
{
    public $emm;
    public $lm;
    public function __construct( EntityManager $entityManager,LogManager $logManager){
        $this->emm=$entityManager;
        $this->lm = $logManager;
    }

    public function executeMultipleSelectQuery($query)
    {
        $querySAB = $this->entityManager->getConnection()->prepare($query);
        $querySAB->execute();
        $results = $querySAB->fetchAll();
        return $results;
    }

    public function executeOneSelectQuery($query, $param = null)
    {
        $querySAB = $this->entityManager->getConnection()->prepare($query);
        $querySAB->execute();
        $results = $querySAB->fetch();
        return $results;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    public function getMenus($menu,$parent)
    {
        $max = $this->getMaxLot($menu,2);
        $menus = $this->executeMultipleSelectQuery("SELECT MNUMENCOD as code, MNUOPTLIB as lib FROM SAB139.ZMNUMEN0, SAB139.ZMNUOPT0 WHERE MNUOPTCOD = MNUMENCOD AND MNUMENPRE = ".$parent." AND MNUMENGRP = '".$menu."' AND MNUMENREF = ".$max." ORDER BY MNUMENORD ");
        return $menus;
    }

    public function getMaxLot($groupe,$type)
    {
        $max = $this->executeOneSelectQuery("SELECT MAX(MNUHLBREF) AS max_ref from ZMNUHLB0 WHERE MNUHLBCLA = ".$type."and MNUHLBNOM = '".$groupe."'");
        return $max['MAX_REF'];
    }

    public function getDonnees($groupe)
    {
        switch($groupe)
        {
            case 0 : $code = 5; break;
            case 1 : $code = 2; break;
            case 2 : $code = 1; break;
            case 3 : $code = 4; break;
        }
        $refDonnees = $this->executeMultipleSelectQuery("SELECT DISTINCT ZMNUOPT0.MNUOPTENS FROM SAB139.ZMNUOPT0,SAB139.ZMNUOPG0 WHERE ZMNUOPG0.MNUOPGCOD = ZMNUOPT0.MNUOPTCOD and ZMNUOPG0.MNUOPGTYP =".$code);
        $datas = array();
        foreach($refDonnees as $refDonnee)
        {
            $data = array();
            $lib = $this->searchLib($refDonnee['MNUOPTENS']);
            $data['MNUOPTENS'] = $refDonnee['MNUOPTENS'];
            $data['ABBR1'] = $lib['abbr1'];
            $data['ABBR2'] = $lib['abbr2'];
            array_push($datas,$data);
        }
        return $datas;
    }

    public function getDonneesEach($donnee,$menu)
    {
        switch($menu)
        {
            case 0 : $code = 5; break;
            case 1 : $code = 2; break;
            case 2 : $code = 1; break;
            case 3 : $code = 4; break;
        }
        $refDonnees = $this->executeMultipleSelectQuery("SELECT ZMNUOPT0.MNUOPTLIB FROM SAB139.ZMNUOPT0,SAB139.ZMNUOPG0 WHERE ZMNUOPG0.MNUOPGCOD = ZMNUOPT0.MNUOPTCOD and ZMNUOPT0.MNUOPTENS = '".$donnee."' and ZMNUOPG0.MNUOPGTYP =".$code);
        return $refDonnees;
    }

    public function getMaxRefDonnees($groupe)
    {
        $max = $this->executeOneSelectQuery("SELECT MAX(MNUUTPREF) AS max_ref from ZMNUUTP0 WHERE MNUUTPGRP = '".$groupe."'");
        return $max['MAX_REF'];
    }

    public function searchLib($abbr)
    {
        $abbr1 = substr($abbr,0,3);
        $abbr2 = substr($abbr,3,3);
        $result = array();
        $result['abbr1'] = $this->libelleDonnee($abbr1);
        $result['abbr2'] = $this->libelleDonnee($abbr2);
        return $result;
    }

    public function libelleDonnee($abbr)
    {
        $lib = $this->executeOneSelectQuery("SELECT MNUREVAPP FROM ZMNUREV0 WHERE MNUREVPAP = '".$abbr."'");
        if(!$lib)
        {
            $lib = $this->executeOneSelectQuery("SELECT MNUREXLIB FROM ZMNUREX0 WHERE MNUREXPRE = '".$abbr."'");
            return $lib['MNUREXLIB'];
        }else
        {
            return $lib['MNUREVAPP'];
        }
    }

}