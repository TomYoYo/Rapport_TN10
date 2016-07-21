<?php

namespace BackOffice\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BackOffice\MonitoringBundle\Entity\Log;
use Symfony\Component\Debug\Exception;

class MonitoController extends Controller
{
    const DATABASE_UNREACHABLE = "databaseUnreachable";
    const DATABASE_EMPTY_TABLE = "databaseEmptyTable";
    const DATABASE_OK = "databaseOk";
    const TRIGGER_OK = "triggerOk";
    const SURVEILLANCE_TRIGGER_OK = "surveillanceTriggerOk";
    const SURVEILLANCE_TRIGGER_KO = "surveillanceTriggerKo";
    const SURVEILLANCE_FICHIERS_OK = "surveillanceFichiersOk";
    const SURVEILLANCE_FICHIERS_KO = "surveillanceFichiersKo";
    const TRIGGER_KO = "triggerKo";
    const SERVER_OK = "serverOk";
    const SERVER_UNREACHABLE = "serverUnreachable";
    const SERVER_PB = "serverPb";
    const NO_INFO = "noInfo";
    const SURVEILLANCE_SIT_OK = "surveillanceSITOk";
    const SURVEILLANCE_SIT_KO = "surveillanceSITKo";
    const SURVEILLANCE_SIT_ERROR = "surveillanceSITError";

