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

class UserManager
{
    public $emm;
    public $lm;
    public function __construct( EntityManager $entityManager,LogManager $logManager){
        $this->emm=$entityManager;
        $this->lm = $logManager;
    }

    public function setEntityManager($em)
    {
        $this->entityManager = $em;
    }

    //execute un select qui retourne plusieurs lignes
    public function executeMultipleSelectQuery($query)
    {
        $querySAB = $this->entityManager->getConnection()->prepare($query);
        $querySAB->execute();
        $results = $querySAB->fetchAll();
        return $results;
    }

    //execute un select qui retourne une ligne
    public function executeOneSelectQuery($query, $param = null)
    {
        $querySAB = $this->entityManager->getConnection()->prepare($query);
        $querySAB->execute();
        $results = $querySAB->fetch();
        return $results;
    }

    //méthode de récupération de la date du mot de passe
    public function getDatePassword($login)
    {
        $login = trim($login);
        $result = $this->executeOneSelectQuery("SELECT MNUPWDEPD FROM ZMNUPWD0 WHERE MNUPWDUSR = '".$login."'");
        return $result;

    }

    //méthode de récupération des données utilisateurs potentiellement filtrés
    public function getAllUsers($datas,$menu=null,$metier=null,$donnee=null,$desactivate = null)
    {
        if ($datas == null) {
         $results = $this->executeMultipleSelectQuery("SELECT MNURUTCUT,MNURUTUTI,MNURUTNOM,MNURUTCUT,MNURUTLOG FROM ZMNURUT0 where MNURUTUTI not like '%. %' ORDER BY TRIM(ZMNURUT0.MNURUTUTI) ASC");

         }
        else
        {
            if(isset($datas))
            {
                $datas = strtoupper($datas);
                $results = $this->executeMultipleSelectQuery("SELECT MNURUTCUT,MNURUTUTI,MNURUTNOM,MNURUTCUT,MNURUTLOG FROM ZMNURUT0 WHERE (MNURUTUTI LIKE '%".$datas."%' or MNURUTNOM LIKE '%".$datas."%') and MNURUTUTI not like '%. %' ORDER BY TRIM(ZMNURUT0.MNURUTUTI) ASC");
            }

        }
        $users = array();
        foreach($results as $result)
        {
            $date = $this->getDatePassword($result['MNURUTUTI']);
            $informationsUser = $this->getMoreInformations($result['MNURUTCUT']);
            $zmnuutih0 = $this->executeOneSelectQuery("SELECT * FROM ZMNUUTIH0 WHERE MNUUTIHCUT ='".$result['MNURUTCUT']."'");
            if(empty($informationsUser))
            {
                $entree = 'N';
            }
            else
            {
                $entree = 'O';
            }
            $insert = true;
            if($desactivate == 1 && $entree == 'O')
            {
                $insert = false;
            }
            else
            {

                if($desactivate == 2 && $entree == 'N')
                {
                    $insert = false;
                }
                else
                {
                    if($desactivate == 1)
                    {
                        if($menu != null && $menu != $zmnuutih0['MNUUTIHGR2'])
                        {
                            $insert = false;
                        }
                        if($donnee != null && $donnee != $zmnuutih0['MNUUTIHGR3'])
                        {
                            $insert = false;
                        }
                        if($metier != null && $metier != $zmnuutih0['MNUUTIHGR4'])
                        {
                            $insert = false;
                        }
                    }
                    else{
                        if($menu != null && $menu != $informationsUser['MNUUTIGR2'])
                        {
                            $insert = false;
                        }
                        if($donnee != null && $donnee != $informationsUser['MNUUTIGR3'])
                        {
                            $insert = false;
                        }
                        if($metier != null && $metier != $informationsUser['MNUUTIGR4'])
                        {
                            $insert = false;
                        }
                    }
                }
            }
            if($insert)
            {
            array_push($users,array(
               'login' => $result['MNURUTUTI'],
                'name' => $result['MNURUTNOM'],
                'code' => $result['MNURUTCUT'],
                'hab2' => $informationsUser['MNUUTIGR2'],
                'hab3' => $informationsUser['MNUUTIGR3'],
                'hab4' => $informationsUser['MNUUTIGR4'],
                'entree' => $entree,
                'date' => $date['MNUPWDEPD']
            ));
            }
        }
        return $users;
    }

