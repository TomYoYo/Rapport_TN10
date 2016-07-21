<?php

namespace Fiscalite\BudgetBundle\Controller;

use Fiscalite\BudgetBundle\Entity\CBSettings;
use mageekguy\atoum\cli\progressBar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BackOffice\ActionBundle\Entity\Action;

class DefaultController extends Controller
{
    /**
     * Display form and results
     *
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'display' => false
        );
    }

    /**
     * Settings of CB
     *
     * @Template()
     */
    public function settingsAction()
    {
        return array();
    }

    /**
     * Settings of CB
     *
     * @Template()
     */
    public function settingsCodeAction()
    {
        // Init
        $em = $this->getDoctrine()->getManager('bfi');
        $lst_bfi = "";
        $codes_not_set = null;
        $codes_set = null;

        // Codes renseignés sur BFI
        $codes_set = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->findBy(
            array('type' => 'CODE'),
            array('valueSab' => 'asc')
        );

        // Création de la chaîne
        foreach ($codes_set as $data) {
            $lst_bfi .= $data->getValueSab() . ',';
        }

        // Suppression de la dernière virgule
        $lst_bfi = substr($lst_bfi, 0, -1);

        // Codes non paramétrés
        if ($lst_bfi != "") {
            $query = "SELECT DISTINCT(TRIM(SUBSTR(CPTANADON, 9))) AS CDE_BUD FROM ZCPTANA0 " .
                "WHERE TRIM(SUBSTR(CPTANADON, 9)) NOT IN ($lst_bfi) " .
                "ORDER BY TRIM(SUBSTR(CPTANADON, 9))";
        } else {
            $query = "SELECT DISTINCT(TRIM(SUBSTR(CPTANADON, 9))) AS CDE_BUD FROM ZCPTANA0 " .
                "ORDER BY TRIM(SUBSTR(CPTANADON, 9))";
        }
        $req = $em->getConnection()->prepare($query);
        $req->execute();
        $result_sab = $req->fetchAll();

        foreach ($result_sab as $data) {
            $codes_not_set[] = $data['CDE_BUD'];
        }

        return array(
            'codes_set' => $codes_set,
            'codes_not_set' => $codes_not_set
        );
    }

    /**
     * Settings of CB
     *
     * @Template()
     */
    public function settingsNatureAction()
    {
        // Init
        $em = $this->getDoctrine()->getManager('bfi');
        $lst_bfi = "";
        $natures_not_set = null;
        $natures_set = null;

        // Natures renseignées sur BFI
        $natures_set = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->findBy(
            array('type' => 'NATURE'),
            array('valueSab' => 'asc')
        );

        // Création de la chaîne
        foreach ($natures_set as $data) {
            $lst_bfi .= $data->getValueSab() . ',';
        }

        // Suppression de la dernière virgule
        $lst_bfi = substr($lst_bfi, 0, -1);

        // Natures non paramétrées
        if ($lst_bfi) {
            $query = "SELECT DISTINCT(CPTCPOETA) AS NAT_BUD FROM ZCPTCPO0 " .
                "WHERE CPTCPOETA NOT IN ($lst_bfi) ORDER BY CPTCPOETA";
        } else {
            $query = "SELECT DISTINCT(CPTCPOETA) AS NAT_BUD FROM ZCPTCPO0 " .
                "ORDER BY CPTCPOETA";
        }
        $req = $em->getConnection()->prepare($query);
        $req->execute();
        $result_sab = $req->fetchAll();

        foreach ($result_sab as $data) {
            $natures_not_set[] = $data['NAT_BUD'];
        }

        return array(
            'natures_set' => $natures_set,
            'natures_not_set' => $natures_not_set
        );
    }

    /**
     * Edit Settings
     *
     * @Template()
     */
    public function settingsEditAction($id)
    {
        // Init
        $em = $this->getDoctrine()->getManager('bfi');
        $setting = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->find($id);

        return array(
            'setting' => $setting
        );
    }

    public function settingsUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->find($id);