    public $dateTimeSuccessSabCore = '';
    public $dateTimeFailSabCore = '';
    public $lastTimeSabCoreStatus = '';
    public $dateTimeSuccessSIT = '';
    public $dateTimeFailSIT = '';
    public $dateTimeAlertSIT = '';
    public $lastDateTimeSIT = '';
    public $windowsConnexionManager;
    public $angersConnexionManager;
    public $listeTablesOracleSAB = array(
        array('nom' => 'ZADRESS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAGGCO0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAGPAS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAS0110', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAS0440', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAS1190', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAS3120', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBASLOC0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBASTAU0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCARCON0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCHQHIS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCLI1140', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCLIDIR0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCLIENA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCLIENB0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCLIENC0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCLINTA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCOMPTE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCPTBIS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCPTRIB0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCRE1790', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREBI20', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREBIS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREDOS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREEMP0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREMAD0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREPLA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZCREPRE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZDATBLO0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZDATCON0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZDWHOPE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZECHTAS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZERECOU0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZETUCRE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZEUPG5C0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZGUICB10', 'status' => '', 'count' => '/'),
        array('nom' => 'ZGUICB20', 'status' => '', 'count' => '/'),
        array('nom' => 'ZIFUADM0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZIFUAGR0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZIFUFOY0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZIFUHCT0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMOUANA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZPLAN0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZSCHEMA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZSOLDE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUUTI0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUUTIH0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNURUT0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUGRP0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAS0060', 'status' => '', 'count' => '/'),
        array('nom' => 'ZBAS2240', 'status' => '', 'count' => '/'),
        array('nom' => 'ZPRO0080', 'status' => '', 'count' => '/'),
        array('nom' => 'ZFWKGUT0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUHLB0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUHLA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUMEN0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUUTO0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUUTP0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZHBTMET0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUPWD0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMOUANA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZTAUVAL0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZTITULA0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUOUQ0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUAGE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNURSE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUSER0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNURSS0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUSES0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZFWKGRP0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUOPT0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZHBTI010', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUOPG0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNURUE0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUREV0', 'status' => '', 'count' => '/'),
        array('nom' => 'ZMNUREX0', 'status' => '', 'count' => '/'),
    );
    public $listeTablesOracleTitre = array(
        array('nom' => 'FDEUFCH00_PEXTDET_XTDET', 'status' => '', 'count' => '/'),
        array('nom' => 'FDEUFCH00_PEXTENT_XTENT', 'status' => '', 'count' => '/'),
        array('nom' => 'FDEUFCH00_PELDET_LDET', 'status' => '', 'count' => '/'),
        array('nom' => 'FDEUFCH00_PELENT_LENT', 'status' => '', 'count' => '/'),
        array('nom' => 'FDEUFCH00_PEYCUTIL_YCUTIL', 'status' => '', 'count' => '/'),
        array('nom' => 'USERS', 'status' => '', 'count' => '/')
    );
    public $listeTablesOracleBFI = array(
        array('nom' => 'Editique', 'status' => '', 'count' => '/'),
        array('nom' => 'Editique_Corres_Releve', 'status' => '', 'count' => '/'),
        array('nom' => 'Editique_Message_Commercial', 'status' => '', 'count' => '/'),
        array('nom' => 'Log', 'status' => '', 'count' => '/'),
        array('nom' => 'Mail', 'status' => '', 'count' => '/'),
        array('nom' => 'OD_Action', 'status' => '', 'count' => '/'),
        array('nom' => 'OD_Correspondance_Comptes', 'status' => '', 'count' => '/'),
        array('nom' => 'OD_Mouvement', 'status' => '', 'count' => '/'),
        array('nom' => 'OD_Operation', 'status' => '', 'count' => '/'),
        array('nom' => 'OD_Statut', 'status' => '', 'count' => '/'),
        array('nom' => 'P30S', 'status' => '', 'count' => '/'),
        array('nom' => 'Profil', 'status' => '', 'count' => '/'),
        array('nom' => 'TauxCredit', 'status' => '', 'count' => '/'),
        array('nom' => 'Trigger_Action', 'status' => '', 'count' => '/'),
        array('nom' => 'PARAMETRAGE_ROLE', 'status' => '', 'count' => '/'),
        array('nom' => 'PARAMETRAGE_SPOOL', 'status' => '', 'count' => '/'),
        array('nom' => 'PARAMETRAGE_TAG', 'status' => '', 'count' => '/'),
        array('nom' => 'TRANSVERSE_FILTRE', 'status' => '', 'count' => '/'),
        array('nom' => 'TRANSVERSE_PARAMETRAGE', 'status' => '', 'count' => '/'),
        array('nom' => 'TRANSVERSE_ROLE', 'status' => '', 'count' => '/'),
        array('nom' => 'TRANSVERSE_SPOOL', 'status' => '', 'count' => '/'),
        array('nom' => 'TRANSVERSE_TAG', 'status' => '', 'count' => '/'),
        
    );
    public $statusOracleBFI;
    public $statusOracleSAB;
    public $statusOracleTitre;
    public $listeTrigger = array(
        array('nom' => 'ZBAGPAS0_UPD_TRIG_ACT', 'status' => ''),
        array('nom' => 'ZCHQHIS0_UPD_TRIG_ACT', 'status' => ''),
        array('nom' => 'ZCREPLA0_INS_UPD_TRIG_ACT', 'status' => ''),
        array('nom' => 'ZDATBLO0_INS_TRIG_ACT', 'status' => ''),
        array('nom' => 'ZERECOU0_INS_TRIG_ACT', 'status' => '')
    );
    public $statusTrigger;
    public $statusSabCore;
    // windows
    public $svWinUser;
    public $svWinPass;
    public $svWinServer;
    public $statusWindows;
    public $connexionWindows;
    public $treeWindows;
    public $statusSurveillanceTrigger;
    public $statusSurveillanceTriggerNb;
    public $statusSurveillanceFichiers;
    public $statusSurveillanceFichiersNb;

    public function initSvWindows()
    {
        $this->windowsConnexionManager = $this->container->get('back_office_connexion.windowsFTP');
        $this->listeDossierWindows = array();
    }
    
    public function initSvAngers()
    {
        $this->angersConnexionManager = $this->container->get('back_office_connexion.angersFTP');
    }

    public function indexAction()
    {
        $paramTpl = array();

        // Bases de données
        $this->statusOracleBFI = $this->testConnectionOracle($this->listeTablesOracleBFI);
        $this->statusOracleSAB = $this->testConnectionOracle($this->listeTablesOracleSAB);
        $this->statusOracleTitre = $this->testConnectionOracle($this->listeTablesOracleTitre);
        $this->testPresenceTrigger();

        $paramTpl ['listeTablesBFI'] = $this->listeTablesOracleBFI;
        $paramTpl ['listeTablesSAB'] = $this->listeTablesOracleSAB;
        $paramTpl ['listeTablesTitre'] = $this->listeTablesOracleTitre;
        $paramTpl ['listeTrigger'] = $this->listeTrigger;
        $paramTpl ['statusOracleBFI'] = $this->statusOracleBFI;
        $paramTpl ['statusOracleSAB'] = $this->statusOracleSAB;
        $paramTpl ['statusOracleTitre'] = $this->statusOracleTitre;
        $paramTpl ['statusTrigger'] = $this->statusTrigger;

        // Données externes
        $this->testConnexionSabCore();
        $this->testConnexionWindows();
        $this->testConnexionAngers();
        $this->testSurveillanceSIT();

        $paramTpl ['listeDossierWindows'] = $this->listeDossierWindows;
        $paramTpl ['statusSabCore'] = $this->statusSabCore;
        $paramTpl ['statusWindows'] = $this->statusWindows;
        $paramTpl ['connexionWindows'] = $this->connexionWindows;
        $paramTpl ['treeWindows'] = $this->treeWindows;
        $paramTpl ['statusAngers'] = $this->statusAngers;
        $paramTpl ['connexionAngers'] = $this->connexionAngers;
        $paramTpl ['dateTimeSuccessSabCore'] = $this->dateTimeSuccessSabCore;
        $paramTpl ['dateTimeFailSabCore'] = $this->dateTimeFailSabCore;
        $paramTpl ['lastTimeSabCoreStatus'] = $this->lastTimeSabCoreStatus;
        $paramTpl ['dateTimeSuccessSIT'] = $this->dateTimeSuccessSIT;
        $paramTpl ['dateTimeFailSIT'] = $this->dateTimeFailSIT;
        $paramTpl ['dateTimeAlertSIT'] = $this->dateTimeAlertSIT;
        $paramTpl ['lastDateTimeSIT'] = $this->lastDateTimeSIT;
        $paramTpl ['statusSIT'] = $this->statusSIT;

        // Démons
        $this->testPresenceSurveillanceTrigger();
        $this->testPresenceSurveillanceFichiers();

        $paramTpl ['statusSurveillanceTrigger'] = $this->statusSurveillanceTrigger;
        $paramTpl ['statusSurveillanceTriggerNb'] = $this->statusSurveillanceTriggerNb;
        $paramTpl ['statusSurveillanceFichiers'] = $this->statusSurveillanceFichiers;
        $paramTpl ['statusSurveillanceFichiersNb'] = $this->statusSurveillanceFichiersNb;

        return $this->render('BackOfficeMonitoringBundle:Default:index.html.twig', $paramTpl);
    }

    public function testConnectionOracle(&$listeTable)
    {
        $statusGlobal = self::DATABASE_OK;
        foreach ($listeTable as $k => $table) {

            $stmt = $this->getDoctrine()->getManager()
                ->getConnection()
                ->prepare('SELECT COUNT(*) as COUNT FROM '. $table['nom'])
            ;

            try {
                $stmt->execute();
                $res = $stmt->fetch();
                $listeTable[$k]['count'] = $res['COUNT'];

                if ($res['COUNT'] > 0) {
                    $listeTable[$k]['status'] = self::DATABASE_OK;
                } else {
                    $listeTable[$k]['status'] = self::DATABASE_EMPTY_TABLE;
                    if ($statusGlobal == self::DATABASE_OK) {
                        $statusGlobal = self::DATABASE_EMPTY_TABLE;
                    }
                }
            } catch (\Exception $ex) {
                $listeTable[$k]['status'] = self::DATABASE_UNREACHABLE;
                $statusGlobal = self::DATABASE_UNREACHABLE;
            }
        }
        return $statusGlobal;
    }

    public function testOracleBFIAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->statusOracleBFI = $this->testConnectionOracle($this->listeTablesOracleBFI);

            $paramTpl = array();
            $paramTpl ['listeTablesBFI'] = $this->listeTablesOracleBFI;
            $paramTpl ['statusOracleBFI'] = $this->statusOracleBFI;

            return $this->render('BackOfficeMonitoringBundle:Includes:connexionOracleBFI.html.twig', $paramTpl);
        }
    }

