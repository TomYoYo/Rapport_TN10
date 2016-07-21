<?php

namespace Fiscalite\ODBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajax controller.
 *
 */
class AjaxController extends Controller
{
    public function checkCustomerAction(Request $request)
    {
        $data = $request->query->get('data');

        if (trim($data) == '') {
            $exists = 1;
        } else {
            $exists = 0;

            $stmt = $this->getDoctrine()->getManager("bfi")
                ->getConnection()
                ->prepare("SELECT CLIENACLI FROM ZCLIENA0 WHERE CLIENACLI = '" . $data . "'");

            $stmt->execute();
            $res = $stmt->fetchAll();

            if ($res) {
                $exists = 1;
            } else {
                $exists = 0;
            }
        }

        return new Response($exists);
    }

    public function checkAccountAction(Request $request)
    {
        $data = $request->query->get('data');
        
        if (trim($data) == '') {
            $exists = 1;
        } else {
            $exists = 0;
            
            $stmt = $this->getDoctrine()->getManager("bfi")
                ->getConnection()
                ->prepare("SELECT COMPTECOM FROM ZCOMPTE0 WHERE COMPTECOM = '" . $data . "'");

            $stmt->execute();
            $res = $stmt->fetchAll();
            
            if ($res) {
                $exists = 1;
            } else {
                $exists = 0;
            }
        }

        return new Response($exists);
    }
}