    //méthode d'activation/désactivation d'un utilisateur
    public function updateEnter($id,$way,$user = null)
    {
        if($way)
        {
            $zmnuuti0 = array(
                'MNUUTIETB' => 1,
                'MNUUTIREF' => 0,
                'MNUUTICUT' => $user->getIdSab(),
                'MNUUTIGR2' => $user->getGroupe2(),
                'MNUUTIGR3' => $user->getGroupe3(),
                'MNUUTIGR4' => $user->getGroupe4(),
                'MNUUTIOUT' => $user->getFiles(),
                'MNUUTILAN' => 1,
                'MNUUTIMSE' => $user->getMenu(),
                'MNUUTIAGE' => $user->getAgence(),
                'MNUUTISER' => $user->getService(),
                'MNUUTISRV' => $user->getSousService(),
                'MNUUTIGEN' => 0,
            );
            $zmnuutih0 = array(
                'MNUUTIHETB' => 1,
                'MNUUTIHREF' => 0,
                'MNUUTIHCUT' => $user->getIdSab(),
                'MNUUTIHGR2' => $user->getGroupe2(),
                'MNUUTIHGR3' => $user->getGroupe3(),
                'MNUUTIHGR4' => $user->getGroupe4(),
                'MNUUTIHOUT' => $user->getFiles(),
                'MNUUTIHLAN' => 1,
                'MNUUTIHMSE' => $user->getMenu(),
                'MNUUTIHAGE' => $user->getAgence(),
                'MNUUTIHSER' => $user->getService(),
                'MNUUTIHSRV' => $user->getSousService(),
                'MNUUTIHGEN' => 0,
                'MNUUTIHDAT' => $this->dayDateToSAB(),
                'MNUUTIHHEU' => $this->timeDateToSAB(),
                'MNUUTIHCUM' => 27,
                'MNUUTIHTYM' => 'A'
            );
            $libellé = "Utilisateur : " . $user->getIdSab() . " réactivé";
            try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->insert('ZMNUUTI0',$zmnuuti0);
                $this->entityManager->getConnection()->insert('ZMNUUTIH0',$zmnuutih0);
                $this->entityManager->getConnection()->commit();

            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }
            $this->lm->addSuccess($libellé,"BackOffice > Habilitation","Réactivation utilisateur SAB");
        }
        else
        {
            $libellé = "Utilisateur : " . $id . " désactivé";
            $zmnuutih0 = array(
                'MNUUTIHETB' => 1,
                'MNUUTIHREF' => 0,
                'MNUUTIHCUT' => $user['MNUUTICUT'],
                'MNUUTIHGR2' =>  $user['MNUUTIGR2'],
                'MNUUTIHGR3' =>  $user['MNUUTIGR3'],
                'MNUUTIHGR4' =>  $user['MNUUTIGR4'],
                'MNUUTIHOUT' =>  $user['MNUUTIOUT'],
                'MNUUTIHLAN' => 1,
                'MNUUTIHMSE' =>  $user['MNUUTIMSE'],
                'MNUUTIHAGE' =>  $user['MNUUTIAGE'],
                'MNUUTIHSER' =>  $user['MNUUTISER'],
                'MNUUTIHSRV' =>  $user['MNUUTISRV'],
                'MNUUTIHGEN' => 0,
                'MNUUTIHDAT' => $this->dayDateToSAB(),
                'MNUUTIHCUM' => 1,
                'MNUUTIHHEU' => $this->timeDateToSAB(),
                'MNUUTIHTYM' => 'D'
            );
            try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->delete('ZMNUUTI0',array('MNUUTICUT' => $id));
                $this->entityManager->getConnection()->insert('ZMNUUTIH0',$zmnuutih0);
                $this->entityManager->getConnection()->commit();

            }catch(Exception $e)
            {
                $this->lm->addError($libellé,"BackOffice > Habilitation","Réactivation utilisateur SAB");
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }



            $this->lm->addSuccess($libellé,"BackOffice > Habilitation","Désactivation utilisateur");
        }
    }

    //méthode de récupération d'informations type groupe habilitation workflow etc ...
    public function getMoreInformations($ref)
    {
        $result = $this->executeOneSelectQuery("SELECT * FROM ZMNUUTI0 WHERE MNUUTICUT =".$ref);
        return $result;
    }

    //méthode de récupération des files d'attentes
    public function getFiles()
    {
        return $this->executeMultipleSelectQuery("SELECT MNUOUQOUT FROM ZMNUOUQ0");
    }

    //méthode de création d'un utilisateur
    public function createUser($user)
    {
        $ref = $this->getRef();
        $login = $this->createLogin($user['firstName'],$user['name']);
        $pNOM = strtoupper($user['firstName']) ." ". strtoupper($user['name']);
        $zmnurut0 = array(
            'MNURUTUTI' => $login,
            'MNURUTNOM' => $pNOM,
            'MNURUTETB' => 1,
            'MNURUTCUT' => $ref,
            'MNURUTLOG' => 'O'
        );

        $zmnuuti0 = array(
            'MNUUTIETB' => 1,
            'MNUUTIREF' => 0,
            'MNUUTICUT' => $ref,
            'MNUUTIGR2' => $user['menu'],
            'MNUUTIGR3' => $user['donnees'],
            'MNUUTIGR4' => $user['metiers'],
            'MNUUTIOUT' => $user['file'],
            'MNUUTILAN' => 1,
            'MNUUTIMSE' => 'N',
            'MNUUTIAGE' => $user['agences'],
            'MNUUTISER' => $user['service'],
            'MNUUTISRV' => $user['sservice'],
            'MNUUTIGEN' => 0
        );

        $zmnutih0 = array(
            'MNUUTIHETB' => 1,
            'MNUUTIHREF' => 0,
            'MNUUTIHCUT' => $ref,
            'MNUUTIHGR2' => $user['menu'],
            'MNUUTIHGR3' => $user['donnees'],
            'MNUUTIHGR4' => $user['metiers'],
            'MNUUTIHOUT' => $user['file'],
            'MNUUTIHLAN' =>  1,
            'MNUUTIHMSE' => 'N',
            'MNUUTIHAGE' => $user['agences'],
            'MNUUTIHSER' => $user['service'],
            'MNUUTIHSRV' => $user['sservice'],
            'MNUUTIHGEN' => 0,
            'MNUUTIHDAT' => $this->dayDateToSAB(),
            'MNUUTIHHEU' => $this->timeDateToSAB(),
            'MNUUTIHCUM' => 27,
            'MNUUTIHTYM' => 'A'
        );

        $d = new \DateTime('0100-01-01');
        $d15 = new \DateTime();
        $interval = new \DateInterval('P3M');
        $d15->sub($interval);
        $fdeufchoo = array(
            "SMNURUTUTI" => $login,
            "SMNUUTIETB" => 1,
            "SMNUUTIAGE" => $user['agences'],
            "SMNUUTISER" => $user['service'],
            "SMNUUTISRV" => $user['sservice'],
            "SMNUUTICUT" => $ref,
            "SMNURUTNOM" => $login,
            "SIEG"=> 'A',
            "AGEN" => '01',
            "DEPT" => $user['service'],
            "SERV"=> $user['sservice'],
            "RUTI" => $this->getNextRUTI(),
            "DATOUV" => $d15->format('Y/m/d'),
            "DATFER" => $d->format('d/m/Y'),
            "ECRUTI" => $this->getNextECRUTI()
        );

         $users = array(
             'NAME' => $login,
             'CURLIB' => 'FDEUOBJ00',
             'INLPGM' => 'FDEUOBJ00/FDECLQ001',
             'INLMNU' => '*NONE',
             'JOBD' => 'FDEUOBJ00/MICNCB',
             'MSGQ' => 'QUSRSYS/'.$login,
             'PRTDEV' => '*WRKSTN',
             'OUTQ' => 'FDEUOBJ/'.$login
       );

        $zmnurue0 = array(
            'MNURUECUT' => $ref,
            'MNURUEETB' => 1
        );

        $codeRes = $user['codeRes'];
        $zbas2240 = array();
        $zclival0 = array();
        if($user['codeRes'] != null)
        {
            $cfd = $user['codeRes'];
        }
        else
        {
            $cfd = $this->getNextCodeFreeData();
        }

        foreach($user['correspondant'] as $crd)
        {
            array_push($zclival0,array(
                'CLIVALETA' => 1,
                'CLIVALSEL' => $crd,
                'CLIVALRUB' => 'RUB03',
                'CLIVALVAL' => $cfd,
                'CLIVALABR' => $login,
                'CLIVALLIB' => $pNOM
            ));
        }


        if($user['codeRes'] != null)
        {
            $code =$superieur = $this->getCodeResFromLogin($user['superieur']);
            $zbas0060 = array(
                'BAS006001' => 1,
                'BAS006002' => 6,
                'BAS006003' => 'CLI',
                'BAS006004' => $codeRes,
                'BAS006006' => $user['abbr'],
                'BAS006007' => $user['lib'],
                'BAS006008' => $user['agences'],
                'BAS006009' => 'N',
                'BAS006010' => 0,
                'BAS006011' => $login,
                'BAS006012' => $user['profil'],
                'BAS006013' => $code['BAS006004'],
                'BAS006014' => $user['dossier'],
                'BAS006015' => 0,
                'BAS006016' => 0,
            );

            if($user['contentieux'] == 1)
            {
                $zproo080 = array(
                    'PRO008002' => 1,
                    'PRO008002' => 8,
                    'PRO008003' => $codeRes,
                    'PRO008005' => $ref,
                    'PRO008008' => $login,
                    'PRO008009' => $pNOM,
                    'PRO008010' => 'O'
                );
            }

            if($user['collateraux'] != null) {
                $collateral= array();
                foreach ($user['collateraux'] as $col) {
                    $code = $this->getCodeResFromLogin($col['user']);
                    if($code)
                    {
                        $collateral['BAS224001'] = 1;
                        $collateral['BAS224002'] = 224;
                        $collateral['BAS224003'] = $login;
                        $collateral['BAS224004'] = $code['BAS006004'];
                        $collateral['BAS224007'] = 0;
                        $collateral['BAS224009'] = $this->dayDateToSAB();
                        $collateral['BAS224010'] = "SABTELE";

                        if($col['Date'] != null)
                        {
                            $collateral['BAS224008'] = $this->dateUnderstandingToSAB($col['Date']);
                        }
                        else
                        {
                            $collateral['BAS224008'] = 0;
                        }
                        array_push($zbas2240,$collateral);
                    }
                }
            }

            if($user['collaterauxother']) {
                $collateral= array();
                foreach ($user['collaterauxother'] as $col) {
                    $collateral['BAS224001'] = 1;
                    $collateral['BAS224002'] = 224;
                    $collateral['BAS224003'] = $col['user'];
                    $collateral['BAS224004'] = $codeRes;
                    $collateral['BAS224007'] = 0;
                    $collateral['BAS224009'] = $this->dayDateToSAB();
                    $collateral['BAS224010'] = "SABTELE";

                    if($col['Date'] != null)
                    {
                        $collateral['BAS224008'] = $this->dateUnderstandingToSAB($col['Date']);
                    }
                    else
                    {
                        $collateral['BAS224008'] = 0;
                    }
                    array_push($zbas2240,$collateral);
                }
            }
            $zhbtmet0 = array();
            $maxRefVal = $this->executeOneSelectQuery("SELECT MAX(MNUHLBREF) FROM ZMNUHLB0 WHERE MNUHLBNOM = '".$user['metiers']."' AND MNUHLBCLA = 4 AND MNUHLBVAL = 1");
            if($maxRefVal)
            {
                $zhbtmet0 = array();
                $refNoVal = $this->executeMultipleSelectQuery("SELECT MNUHLBREF FROM ZMNUHLB0 WHERE MNUHLBNOM = '".$user['metiers']."' AND MNUHLBCLA = 4 AND MNUHLBVAL = 0 AND MNUHLBREF > ".$maxRefVal['MAX(MNUHLBREF)']);
                $valid = array(
                    'HBTMETETA' => 1,
                    'HBTMETREF' => $maxRefVal['MAX(MNUHLBREF)'],
                    'HBTMETGRP' => $user['metiers'],
                    'HBTMETCLA' => 4,
                    'HBTMETAPP' => 'GCR',
                    'HBTMETCOD' => '001',
                    'HBTMETAGE' => 0,
                    'HBTMETPRD' => $codeRes,
                    'HBTMETAUT' => $login,
                    'HBTMETMON' => 0,
                    'HBTMETECH'=> '0000000000'
                );
                if($user['compteRendu']){
                    $valid['HBTMETDLY'] = 1;
                }
                array_push($zhbtmet0,$valid);

                foreach($refNoVal as $noval)
                {
                    $novalid = array(
                        'HBTMETETA' => 1,
                        'HBTMETREF' => $noval['MNUHLBREF'],
                        'HBTMETGRP' => $user['metiers'],
                        'HBTMETCLA' => 4,
                        'HBTMETAPP' => 'GCR',
                        'HBTMETCOD' => '001',
                        'HBTMETAGE' => 0,
                        'HBTMETPRD' => $codeRes,
                        'HBTMETAUT' => $login,
                        'HBTMETMON' => 0,
                        'HBTMETECH'=> '0000000000'
                    );
                    if($user['compteRendu']){
                        $novalid['HBTMETDLY'] = 1;
                    }
                    array_push($zhbtmet0,$novalid);
                }
            }



        }

        $zfwkgut0 = array();
        foreach($user['workflow'] as $workflow) {
            array_push($zfwkgut0, array(
                'FWKGUTETA' => 1,
                'FWKGUTGRP' => $workflow,
                'FWKGUTCUT' => $ref,
            ));
        }
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->insert('ZMNURUT0',$zmnurut0);
            $this->entityManager->getConnection()->insert('ZMNUUTI0',$zmnuuti0);
            $this->entityManager->getConnection()->insert('ZMNUUTIH0',$zmnutih0);
            $this->entityManager->getConnection()->insert("USERS", $users);
            $this->entityManager->getConnection()->insert("ZMNURUE0", $zmnurue0);
            $this->entityManager->getConnection()->insert("FDEUFCH00_PEYCUTIL_YCUTIL", $fdeufchoo);
            if($user['codeRes'] != 0)
            {
                foreach($zbas2240 as $row)
                {
                     $this->entityManager->getConnection()->insert('ZBAS2240',$row);
                }
                $this->entityManager->getConnection()->insert('ZBAS0060',$zbas0060);
                foreach($zhbtmet0 as $row) {
                    $this->entityManager->getConnection()->insert('ZHBTMET0', $row);
                }
                if($user['contentieux'] == 1)
                {
                    $this->entityManager->getConnection()->insert('ZPRO0080',$zproo080);
                }
            }
            foreach($zclival0 as $row)
            {
                $this->entityManager->getConnection()->insert('ZCLIVAL0',$row);
            }

            foreach($zfwkgut0 as $row)
            {
                $this->entityManager->getConnection()->insert('ZFWKGUT0',$row);
            }
           //TODO $this->initiatePassword($login);
            $this->entityManager->getConnection()->commit();
        }
        catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function getAllUsersRespName()
    {
        $users = $this->executeMultipleSelectQuery("SELECT BAS006011 FROM ZBAS0060 where BAS006004 <> 'SAB'  ORDER BY BAS006011");
        return $users;
    }

    public function getNextCodeFreeData()
    {
        $max = $this->executeOneSelectQuery("SELECT MAX(CLIVALVAL) FROM ZCLIVAL0 WHERE CLIVALVAL LIKE 'X%'");
        if(!$max)
        {
            $result = 'X001';
        }else
        {
            $number = substr($max['MAX(CLIVALVAL)'],1,3);
            $result = 'X'.((int)$number+1);
        }
        return $result;
    }

    public function getNameWithLogin($name)
    {
        $name = $this->executeOneSelectQuery("SELECT MNURUTUTI FROM ZMNURUT0 WHERE MNURUTNOM ='".$name."'");
        return $name;
    }

    public function dateSABToUnderstanding($date)
    {
        if($date != 0) {
            $date = (string)$date;
            $dateFormat = substr($date, 5) . '/' . substr($date, 3, 2) . '/' . '20' . substr($date, 1, 2);
            return $dateFormat;
        }
        else return '';
    }

    public function initiatePassword($login)
    {
        $id = $this->getNextId();
        $md5 = md5($login,false);
        $string = "aaaaaaaaaaaaaaaa";
       /* for($i=0;$i<16;$i++)
        {
            $string .= chr(hexdec("0x".substr($md5,$i*2,2)));
        }*/
       /* $zmnupwd0 = array(
            'MNUPWDID' => $id,
            'MNUPWDUSR' => $login,
            'MNUPWDPWD' => (string)$string,
            'MNUPWDCNX' => 0,
            'MNUPWDEPD' => date('d/m/Y G:i:s')
        );
        $this->entityManager->getConnection()->insert('ZMNUPWD0',$zmnupwd0);*/
        $date =  date('d/m/Y');
        $this->entityManager->getConnection()->executeQuery("INSERT INTO ZMNUPWD0 VALUES ($id,$login,$string,0,$date)");

    }

   public function Hex2String($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }


    public function getNextId()
    {
        $id = $this->executeOneSelectQuery("SELECT MAX(MNUPWDID) FROM ZMNUPWD0");
        return (int)$id['MAX(MNUPWDID)']+1;
    }

    public function dayDateToSAB()
    {
        $date = "1" . date('ymd');
        return (int)$date;
    }

    public function timeDateToSAB()
    {
        $time = date('Gis').'00';
        return (int)$time;
    }

    public function dateUnderstandingToSAB($date)
    {
        $dateSAB = "1".date_format(date_create_from_format('d/m/Y',$date),('ymd'));
        return (int)$dateSAB;
    }

    public function getCodeResFromUser($name)
    {
        $code = $this->executeOneSelectQuery("SELECT BAS006004 FROM ZBAS0060 WHERE BAS006007 ='".$name."'");
        return $code;
    }

    public function getCodeResFromLogin($login)
    {
        $code = $this->executeOneSelectQuery("SELECT BAS006004 FROM ZBAS0060 WHERE BAS006011 ='".$login."'");
        return $code;
    }

    public function checkCodeRes($code)
    {
        $code = $this->executeOneSelectQuery("SELECT * FROM ZBAS0060 WHERE BAS006004 = '".$code."'");
        return $code;
    }

    public function createLogin($prenom,$nom)
    {
        if(strpos($prenom,'-'))
        {
            $pre = strtoupper($prenom[0]).strtoupper($prenom[strpos($prenom,'-')+1]);
        }
        else
        {
            $pre = strtoupper($prenom[0]);
        }

        $login = $pre.'.'.strtoupper(str_replace(" ","",$nom));
        if(strlen($login) > 10)
        {
            $login = substr($login,0,-strlen($login)+10);
        }
        while($loginExist = $this->checkLogin($login)['MNURUTUTI'])
        {
            $search = '123456789';
            $pos = strpos($search,substr($loginExist,strlen(trim($loginExist))-1,1));
            if($pos !== false)
            {
                $nbr = (int)substr($search,$pos,1)+1;
                $login = substr($login,0,strlen($login)-1) . $nbr;
            }
            else
            {
                if(strlen($login) == 10)
                {
                    $login = substr($login,0,strlen($login)-1) . '1';
                }
                else
                {
                    $login = $login . '1';
                }
            }
        }
        return $login;

    }

    public function checkLogin($login)
    {
        return $this->executeOneSelectQuery("SELECT MNURUTUTI FROM ZMNURUT0 WHERE MNURUTUTI = '".$login."'");
    }

    public function checkLoginResp($login)
    {
        return $this->executeOneSelectQuery("SELECT * FROM ZBAS0060 WHERE BAS006011 LIKE '".$login."'");
    }

    public function checkAbbr($abbr)
    {
        $abbr = $this->executeOneSelectQuery("SELECT BAS006006 FROM ZBAS0060 WHERE BAS006006 = '".$abbr."'");
        if($abbr)
        {
            return true;
        }
        else{
            return false;
        }
    }

    public function getRef()
    {
        $maxRef = $this->executeOneSelectQuery("SELECT MAX(MNURUTCUT) FROM ZMNURUT0");
        return (int)$maxRef['MAX(MNURUTCUT)']+1;
    }

    public function getCodeRes()
    {
        $codes = $this->executeMultipleSelectQuery("SELECT BAS006004 FROM ZBAS0060 WHERE BAS006004 LIKE '0%' ");
        return '0'.(string)($this->getMaxCode($codes)+1);
    }

    public function getMaxCode($codes)
    {
        $max = 0;
        foreach($codes as $code)
        {
            if((int)$code['BAS006004'] > $max)
            {
                $max = (int)$code['BAS006004'];
            }
        }
        return $max;
    }

    public function getHabilitation($type)
    {
        $metiers = $this->executeMultipleSelectQuery("SELECT MNUGRPNOM FROM ZMNUGRP0 WHERE MNUGRPCLA = ".$type);
        return $metiers;
    }

    public function getAllUsersName()
    {
        $users = $this->executeMultipleSelectQuery("SELECT MNURUTUTI,MNURUTCUT FROM ZMNURUT0");
        return $users;
    }

    public function getWorkflow()
    {
       $workflows = $this->executeMultipleSelectQuery("SELECT FWKGRPGRP FROM ZFWKGRP0");
        $workflowForm = array();
        foreach($workflows as $workflow)
        {
            $workflowForm[$workflow['FWKGRPGRP']] = $workflow['FWKGRPGRP'];
        }
        return $workflowForm;
    }

    public function reinitiatePassword($login)
    {
        $login = trim($login);
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUPWD0 SET MNUPWDEPD = ? WHERE MNUPWDUSR = ?", array(date("Y-M-d"),$login));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e){
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
        $this->lm->addInfo('Forçage du changement de mot de passe de '.$login,"BackOffice > Habilitation",'Changement de mot de passe');
    }

    public function getAgences()
    {
        $agences = $this->executeMultipleSelectQuery("SELECT MNUAGEAGE,MNUAGELIB FROM ZMNUAGE0");
        return $agences;
    }

    public function getServices($id)
    {
        $services = $this->executeMultipleSelectQuery("SELECT MNUSERSER FROM ZMNUSER0 WHERE MNUSERAGE =".$id);
        $servicesForm = array();
        $i = 0;
        foreach($services as $service)
        {
            $service['MNUSERSER'] = trim($service['MNUSERSER']);
            $name = $this->executeOneSelectQuery("SELECT MNURSELIB FROM ZMNURSE0 WHERE MNURSESER ='".$service['MNUSERSER']."'");
            $name['MNURSELIB'] = trim($name['MNURSELIB']);
            $servicesForm[$i]['id'] = $service['MNUSERSER'];
            $servicesForm[$i]['name'] = $name['MNURSELIB'];
            $i++;
        }
        return $servicesForm;
    }

    public function getServicesWithAbbr($abbr,$sous = false)
    {
        if(!$sous)
        {
            $name = $this->executeOneSelectQuery("SELECT MNURSELIB FROM ZMNURSE0 WHERE MNURSESER ='".$abbr."'");

        }
        else
        {
            $name = $this->executeOneSelectQuery("SELECT MNURSSLIB FROM ZMNURSS0 WHERE MNURSSSRV ='".$abbr."'");

        }
        return $name;
    }

    public function getSousServices($id_agence, $id_service)
    {
        $services = $this->executeMultipleSelectQuery("SELECT MNUSESSRV FROM ZMNUSES0 WHERE MNUSESAGE =".$id_agence."AND MNUSESSER ='".$id_service."'");
        $servicesForm = array();
        $i = 0;
        foreach($services as $service)
        {
            $service['MNUSESSRV'] = trim($service['MNUSESSRV']);
            $name = $this->executeOneSelectQuery("SELECT MNURSSLIB FROM ZMNURSS0 WHERE MNURSSSRV ='".$service['MNUSESSRV']."'");
            $name['MNURSSLIB'] = trim($name['MNURSSLIB']);
            $servicesForm[$i]['id'] = $service['MNUSESSRV'];
            $servicesForm[$i]['name'] = $name['MNURSSLIB'];
            $i++;
        }
        return $servicesForm;
    }

    public function getUserAjax($name)
    {
        $name = strtoupper($name);
        $results = $this->executeMultipleSelectQuery("SELECT BAS006011 FROM ZBAS0060 WHERE (BAS006007 LIKE '%".$name."%' OR BAS006011 LIKE '%".$name."%') AND BAS006011 NOT LIKE 'SAB%' AND BAS006007 NOT LIKE 'FID%'");
        $resultArray = array();
        $i =0;
        foreach($results as $result)
        {
            $resultArray[$i] = $result['BAS006011'];
            $i++;
        }
        return $resultArray;

    }

    public function getUserNameAjax($login)
    {
        $user = $this->executeOneSelectQuery("SELECT MNURUTNOM FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        return $user;
    }

    public function getAllUsersLogin()
    {
        $results = $this->executeMultipleSelectQuery("SELECT MNURUTUTI FROM ZMNURUT0");
        return $results;

    }

    public function getAllUsersLoginPass()
    {
        $results = $this->executeMultipleSelectQuery("SELECT MNUPWDUSR FROM ZMNUPWD0 where MNUPWDUSR <> 'SAB139' and MNUPWDUSR <> 'SAB13901' and MNUPWDUSR <> 'SABTELE' and MNUPWDUSR <> 'SABBPM139' and MNUPWDUSR <> 'SABEXPLOIT' and MNUPWDUSR <> 'BPMUSER'");
        return $results;

    }

    public function getUserData($login)
    {
        $zmnurut0 = $this->executeOneSelectQuery("SELECT * FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        $zmnuuti0 = $this->executeOneSelectQuery("SELECT * FROM ZMNUUTI0 WHERE MNUUTICUT =".$zmnurut0['MNURUTCUT']);
        $zbas0060 = $this->executeOneSelectQuery("SELECT * FROM ZBAS0060 WHERE BAS006011 ='".$login."'");
        $zpro0080 = $this->executeOneSelectQuery("SELECT * FROM ZPRO0080 WHERE PRO008005 =".$zmnurut0['MNURUTCUT']);
        $zbas2240 = $this->executeMultipleSelectQuery("SELECT * FROM ZBAS2240 WHERE BAS224003 ='".$login."' ORDER BY BAS224003");
        $zfwkgut0 = $this->executeMultipleSelectQuery("SELECT * FROM ZFWKGUT0 WHERE FWKGUTCUT =".$zmnurut0['MNURUTCUT']);

        $name = explode(" ",$zmnurut0['MNURUTNOM']);

        $return = array(
            'name'=> $name[1],
            'firstName'=> $name[0],
            'agence'  => $zmnuuti0['MNUUTIAGE'],
            'service' => $zmnuuti0['MNUUTISER'],
            'sous_service' => $zmnuuti0['MNUUTISRV'],
            'menu' => $zmnuuti0['MNUUTIGR2'],
            'donnees' => $zmnuuti0['MNUUTIGR3'],
            'metier' => $zmnuuti0['MNUUTIGR4'],
            'file' => $zmnuuti0['MNUUTIOUT'],
        );
        $crd = $this->executeMultipleSelectQuery("SELECT * FROM ZCLIVAL0 WHERE CLIVALABR = '".$login."'");
        $i=0;
        $correspondants = array();
        foreach($crd as $row)
        {
            $correspondants[$i] = $row['CLIVALSEL'];
            $i++;
        }
        $return['correspondant'] = $correspondants;
        if($zbas0060)
        {
            $return['abbr'] = $zbas0060['BAS006006'];
            $return['lib'] = $zbas0060['BAS006007'];
            $return['profil'] = $zbas0060['BAS006012'];
            $zbasother = $this->executeMultipleSelectQuery("SELECT BAS224003,BAS224008 FROM ZBAS2240 WHERE BAS224004 ='".$zbas0060['BAS006004']."' ORDER BY BAS224004");
            $maxRefVal = $this->executeOneSelectQuery("SELECT MAX(MNUHLBREF) FROM ZMNUHLB0 WHERE MNUHLBNOM = '".$zmnuuti0['MNUUTIGR4']."' AND MNUHLBCLA = 4 AND MNUHLBVAL = 1");
            $cr = $this->executeOneSelectQuery("SELECT HBTMETDLY FROM ZHBTMET0 WHERE HBTMETAPP = 'GCR' AND HBTMETCOD = '001' AND HBTMETREF = '".$maxRefVal['MAX(MNUHLBREF)']."' AND HBTMETGRP ='".$zmnuuti0['MNUUTIGR4']."' AND HBTMETPRD ='".$zbas0060['BAS006004']."'");
            if($zpro0080)
            {
                $return['contentieux'] = 1;
            }
            else
            {
                $return['contentieux'] = 2;
            }
            $return['code'] = $zbas0060['BAS006004'];
            $return['superieur'] = $this->getLoginWithCode($zbas0060['BAS006013']);
            $return['dossier'] = $zbas0060['BAS006014'];
            $return['cr'] = $cr['HBTMETDLY'];
            $collateral = array();
            $collateralother = array();
            $i=0;
            foreach($zbas2240 as $col)
            {
                $collateral[$i]['user'] = $this->getLoginWithCode($col['BAS224004']);
                $collateral[$i]['Date'] = $this->dateSABToUnderstanding($col['BAS224008']);
                $i++;
            }
            sort($collateral);
            $i=0;

            foreach ($zbasother as $item) {
                $collateralother[$i]['user'] = $item['BAS224003'];
                $collateralother[$i]['Date'] = $this->dateSABToUnderstanding($item['BAS224008']);
                $i++;
            }
            $return['collateral'] = $collateral;
            $return['collateralother'] = $collateralother;



        }
        $i=0;
        $workflow = array();
        foreach($zfwkgut0 as $work)
        {
            $workflow[$i] = $work['FWKGUTGRP'];
            $i++;
        }
        $return['workflow'] = $workflow;

        return $return;


    }


    public function getLoginWithCode($code)
    {
        $login = $this->executeOneSelectQuery("SELECT * FROM ZBAS0060 WHERE BAS006004 ='".$code."'");
        return $login['BAS006011'];
    }

    public function updateService($service,$login)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTISER = ? WHERE MNUUTICUT = ?", array($service,$ref['MNURUTCUT']));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateAgence($agence,$login)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTIAGE = ? WHERE MNUUTICUT = ?", array($agence,$ref['MNURUTCUT']));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateSservice($sous_service,$login)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTISRV = ? WHERE MNUUTICUT = ?", array($sous_service,$ref['MNURUTCUT']));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateFile($file,$login)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTIOUT = ? WHERE MNUUTICUT = ?", array($file,$ref['MNURUTCUT']));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }



    public function updateGroupe($hab,$login,$groupe)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");
        switch($groupe)
        {
            case 2 : try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTIGR2 = ? WHERE MNUUTICUT = ?", array($hab,$ref['MNURUTCUT']));
                $this->entityManager->getConnection()->commit();
            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }break;
            case 3: try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTIGR3 = ? WHERE MNUUTICUT = ?", array($hab,$ref['MNURUTCUT']));
                $this->entityManager->getConnection()->commit();
            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }break;
            case 4 :try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->executeUpdate("UPDATE ZMNUUTI0 SET MNUUTIGR4 = ? WHERE MNUUTICUT = ?", array($hab,$ref['MNURUTCUT']));
                $this->entityManager->getConnection()->commit();
            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }break;
        }
    }

    public function updateContentieux($login,$hab,$code)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT,MNURUTNOM FROM ZMNURUT0 WHERE MNURUTUTI ='".$login."'");

        switch($hab)
        {
            case 2 : try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->delete("ZPRO0080", array('PRO008003'=>(string)$code));
                $this->entityManager->getConnection()->commit();
            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }break;
            case 1 : $zpro0080 = array(
                'PRO008001' => 1,
                'PRO008002' => 8,
                'PRO008003' => (string)$code,
                'PRO008005' => $ref['MNURUTCUT'],
                'PRO008008' => $login,
                'PRO008009' => $ref['MNURUTNOM'],
                'PRO008010' => 'O'
            );

                try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->insert("ZPRO0080", $zpro0080);
                $this->entityManager->getConnection()->commit();
            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }break;
        }
    }

    public function updateDossier($dossier,$code)
    {
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZBAS0060 SET BAS006014 = ? WHERE BAS006004 = ?", array($dossier,$code));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateSuperieur($code,$superieur)
    {
        $codeSuperieur = $this->getCodeResFromLogin($superieur);
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZBAS0060 SET BAS006013 = ? WHERE BAS006004 = ?", array($codeSuperieur['BAS006004'],$code));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateAbrege($code,$abbr)
    {
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZBAS0060 SET BAS006006 = ? WHERE BAS006004 = ?", array($abbr,$code));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateLibelle($code,$lib)
    {
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZBAS0060 SET BAS006007 = ? WHERE BAS006004 = ?", array($lib,$code));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateProfil($code,$profil)
    {
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZBAS0060 SET BAS006012 = ? WHERE BAS006004 = ?", array($profil,$code));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function addCollateral($login,$loginCol,$date)
    {
        if($date == '')
        {
            $date = 0;
        }
        else
        {
            $date = $this->dateUnderstandingToSAB($date);
        }
        $code = $this->getCodeResFromLogin($loginCol);
        $zbas2240 = array(
          'BAS224001' => 1,
            'BAS224002' => 224,
            'BAS224003' => $login,
            'BAS224004' => $code['BAS006004'],
            'BAS224007' => 0,
            'BAS224008' => $date,
            'BAS224009' => $this->dayDateToSAB(),
            'BAS224010' => 'SABTELE'
        );
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->insert("ZBAS2240", $zbas2240);
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function removeCollateral($login,$loginCol)
    {
        $code = $this->getCodeResFromLogin($loginCol);
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeQuery("DELETE FROM ZBAS2240 WHERE BAS224003 ='".$login."' AND BAS224004 ='".$code['BAS006004']."'");
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function addWorkflow($login,$workflow)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI = '".$login."'");
        $work = array(
            'FWKGUTETA' => 1,
            'FWKGUTGRP' => $workflow,
            'FWKGUTCUT' => $ref['MNURUTCUT'],
        );
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->insert("ZFWKGUT0", $work);
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function addCRD($login,$lib,$codeRes)
    {
        $zmnurut0 = $this->executeOneSelectQuery("SELECT * FROM ZMNURUT0 WHERE MNURUTUTI = '".$login."'");
        if($codeRes)
        {
            $code = $codeRes;
        }
        else
        {
            $codeUsed = $this->executeOneSelectQuery("SELECT * FROM ZCLIVAL0 WHERE CLIVALABR = '".$login."'");
            if($codeUsed)
            {
                $code = $codeUsed['CLIVALVAL'];
            }
            else
            {
                $code = $this->getNextCodeFreeData();
            }
        }

        $clival0 = array(
            'CLIVALETA' => 1,
            'CLIVALSEL' => $lib,
            'CLIVALRUB' => 'RUB03',
            'CLIVALVAL' => $code,
            'CLIVALABR' => $zmnurut0['MNURUTUTI'],
            'CLIVALLIB' => $zmnurut0['MNURUTNOM']
        );

        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->insert("ZCLIVAL0", $clival0);
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function removeWorkflow($login,$workflow)
    {
        $ref = $this->executeOneSelectQuery("SELECT MNURUTCUT FROM ZMNURUT0 WHERE MNURUTUTI = '".$login."'");
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->delete("ZFWKGUT0", array('FWKGUTGRP'=>$workflow,'FWKGUTCUT' => $ref['MNURUTCUT']));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function removeCRD($login,$lib)
    {
        $codeUsed = $this->executeOneSelectQuery("SELECT * FROM ZCLIVAL0 WHERE CLIVALABR = '".$login."'");
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->delete("ZCLIVAL0", array('CLIVALSEL'=>$lib ,'CLIVALVAL' => $codeUsed['CLIVALVAL']));
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function getNextRUTI()
    {
        $max = $this->executeOneSelectQuery("SELECT MAX(RUTI) as max_ruti FROM FDEUFCH00_PEYCUTIL_YCUTIL");
        return (int)$max['MAX_RUTI']+1;
    }

    public function getNextECRUTI()
    {
        $max = $this->executeOneSelectQuery("SELECT MAX(ECRUTI) as MAX_ECRUTI FROM FDEUFCH00_PEYCUTIL_YCUTIL");
        $string = $max['MAX_ECRUTI'];
        $letter = substr($string,0,1);
        $number = substr($string,1,2);
        if((int)$number == 99)
        {
           $letter = chr(ord($letter)+1);
            $result = $letter . '01';
        }
        else
        {
            $result = $letter.((int)$number+1);
        }
        return $result;
    }

    public function changeSuperieur($data)
    {
        $newCode = $this->getCodeResFromLogin($data['newSup']);
        foreach($data['sub'] as $user)
        {
            try{
                $this->entityManager->getConnection()->beginTransaction();
                $this->entityManager->getConnection()->executeUpdate("UPDATE ZBAS0060 SET BAS006013 = '".$newCode['BAS006004']."' WHERE BAS006011 = '".$user."'");
                $this->entityManager->getConnection()->commit();
            }catch(Exception $e)
            {
                $this->entityManager->getConnection()->rollback();
                throw $e;
            }
        }
    }

    public function getSub($login)
    {
        $code = $this->getCodeResFromLogin($login);
        $subordonnees = $this->executeMultipleSelectQuery("SELECT * FROM ZBAS0060 WHERE BAS006013 = '".$code['BAS006004']."' ORDER BY BAS006011");
        return $subordonnees;
    }

    public function updateCR($hab,$cr,$code)
    {
        $maxRefVal = $this->executeOneSelectQuery("SELECT MAX(MNUHLBREF) FROM ZMNUHLB0 WHERE MNUHLBNOM = '".$hab."' AND MNUHLBCLA = 4 AND MNUHLBVAL = 1");
        $refNoVal = $this->executeMultipleSelectQuery("SELECT MNUHLBREF FROM ZMNUHLB0 WHERE MNUHLBNOM = '".$hab."' AND MNUHLBCLA = 4 AND MNUHLBVAL = 0 AND MNUHLBREF > ".$maxRefVal['MAX(MNUHLBREF)']);
        if($cr)
        {
            $entry = 1;
        }else
        {
            $entry = 0;
        }
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $this->entityManager->getConnection()->executeUpdate("UPDATE ZHBTMET0 SET HBTMETDLY = ".$entry." WHERE HBTMETREF = '".$maxRefVal['MAX(MNUHLBREF)']."' AND HBTMETGRP = '".$hab."' AND HBTMETPRD = '".$code."' AND HBTMETAPP = 'GCR'");
            foreach($refNoVal as $val)
            {
                $this->entityManager->getConnection()->executeUpdate("UPDATE ZHBTMET0 SET HBTMETDLY = ".$entry." WHERE HBTMETREF = '".$val['MNUHLBREF']."' AND HBTMETGRP = '".$hab."' AND HBTMETPRD = '".$code."' AND HBTMETAPP = 'GCR'");

            }
            $this->entityManager->getConnection()->commit();
        }catch(Exception $e)
        {
            $this->entityManager->getConnection()->rollback();
            throw $e;
        }
    }

    public function test()
    {
        return $this->createLogin('ALFRED','GOGOL');
         /*$zmnupwd0 = array(
             'MNUPWDID' => 719,
             'MNUPWDUSR' => 'A.POPOL',
             'MNUPWDPWD' => SAB139.MD5HASH('A.POPOL'),
             'MNUPWDCNX' => 0,
             'MNUPWDEPD' => date('d/m/Y G:i:s')
         );
         $this->entityManager->getConnection()->insert('ZMNUPWD0',$zmnupwd0);*/
      /*  $returnValue = '';
        $inputValue = 'A.POPOL';
        $id = 719;

        $stmt =  $this->entityManager->getConnection()->prepare('BEGIN :return_value := BFI.MD5HASH(:input_value);END;');
        $stmt->bindParam(':return_value', $returnValue,SQLT_CHR,16);
        $stmt->bindParam(':input_value', $inputValue,SQLT_CHR,-1);
        $stmt->execute();

        $date = new \DateTime();
        $datestring = $date->format('d/m/Y');
        $query = $this->entityManager->getConnection()->prepare("INSERT INTO ZMNUPWD0(MNUPWDID,MNUPWDUSR,MNUPWDPWD,MNUPWDCNX,MNUPWDEPD) VALUES (?,?,?,?,?)");
        $query->bindValue(1,$id);
        $query->bindValue(2,$inputValue);
        $query->bindValue(3,$returnValue);
        $query->bindValue(4,0);
        $query->bindValue(5,date('d/m/Y G:i:s'));
        $query->execute();*/


    }
}