    public function testOracleSABAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->statusOracleSAB = $this->testConnectionOracle($this->listeTablesOracleSAB);

            $paramTpl = array();
            $paramTpl ['listeTablesSAB'] = $this->listeTablesOracleSAB;
            $paramTpl ['statusOracleSAB'] = $this->statusOracleSAB;

            return $this->render('BackOfficeMonitoringBundle:Includes:connexionOracleSAB.html.twig', $paramTpl);
        }
    }

    public function testOracleTitreAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->statusOracleTitre = $this->testConnectionOracle($this->listeTablesOracleTitre);

            $paramTpl = array();
            $paramTpl ['listeTablesTitre'] = $this->listeTablesOracleTitre;
            $paramTpl ['statusOracleTitre'] = $this->statusOracleTitre;

            return $this->render('BackOfficeMonitoringBundle:Includes:connexionOracleTitre.html.twig', $paramTpl);
        }
    }

    public function testPresenceTrigger()
    {
        $this->statusTrigger = self::TRIGGER_OK;
        foreach ($this->listeTrigger as $k => $trigger) {

            $rq = "SELECT * FROM all_triggers where trigger_name = '" . $trigger['nom'] . "' and status = 'ENABLED'";
            $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($rq);

            try {
                $stmt->execute();
                $res = $stmt->fetchAll();

                if (empty($res)) {
                    $this->listeTrigger[$k]['status'] = self::TRIGGER_KO;
                    if ($this->statusTrigger == self::TRIGGER_OK) {
                        $this->statusTrigger = self::TRIGGER_KO;
                    }
                } else {
                    $this->listeTrigger[$k]['status'] = self::TRIGGER_OK;
                }
            } catch (\Exception $ex) {
                $this->listeTrigger[$k]['status'] = self::TRIGGER_KO;
                $this->statusTrigger = self::TRIGGER_KO;
            }
        }
    }

    public function testTriggerAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testPresenceTrigger();

            $paramTpl = array();
            $paramTpl ['listeTrigger'] = $this->listeTrigger;
            $paramTpl ['statusTrigger'] = $this->statusTrigger;

            return $this->render('BackOfficeMonitoringBundle:Includes:presenceTrigger.html.twig', $paramTpl);
        }
    }

    public function testConnexionSabCore()
    {
        $paramSuccess = array(
            'niveau' => Log::NIVEAU_SUCCESS,
            'module' => 'BackOffice > Connexion',
            'action' => 'Connexion SFTP'
        );

        $paramFail = array(
            'niveau' => Log::NIVEAU_ERREUR,
            'module' => 'BackOffice > Connexion',
            'action' => 'Connexion SFTP'
        );

        $em = $this->getDoctrine()->getManager();

        $resSuccess = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramSuccess,
            array('datetime' => 'DESC')
        );
        $resFail = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramFail,
            array('datetime' => 'DESC')
        );

        if (!$resSuccess) {
            $this->statusSabCore = self::SERVER_UNREACHABLE;
            $this->lastTimeSabCoreStatus = new \DateTime();
            if ($resFail) {
                $this->dateTimeFailSabCore = $resFail->getDatetime();
                $this->lastTimeSabCoreStatus = $this->dateTimeFailSabCore;
            }
        } else {
            $this->dateTimeSuccessSabCore = $resSuccess->getDatetime();
            if ($resFail) {
                $this->dateTimeFailSabCore = $resFail->getDatetime();

                if ($this->dateTimeSuccessSabCore > $this->dateTimeFailSabCore) {
                    $this->statusSabCore = self::SERVER_OK;
                    $this->lastTimeSabCoreStatus = $this->dateTimeSuccessSabCore;
                } else {
                    $this->statusSabCore = self::SERVER_UNREACHABLE;
                    $this->lastTimeSabCoreStatus = $this->dateTimeFailSabCore;
                }
            } else {
                $this->statusSabCore = self::SERVER_OK;
                $this->lastTimeSabCoreStatus = $this->dateTimeSuccessSabCore;
            }

        }
    }

    public function testSabCoreAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testConnexionSabCore();

            $paramTpl = array();
            $paramTpl ['statusSabCore'] = $this->statusSabCore;
            $paramTpl ['dateTimeSuccessSabCore'] = $this->dateTimeSuccessSabCore;
            $paramTpl ['dateTimeFailSabCore'] = $this->dateTimeFailSabCore;
            $paramTpl ['lastTimeSabCoreStatus'] = $this->lastTimeSabCoreStatus;

            return $this->render('BackOfficeMonitoringBundle:Includes:connexionSabCore.html.twig', $paramTpl);
        }
    }

    public function testConnexionWindows()
    {
        $this->initSvWindows();

        if (!$this->windowsConnexionManager->isConnected) {
            $this->connexionWindows = self::SERVER_UNREACHABLE;
            $this->treeWindows = self::SERVER_UNREACHABLE;
            $this->statusWindows = self::SERVER_UNREACHABLE;
        } else {
            $repBq = $this->container->getParameter('svWin.dirBanqueClient');
            $this->connexionWindows = self::SERVER_OK;
            if (ftp_nlist($this->windowsConnexionManager->conn_ftp, $repBq)) {
                $this->treeWindows = self::SERVER_OK;
                $this->statusWindows = self::SERVER_OK;
            } else {
                $this->treeWindows = self::SERVER_UNREACHABLE;
                $this->statusWindows = self::SERVER_PB;
            }
        }
    }

    public function testWindowsAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testConnexionWindows();

            $paramTpl = array();
            $paramTpl ['listeDossierWindows'] = $this->listeDossierWindows;
            $paramTpl ['connexionWindows'] = $this->connexionWindows;
            $paramTpl ['treeWindows'] = $this->treeWindows;
            $paramTpl ['statusWindows'] = $this->statusWindows;

            return $this->render('BackOfficeMonitoringBundle:Includes:connexionWindows.html.twig', $paramTpl);
        }
    }
    
    public function testConnexionAngers()
    {
        $this->connexionAngers = self::SERVER_UNREACHABLE;
        $this->statusAngers = self::SERVER_UNREACHABLE;
        
        /*$this->initSvAngers();

        if (!$this->angersConnexionManager->isConnected) {
            $this->connexionAngers = self::SERVER_UNREACHABLE;
            $this->statusAngers = self::SERVER_UNREACHABLE;
        } else {
            $this->connexionAngers = self::SERVER_OK;
            $this->statusAngers = self::SERVER_OK;
        }*/
    }

    public function testAngersAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testConnexionAngers();

            $paramTpl = array();
            $paramTpl ['connexionAngers'] = $this->connexionAngers;
            $paramTpl ['statusWindows'] = $this->statusWindows;

            return $this->render('BackOfficeMonitoringBundle:Includes:connexionAngers.html.twig', $paramTpl);
        }
    }

    public function testSurveillanceSIT()
    {
        $paramSuccess = array(
            'niveau' => Log::NIVEAU_INFO,
            'module' => 'BackOffice > Monitoring',
            'action' => 'Vérification du fichier SIT'
        );

        $paramFail = array(
            'niveau' => Log::NIVEAU_ERREUR,
            'module' => 'BackOffice > Monitoring',
            'action' => 'Vérification du fichier SIT'
        );

        $paramAlert = array(
            'niveau' => Log::NIVEAU_ALERT,
            'module' => 'BackOffice > Monitoring',
            'action' => 'Vérification du fichier SIT'
        );

        $em = $this->getDoctrine()->getManager();

        $resSuccess = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramSuccess,
            array('datetime' => 'DESC')
        );
        $resFail = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramFail,
            array('datetime' => 'DESC')
        );
        $resAlert = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramAlert,
            array('datetime' => 'DESC')
        );

        if ($resSuccess) {
            $this->dateTimeSuccessSIT = $resSuccess->getDatetime();
            $this->lastDateTimeSIT = $this->dateTimeSuccessSIT;
            $this->statusSIT = self::SURVEILLANCE_SIT_OK;
        }
        if ($resFail) {
            $this->dateTimeFailSIT = $resFail->getDatetime();
            if ($this->dateTimeFailSIT > $this->lastDateTimeSIT) {
                $this->lastDateTimeSIT = $this->dateTimeFailSIT;
                $this->statusSIT = self::SURVEILLANCE_SIT_KO;
            }
        }
        if ($resAlert) {
            $this->dateTimeAlertSIT = $resAlert->getDatetime();
            if ($this->dateTimeAlertSIT > $this->lastDateTimeSIT) {
                $this->lastDateTimeSIT = $this->dateTimeAlertSIT;
                $this->statusSIT = self::SURVEILLANCE_SIT_ERROR;
            }
        }
        
        if (!$resSuccess && !$resAlert && !$resFail) {
            $this->statusSIT = self::SURVEILLANCE_SIT_ERROR;
        }
    }

    public function testSITAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testSurveillanceSIT();

            $paramTpl = array();
            $paramTpl ['dateTimeSuccessSIT'] = $this->dateTimeSuccessSIT;
            $paramTpl ['dateTimeFailSIT'] = $this->dateTimeFailSIT;
            $paramTpl ['dateTimeAlertSIT'] = $this->dateTimeAlertSIT;
            $paramTpl ['lastDateTimeSIT'] = $this->lastDateTimeSIT;
            $paramTpl ['statusSIT'] = $this->statusSIT;

            return $this->render('BackOfficeMonitoringBundle:Includes:surveillanceCASA.html.twig', $paramTpl);
        }
    }

    public function testPresenceSurveillanceTrigger()
    {
        $this->statusSurveillanceTrigger = self::SURVEILLANCE_TRIGGER_OK;
        $this->statusSurveillanceTriggerNb = 1;

        @$res = exec("ps -aef | grep 'app/console trigger:check:action'| grep -v grep | wc -l");

        switch ($res){
            case '0':
                $this->statusSurveillanceTrigger = self::SURVEILLANCE_TRIGGER_KO;
                $this->statusSurveillanceTriggerNb = 0;
                break;
            case '1':
                $this->statusSurveillanceTrigger = self::SURVEILLANCE_TRIGGER_OK;
                $this->statusSurveillanceTriggerNb = 1;
                break;
            default:
                $this->statusSurveillanceTrigger = self::SURVEILLANCE_TRIGGER_OK;
                $this->statusSurveillanceTriggerNb = $res;
        }
    }

    public function testSurveillanceTriggerAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testPresenceSurveillanceTrigger();

            $paramTpl = array();
            $paramTpl ['statusSurveillanceTrigger'] = $this->statusSurveillanceTrigger;
            $paramTpl ['statusSurveillanceTriggerNb'] = $this->statusSurveillanceTriggerNb;

            $tpl = 'BackOfficeMonitoringBundle:Includes:presenceSurveillanceTrigger.html.twig';
            return $this->render($tpl, $paramTpl);
        }
    }

    public function testPresenceSurveillanceFichiers()
    {
        $this->statusSurveillanceFichiers = self::SURVEILLANCE_FICHIERS_OK;
        $this->statusSurveillanceFichiersNb = 1;

        @$res = exec("ps -aef | grep 'app/console monitoring:check:files'| grep -v grep | wc -l");

        switch ($res){
            case '0':
                $this->statusSurveillanceFichiers = self::SURVEILLANCE_FICHIERS_KO;
                $this->statusSurveillanceFichiersNb = 0;
                break;
            case '1':
                $this->statusSurveillanceFichiers = self::SURVEILLANCE_FICHIERS_OK;
                $this->statusSurveillanceFichiersNb = 1;
                break;
            default:
                $this->statusSurveillanceFichiers = self::SURVEILLANCE_FICHIERS_OK;
                $this->statusSurveillanceFichiersNb = $res;
        }
    }

    public function testSurveillanceFichiersAction()
    {
        $request = $this->container->get('request');
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax == true) {
            $this->testPresenceSurveillanceFichiers();

            $paramTpl = array();
            $paramTpl ['statusSurveillanceFichiers'] = $this->statusSurveillanceFichiers;
            $paramTpl ['statusSurveillanceFichiersNb'] = $this->statusSurveillanceFichiersNb;

            $tpl = 'BackOfficeMonitoringBundle:Includes:presenceSurveillanceFichiers.html.twig';
            return $this->render($tpl, $paramTpl);
        }
    }
}
