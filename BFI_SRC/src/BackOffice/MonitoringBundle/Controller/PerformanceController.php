<?php

namespace BackOffice\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PerformanceController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BackOfficeMonitoringBundle:Performance');

        $perf = $repo->findLast30();

        $tabJ = array();
        for ($i=30; $i>=0; $i--) {
            $tabJ[]=date('d/m/Y', strtotime('-'.$i.' day'));
        }

        $param = array('jours' => $tabJ, 'perf' => $perf);

        return $this->render('BackOfficeMonitoringBundle:Performance:index.html.twig', $param);
    }
}