        if (!$setting) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier ce paramétrage. Celui-ci n\'existe pas.'
            );

            return $this->redirect($this->generateUrl('fiscalite_controle_budget_settings'));
        }

        $valueFirme = $request->request->get('valueFirme');

        if (!$valueFirme) {
            $type = $setting->getType();
            $em->remove($setting);
            $em->flush();

            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Paramétrage supprimé avec succès.'
            );

            if ($type == "CODE") {
                return $this->redirect($this->generateUrl('fiscalite_controle_budget_settings_code'));
            } else {
                return $this->redirect($this->generateUrl('fiscalite_controle_budget_settings_nature'));
            }
        }

        if ($setting->getType() == 'NATURE') {
            $valueFirme = strtoupper($valueFirme);
        }

        $setting->setValueFirme($valueFirme);
        $em->persist($setting);
        $em->flush();

        if ($setting->getType() == "CODE") {
            return $this->redirect($this->generateUrl('fiscalite_controle_budget_settings_code'));
        } else {
            return $this->redirect($this->generateUrl('fiscalite_controle_budget_settings_nature'));
        }
    }

    public function ajaxAddSettingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $valueSab = $request->query->get('valueSab');
        $valueCB = $request->query->get('valueCB');
        $type = $request->query->get('type');

        if ($valueCB == "") {
            $return = "KO";
        } else {
            if ($type == "nature") {
                $valueCB = strtoupper($valueCB);
            }

            $setting = new CBSettings();
            $setting->setValueSab($valueSab);
            $setting->setValueFirme($valueCB);
            $setting->setType(strtoupper($type));

            $em->persist($setting);
            $em->flush();

            $return = "OK";
        }

        return new Response($return);
    }
    
    public function submitAction(Request $request)
    {
        $datas = $request->request->get('form');
        $dateGene = $datas['dateGene'];
        $dirLocal = $this->container->getParameter('dirSortieDivers');
        $nameRealise = $this->container->getParameter('dwm.nameRea');
        $nameReference = $this->container->getParameter('dwm.nameRef');
        $em = $this->getDoctrine()->getManager();
        
        if (!$dateGene || (!isset($datas['cb_civ']) && !isset($datas['cb_cpt']))) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Une erreur de saisie est présente. Merci de recommencer.'
            );
            return $this->redirect($this->generateUrl('fiscalite_controle_budget'));
        }
        
        $doCiv = isset($datas['cb_civ']) ? true : false;
        $doCpt = isset($datas['cb_cpt']) ? true : false;
        
        $this->genereRefAnal($dateGene, $doCiv, $doCpt);
        $this->genereRealCpt($dateGene, $doCiv, $doCpt);
        
        if ($datas['extract'] == "SIM") {
            // on revient sur le formulaire avec affichage des résultats
            return $this->render('FiscaliteBudgetBundle:Default:index.html.twig', array(
                'datas' => $datas,
                'display' => true,
                'refPath' => base64_encode($dirLocal.date('ymd')."/".$nameReference),
                'reaPath' => base64_encode($dirLocal.date('ymd')."/".$nameRealise)
            ));
        } else {
            // On ajoute une nouvelle action qui gère le transfert vers DWM
            $action = new Action();

            $action
                ->setType('FISCALITE')
                ->setModule('BUDGET')
                ->setNumCpt(0);

            $em->persist($action);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Les contrôles budgétaires ont été générés et vont être transmis au service de gestion.'
            );
        
            // on revient sur le formulaire sans affichage des résultats
            return $this->render('FiscaliteBudgetBundle:Default:index.html.twig', array(
                'datas' => $datas,
                'display' => false
            ));
        }
    }
    
    private function genereRefAnal($date, $doCiv, $doCpt)
    {
        // Récupération des données
        $em = $this->getDoctrine()->getManager();
        $query = "SELECT TRIM(CPTCPOETA) AS CDE, CPTCPORUB, PLANINTIT FROM ZCPTCPO0, ZPLAN0 " .
            "WHERE CPTCPORUB = PLANCOOBL GROUP BY TRIM(CPTCPOETA), CPTCPORUB, PLANINTIT";
        $req = $em->getConnection()->prepare($query);
        $req->execute();
        $result = $req->fetchAll();
        
        // Initialisation
        $flux = "";
        $dateExtract = date('d/m/Y H:i:s');
        $nameReference = $this->container->getParameter('dwm.nameRef');
        
        // Ecriture du flux
        foreach ($result as $res) {
            // Récupération du sens de consolidation
            $setting = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->findOneBy(
                array('valueSab' => $res['CDE'], 'type' => 'NATURE')
            );

            if ($setting) {
                $sens = strtoupper($setting->getValueFirme());
            } else {
                $sens = "R";
            }

            if ($doCiv) {
                $ligne =
                    substr($date, 6, 4) . ";" .
                    "BQ-EXC" . ";" .
                    trim($res['CPTCPORUB']) . ";" .
                    trim($res['PLANINTIT']) . ";" .
                    $this->addCaractere(trim($res['CDE']), 4) . ";" .
                    $sens . ";" .
                    "BANQUE" . ";" .
                    $dateExtract .
                    "\r\n";
                
                $flux .= $ligne;
            }

            if ($doCpt) {
                $ligne =
                    substr($date, 6, 4) . ";" .
                    "BANQFI" . ";" .
                    trim($res['CPTCPORUB']) . ";" .
                    trim($res['PLANINTIT']) . ";" .
                    $this->addCaractere(trim($res['CDE']), 4) . ";" .
                    $sens . ";" .
                    "BANQUE" . ";" .
                    $dateExtract .
                    "\r\n";
                
                $flux .= $ligne;
            }
        }
        
        // Création du fichier
        $this->moveFile($flux, $nameReference);
    }
    
    private function genereRealCpt($date, $doCiv, $doCpt)
    {
        // Init variables
        $anneExtract = substr($date, 6, 4);
        $dateCiv = $anneExtract . "0101";
        if (substr($date, 3, 2) > "09") {
            $dateCpt = $anneExtract . "1001";
        } else {
            $dateCpt = $anneExtract - 1 . "1001";
        }
        $dateExtract = substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
        $flux = "";
        $arr_temp = array();
        $arr_temp2 = array();
        $dateCompExtract = date('d/m/Y H:i:s');
        $nameRealise = $this->container->getParameter('dwm.nameRea');
        
        // Récupération des données
        $em = $this->getDoctrine()->getManager();
        if ($doCiv) {
            $queryCiv = "SELECT TRIM(SUBSTR(CPTANADON, 9)) AS CDEBUD, DWHANARUB FROM ZCPTANA0, ZDWHANA0, ZCPTCPO0 " .
                "WHERE DWHANADAT >=  $dateCiv AND DWHANADAT <= $dateExtract AND CPTANACLE = DWHANACLE " .
                " AND DWHANARUB = CPTCPORUB GROUP BY TRIM(SUBSTR(CPTANADON, 9)), DWHANARUB " .
                " ORDER BY TRIM(SUBSTR(CPTANADON, 9)), DWHANARUB";
            $reqCiv = $em->getConnection()->prepare($queryCiv);
            $reqCiv->execute();
            $resultCiv = $reqCiv->fetchAll();
            
            // Ecriture du flux
            foreach ($resultCiv as $resCiv) {
                // Récupération du code budgétaire firme
                $setting = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->findOneBy(
                    array('valueSab' => $resCiv['CDEBUD'], 'type' => 'CODE')
                );

                if ($setting) {
                    $cdo_fir = $setting->getValueFirme();
                } else {
                    $cdo_fir = $resCiv['CDEBUD'];
                }

                // Réalisation du tableau intermédiaire (pour sommer avec les paramétrages)
                $arr_temp[$this->addCaractere($cdo_fir, 5)][trim($resCiv['DWHANARUB'])][]
                    = $this->getMontant("CIV", $resCiv, $date);
            }

            foreach ($arr_temp as $key_cde_bud => $arr_res) {
                foreach ($arr_res as $key_rub_cpt => $montants) {
                    // Ecriture
                    $ligne =
                        substr($date, 6, 4) . ";" .
                        $date . ";" .
                        "BQ-EXC" . ";" .
                        $key_cde_bud . ";" .
                        $key_rub_cpt . ";" .
                        array_sum($montants) . ";" .
                        $dateCompExtract .
                        "\r\n";

                    $flux .= $ligne;
                }
            }
        }
        if ($doCpt) {
            $queryCpt = "SELECT TRIM(SUBSTR(CPTANADON, 9)) AS CDEBUD, DWHANARUB FROM ZCPTANA0, ZDWHANA0, ZCPTCPO0 " .
                "WHERE DWHANADAT >= $dateCpt AND DWHANADAT <= $dateExtract AND CPTANACLE = DWHANACLE " .
                " AND DWHANARUB = CPTCPORUB GROUP BY TRIM(SUBSTR(CPTANADON, 9)), DWHANARUB " .
                " ORDER BY TRIM(SUBSTR(CPTANADON, 9)), DWHANARUB";
            $reqCpt = $em->getConnection()->prepare($queryCpt);
            $reqCpt->execute();
            $resultCpt = $reqCpt->fetchAll();
            
            // Ecriture du flux
            foreach ($resultCpt as $resCpt) {
                // Récupération du code budgétaire firme
                $setting = $em->getRepository('FiscaliteBudgetBundle:CBSettings')->findOneBy(
                    array('valueSab' => $resCpt['CDEBUD'], 'type' => 'CODE')
                );

                if ($setting) {
                    $cdo_fir = $setting->getValueFirme();
                } else {
                    $cdo_fir = $resCpt['CDEBUD'];
                }

                // Réalisation du tableau intermédiaire (pour sommer avec les paramétrages)
                $arr_temp2[$this->addCaractere($cdo_fir, 5)][trim($resCpt['DWHANARUB'])][]
                    = $this->getMontant("CPT", $resCpt, $date);
            }

            foreach ($arr_temp2 as $key_cde_bud => $arr_res) {
                foreach ($arr_res as $key_rub_cpt => $montants) {
                    // Ecriture
                    $ligne =
                        substr($date, 6, 4) . ";" .
                        $date . ";" .
                        "BANQFI" . ";" .
                        $key_cde_bud . ";" .
                        $key_rub_cpt . ";" .
                        array_sum($montants) . ";" .
                        $dateCompExtract .
                        "\r\n";

                    $flux .= $ligne;
                }
            }
        }
        
        // Création du fichier
        $this->moveFile($flux, $nameRealise);
    }
    
    private function moveFile($print, $filename)
    {
        $directory = $this->container->getParameter('dirSortieDivers');
        $fileManager = $this->container->get('backoffice_file.fileManager');
        
        $dateDir = $directory . '/' . date('ymd') . '/';
        
        if (!file_exists($dateDir)) {
            mkdir($dateDir);
        }
        
        // Créer le fichier
        $fileManager->ecrireFichier($dateDir, $filename, $print);
    }
    
    private function getMontant($type, $datas, $dateExtract)
    {
        $codeRub = substr($datas['DWHANARUB'], 0, 1);
        $anneeExtract = substr($dateExtract, 6, 4);
        $moisExtract = substr($dateExtract, 3, 2);
        $jourExtract = $moisExtract . substr($dateExtract, 0, 2);
        $dateFormat = substr($dateExtract, 6, 4) . $moisExtract . substr($dateExtract, 0, 2);
        
        $montantExtract = $this->getMontantAt($datas, $dateFormat);
        
        if ($type == "CIV") {
            if ($codeRub == 6 || $codeRub == 7) {
                return abs($montantExtract);
            } else {
                $anneePrec = $anneeExtract - 1;
                $montantFinAnnee = $this->getMontantAt($datas, $anneePrec . "1231");
                
                return abs($montantExtract - $montantFinAnnee);
            }
        } elseif ($type == "CPT") {
            if ($moisExtract == "09" || $moisExtract == "10" || $moisExtract == "11" || $moisExtract == "12") {
                $montantFinAnneCpt = $this->getMontantAt($datas, $anneeExtract . "0930");
                
                return abs($montantExtract - $montantFinAnneCpt);
            } else {
                if ($codeRub == 6 || $codeRub == 7) {
                    $anneePrec = $anneeExtract - 1;
                    $montantFinAnneCpt = $this->getMontantAt($datas, $anneePrec . "0930");
                    $montantFinAnnee = $this->getMontantAt($datas, $anneePrec . "1231");
                
                    return abs($montantExtract - $montantFinAnneCpt + $montantFinAnnee);
                } else {
                    $anneePrec = $anneeExtract - 1;
                    $montantFinAnneCpt = $this->getMontantAt($datas, $anneePrec . "0930");
                
                    return abs($montantExtract - $montantFinAnneCpt);
                }
            }
        }
    }
    
    private function getMontantAt($datas, $date)
    {
        $em = $this->getDoctrine()->getManager();
        
        $query = "SELECT SUM(DWHANASLD) AS MONTANT FROM ZCPTANA0, ZDWHANA0 " .
            "WHERE CPTANACLE = DWHANACLE AND DWHANADAT = '$date' AND DWHANARUB = '" . $datas['DWHANARUB'] .
            "' AND SUBSTR(CPTANADON, 9, 4) = '" . $this->addCaractere($datas['CDEBUD'], 4, ' ') . "'";

        $req = $em->getConnection()->prepare($query);
        $req->execute();
        $result = $req->fetch();
        
        return $result['MONTANT'];
    }
    
    private function addCaractere($content, $size, $caractere = '0', $position = 'left')
    {
        // on complete la chaine
        while (mb_strlen($content, 'utf-8') < $size) {
            if ($position == 'left') {
                $content = $caractere . $content;
            } else {
                $content = $content . $caractere;
            }
        }

        return $content;
    }
}
