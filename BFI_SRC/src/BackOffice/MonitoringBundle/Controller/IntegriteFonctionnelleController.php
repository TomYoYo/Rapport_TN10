<?php

namespace BackOffice\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IntegriteFonctionnelleController extends Controller
{
    public function indexAction()
    {
        $rq = "SELECT max(DWHCPTDAO) as DATE_COMPTABLE FROM ZDWHCPT0";
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($rq);
        $stmt->execute();
        $res = $stmt->fetch();
        //var_dump($res['DATE_COMPTABLE']);

        $date = $res['DATE_COMPTABLE'];
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6);
        $date = $day.'/'.$month.'/'.$year;

        $param = array('DATE_COMPTABLE'=>array('res'=>$date, 'integrite'=> $date == date('d/m/Y')));
        //die($res['DATE_COMPTABLE']);

        return $this->render('BackOfficeMonitoringBundle:IntegriteFonctionnelle:index.html.twig', $param);
    }
}
