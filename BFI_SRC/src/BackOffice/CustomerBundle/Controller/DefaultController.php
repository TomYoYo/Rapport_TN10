<?php

namespace BackOffice\CustomerBundle\Controller;

use BackOffice\ConnexionBundle\Manager\SabSFTPManager;
use BackOffice\CustomerBundle\Entity\Customer;
use BackOffice\CustomerBundle\Entity\CustomerRepository;
use BackOffice\CustomerBundle\Entity\SettingsCategorie;
use BackOffice\CustomerBundle\Entity\SettingsCivility;
use BackOffice\CustomerBundle\Entity\SettingsJuridique;
use BackOffice\CustomerBundle\Entity\SettingsQuality;
use BackOffice\CustomerBundle\Entity\SettingsResp;
use BackOffice\CustomerBundle\Entity\SettingsStateCode;
use BackOffice\CustomerBundle\Form\CustomerType;
use BackOffice\CustomerBundle\Manager\InformationsManager;
use Doctrine\ORM\EntityManager;
use mageekguy\atoum\tools\diff;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\False;

class DefaultController extends Controller
{
    public function indexAction()
    {
        /*$path = '/home/FIDUCIAL/app/exchange/SI_FIDINFO/in/';
        $date = date('Y-m-d');
        $file = $path."FI_Client_Financeur_GE_".$date.".csv";
        $row = 1;
        if(file_exists($file))
        {
            if(($handle = fopen($file,"r")) !== false)
            {
                while (($data = fgetcsv($handle, 0, "|",'*')) !== FALSE) {
                    $num = count($data);
                    echo "<p> $num champs à la ligne $row: <br /></p>\n";
                    $row++;
                    for ($c=0; $c < $num; $c++) {
                        echo $data[$c] . " ----".$c."<br />\n";
                    }
                }
                fclose($handle);
            }
        }
        else{
            echo "Pas de Fichier de ce jour";
        }*/
        return $this->render('BackOfficeCustomerBundle:Default:index.html.twig');
    }

    public function parametrageResponsableAction()
    {
        $request = $this->get('request');
        $manager = $this->get('backoffice.informationsmanager');
        $em = $this->getDoctrine()->getManager();
        $codes = $manager->getCodes();
        $codesForm = array();
        $i=0;
        $depts = array();
        $depts["01"] = "01 - Ain";
        $depts["02"] = "02 - Aisne";
        $depts["03"] = "03 - Allier";
        $depts["04"] = "04 - Alpes de Haute Provence";
        $depts["05"] = "05 - Hautes Alpes";
        $depts["06"] = "06 - Alpes Maritimes";
        $depts["07"] = "07 - Ardèche";
        $depts["08"] = "08 - Ardennes";
        $depts["09"] = "09 - Ariège";
        $depts["10"] = "10 - Aube";
        $depts["11"] = "11 - Aude";
        $depts["12"] = "12 - Aveyron";
        $depts["13"] = "13 - Bouches du Rhône";
        $depts["14"] = "14 - Calvados";
        $depts["15"] = "15 - Cantal";
        $depts["16"] = "16 - Charente";
        $depts["17"] = "17 - Charente Maritime";
        $depts["18"] = "18 - Cher";
        $depts["19"] = "19 - Corrèze";
        $depts["20"] = "20 - Corse";
        $depts["21"] = "21 - Côte d'Or";
        $depts["22"] = "22 - Côtes d'Armor";
        $depts["23"] = "23 - Creuse";
        $depts["24"] = "24 - Dordogne";
        $depts["25"] = "25 - Doubs";
        $depts["26"] = "26 - Drôme";
        $depts["27"] = "27 - Eure";
        $depts["28"] = "28 - Eure et Loir";
        $depts["29"] = "29 - Finistère";
        $depts["30"] = "30 - Gard";
        $depts["31"] = "31 - Haute Garonne";
        $depts["32"] = "32 - Gers";
        $depts["33"] = "33 - Gironde";
        $depts["34"] = "34 - Hérault";
        $depts["35"] = "35 - Ille et Vilaine";
        $depts["36"] = "36 - Indre";
        $depts["37"] = "37 - Indre et Loire";
        $depts["38"] = "38 - Isère";
        $depts["39"] = "39 - Jura";
        $depts["40"] = "40 - Landes";
        $depts["41"] = "41 - Loir et Cher";
        $depts["42"] = "42 - Loire";
        $depts["43"] = "43 - Haute Loire";
        $depts["44"] = "44 - Loire Atlantique";
        $depts["45"] = "45 - Loiret";
        $depts["46"] = "46 - Lot";
        $depts["47"] = "47 - Lot et Garonne";
        $depts["48"] = "48 - Lozère";
        $depts["49"] = "49 - Maine et Loire";
        $depts["50"] = "50 - Manche";
        $depts["51"] = "51 - Marne";
        $depts["52"] = "52 - Haute Marne";
        $depts["53"] = "53 - Mayenne";
        $depts["54"] = "54 - Meurthe et Moselle";
        $depts["55"] = "55 - Meuse";
        $depts["56"] = "56 - Morbihan";
        $depts["57"] = "57 - Moselle";
        $depts["58"] = "58 - Nièvre";
        $depts["59"] = "59 - Nord";
        $depts["60"] = "60 - Oise";
        $depts["61"] = "61 - Orne";
        $depts["62"] = "62 - Pas de Calais";
        $depts["63"] = "63 - Puy de Dôme";
        $depts["64"] = "64 - Pyrénées Atlantiques";
        $depts["65"] = "65 - Hautes Pyrénées";
        $depts["66"] = "66 - Pyrénées Orientales";
        $depts["67"] = "67 - Bas Rhin";
        $depts["68"] = "68 - Haut Rhin";
        $depts["69"] = "69 - Rhône";
        $depts["70"] = "70 - Haute Saône";
        $depts["71"] = "71 - Saône et Loire";
        $depts["72"] = "72 - Sarthe";
        $depts["73"] = "73 - Savoie";
        $depts["74"] = "74 - Haute Savoie";
        $depts["75"] = "75 - Paris";
        $depts["76"] = "76 - Seine Maritime";
        $depts["77"] = "77 - Seine et Marne";
        $depts["78"] = "78 - Yvelines";
        $depts["79"] = "79 - Deux Sèvres";
        $depts["80"] = "80 - Somme";
        $depts["81"] = "81 - Tarn";
        $depts["82"] = "82 - Tarn et Garonne";
        $depts["83"] = "83 - Var";
        $depts["84"] = "84 - Vaucluse";
        $depts["85"] = "85 - Vendée";
        $depts["86"] = "86 - Vienne";
        $depts["87"] = "87 - Haute Vienne";
        $depts["88"] = "88 - Vosges";
        $depts["89"] = "89 - Yonne";
        $depts["90"] = "90 - Territoire de Belfort";
        $depts["91"] = "91 - Essonne";
        $depts["92"] = "92 - Hauts de Seine";
        $depts["93"] = "93 - Seine St Denis";
        $depts["94"] = "94 - Val de Marne";
        $depts["95"] = "95 - Val d'Oise";
        $depts["97"] = "97 - DOM";
        foreach ($codes as $code) {
            $codesForm[$i] = $code['BAS006004'];
            $i++;
        }

        $form = $this->createFormBuilder()
            ->add('codes','choice',array(
                'choices' => $codesForm,
                'empty_value' => 'Choisissez une option',
                'required' => true
            ))
        ->add('deptChoice','choice',array(
            'choices'=> $depts,
            'required' => true,
            'multiple' => true,
            'expanded' => true
        ))->getForm();

        if($request->getMethod()=='POST')
        {
            $form->handleRequest($request);
            $data = $form->getData();
            if ($form->isValid()) {
                $code = $em->getRepository('BackOfficeCustomerBundle:SettingsResp')->findOneBy(array('codeResponsable'=>$codesForm[$data['codes']]));
                if(!$code)
                {
                    $code = new SettingsResp();
                    $code->setCodeResponsable($codesForm[$data['codes']]);
                }
                $code->setDepartement($data['deptChoice']);
                $em->persist($code);
                $em->flush();
                return $this->redirect($this->generateUrl('back_office_customer_parametrage_responsable'));
            }
        }

        return $this->render('BackOfficeCustomerBundle:Default:settingsCodes.html.twig',array(
            'form' => $form->createView()
        ));

    }


    public function parametrageCivilityAction()
    {
        /**
         * @var InformationsManager $manager
         */
        $manager = $this->get('backoffice.informationsmanager');
        $em = $this->getDoctrine()->getManager();
        $civs = $em->getRepository('BackOfficeCustomerBundle:SettingsCivility')->findAll();
        $civilities = array();
        /**
         * @var SettingsCivility $civ
         */
        foreach ($civs as $civ) {
            $civilities[$civ->getId()] = $civ->getIntitule();
        }
        $form = $this->createFormBuilder()->add('formeJuridique','choice',array(
            'choices' => $manager->getListJuridiqueForm()
        ))->add('civility','choice',array(
            'choices' => $civilities
        ))->getForm();
        return $this->render('BackOfficeCustomerBundle:Default:settingsCivility.html.twig',array(
            'form' => $form->createView()
        ));
    }

    public function parametrageEtatAction()
    {
        $manager = $this->get('backoffice.informationsmanager');
        $em = $this->getDoctrine()->getManager();
        $states = $em->getRepository('BackOfficeCustomerBundle:SettingsStateCode')->findAll();
        $statesFrom = array();
        /**
         * @var SettingsStateCode $state
         */
        foreach ($states as $state) {
            $statesFrom[$state->getId()] = $state->getIntitule();
        }
        $form = $this->createFormBuilder()->add('formeJuridique_state','choice',array(
            'choices' => $manager->getListJuridiqueForm()
        ))->add('state','choice',array(
            'choices' => $statesFrom
        ))->getForm();
        return $this->render('BackOfficeCustomerBundle:Default:settingsStatesCode.html.twig',array(
            'form' => $form->createView()
        ));
    }

    public function anomaliesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $anomalies = $em->getRepository('BackOfficeCustomerBundle:Anomalie')->findAll();
        return $this->render('BackOfficeCustomerBundle:Default:anomalie.html.twig',array(
            'anomalies' => $anomalies
        ));
    }

    public function anomalieDeleteAction($id)
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $anomalie = $em->getRepository('BackOfficeCustomerBundle:Anomalie')->findOneBy(array('id'=>$id));
        $em->remove($anomalie);
        $em->flush();
        return $this->redirect($this->generateUrl('back_office_customer_anomalies'));
    }


    public function customerListAction()
    {
        /**
         * @var EntityManager $em
         */
        $request = $this->get('request');
        $page = $request->get('page');
        $info = $request->get('info');
        $error = $request->get('error');
        $statut = $request->get('statut');
        $em = $this->getDoctrine()->getManager();
        $customers = $em->getRepository('BackOfficeCustomerBundle:Customer')->findBy(array('statut'=>$statut),array('idCustomer'=>'ASC'));
        $adapter = new ArrayAdapter($customers);
        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setCurrentPage($page);
        return $this->render('BackOfficeCustomerBundle:Customer:list.html.twig',array(
            'customers' => $pagerFanta->getCurrentPageResults(),
            'pager' => $pagerFanta,
            'statut' => $statut,
            'info' => $info,
            'error' => $error
        ));
    }

    public function customerAction($id)
    {
        /**
         * @var EntityManager $em
         */
        /**
         * @var Request $request
         */
        /**
         * @var Customer $customer
         */
        /**
         * @var Form $form
         */
        /**
         * @var InformationsManager $manager
         */
        $manager = $this->get('backoffice.informationsmanager');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('BackOfficeCustomerBundle:Customer')->findOneBy(array('id'=>$id));
        $old_customer = clone $customer;
        $info = $manager->checkData($customer);
        $form = $this->get('form.factory')->create(new CustomerType(),$customer);
        if($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $this->setMofification($customer,$old_customer);
                if(!$result = $manager->checkData($customer) )
                {
                    $customer->setanomaliesComments(null);
                    $customer->setstatut(3);
                    if(!$customer->getExist())
                    {
                        if($manager->checkIfCustomerExist($customer))
                        {
                            $customer->setExist(true);
                        }
                    }
                    $em->persist($customer);
                    $em->flush();
                    $this->createCustomer($customer);
                    return $this->redirect($this->generateUrl('back_office_customer_modified',array(
                        'info' => 'client Envoyé'
                    )));
                }
                else
                {
                    $em->persist($customer);
                    $em->flush();
                    if($customer->getanomaliesComments() != null)
                    {
                        return $this->redirect($this->generateUrl('back_office_customer_anomalies_sab',array(
                            'info' => 'Client enregistré mais non complet pour envoi à SAB'
                        )));
                    }
                    else
                    {
                        return $this->redirect($this->generateUrl('back_office_customer_tocomplete',array(
                            'info' => 'Client enregistré mais non complet pour envoi à SAB'
                        )));
                    }

                }
            }
        }
        return $this->render('BackOfficeCustomerBundle:Customer:customer.html.twig',array(
            'form' => $form->createView(),
            'customer' => $customer,
            'statut' => $customer->getstatut(),
            'info' => $info
        ));
    }

    public function sendAction($id)
    {
       $em = $this->getDoctrine()->getManager();
        /**
         * @var InformationsManager $manager
         */
        /**
         * @var Customer $customer
         */
        $manager = $this->get('backoffice.informationsmanager');
        $customer = $em->getRepository('BackOfficeCustomerBundle:Customer')->findOneBy(array('id'=>$id));
        if(!$result = $manager->checkData($customer) )
        {
            $customer->setanomaliesComments(null);
            $customer->setstatut(3);
            if(!$customer->getExist())
            {
                if($manager->checkIfCustomerExist($customer))
                {
                    $customer->setExist(true);
                }
            }
            $em->persist($customer);
            $em->flush();
            $this->createCustomer($customer);
            return $this->redirect($this->generateUrl('back_office_customer_modified',array(
                'info' => 'client Envoyé'
            )));
        }
        else
        {
            if($customer->getstatut() == 5)
            {
                return $this->redirect($this->generateUrl('back_office_customer_anomalies_sab',array(
                    'error' => $result
                )));
            }
            else
            {
                return $this->redirect($this->generateUrl('back_office_customer_tocomplete',array(
                    'error' => $result
                )));
            }
        }
    }

    public function setMofification(Customer $customer,Customer $old_customer)
    {
        if($customer->getadresse() != $old_customer->getadresse())
        {
            $customer->setadresse_mod(true);
        }
        if($customer->getcP() != $old_customer->getcP())
        {
            $customer->setcP_mod(true);
        }
        if($customer->getville() != $old_customer->getville())
        {
            $customer->setville_mod(true);
        }
        if($customer->getpays() != $old_customer->getpays())
        {
            $customer->setpays_mod(true);
        }
        if($customer->getcodeApe() != $old_customer->getcodeApe())
        {
            $customer->setcodeape_mod(true);
        }
        if($customer->getcodeEtat() != $old_customer->getcodeEtat())
        {
            $customer->setetat_mod(true);
        }
        if($customer->getcodeCivilite() != $old_customer->getcodeCivilite())
        {
            $customer->setcivility_mod(true);
        }
        if($customer->getqualiteClient() != $old_customer->getqualiteClient())
        {
            $customer->setquality_mod(true);
        }
        if($customer->getSiren() != $old_customer->getSiren())
        {
            $customer->setsiren_mod(true);
        }
        if($customer->getcodeNic() != $old_customer->getcodeNic())
        {
            $customer->setcodenic_mod(true);
        }
        if($customer->getemail() != $old_customer->getemail())
        {
            $customer->setemail_mod(true);
        }
        if($customer->gettel() != $old_customer->gettel())
        {
            $customer->settel_mod(true);
        }
        if($customer->getfax() != $old_customer->getfax())
        {
            $customer->setfax_mod(true);
        }
        if($customer->getcapital() != $old_customer->getcapital())
        {
            $customer->setcapital_mod(true);
        }

    }

    public function transcodageAction($id)
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        /**
         * @var InformationsManager $manager
         */
        /**
         * @var Customer $customer
         */
        $manager = $this->get('backoffice.informationsmanager');
        $customer = $em->getRepository('BackOfficeCustomerBundle:Customer')->findOneBy(array('id'=>$id));
        if($customer->getformeJuridiqueExt() != null)
        {
            $stateCode = $manager->getStateCode($customer->getformeJuridiqueExt());
            $civlity = $manager->getCivilityCode($customer->getformeJuridiqueExt());
            $quality = $manager->getQuality($customer->getformeJuridiqueExt());
            if($stateCode && $stateCode != $customer->getcodeEtat())
            {
                $customer->setcodeEtat($stateCode);
            }
            if($civlity && $civlity != $customer->getcodeCivilite())
            {
                $customer->setcodeCivilite($civlity);
            }
            if($quality && $quality != $customer->getcodeCivilite())
            {
                $customer->setqualiteClient($quality);
            }
            $em->persist($customer);
            $em->flush();
        }
            return $this->redirect($this->generateUrl('back_office_customer_detail',array('id'=>$id)));
    }

    public function createCustomer(Customer $customer)
    {
        /**
         * @var InformationsManager $manager
         */
        $manager = $this->get('backoffice.informationsmanager');
        $manager->createCustomerFile($customer);

    }

public function parametrageJuridiqueAction()
{
    $em = $this->getDoctrine()->getManager();
    $civs = $em->getRepository('BackOfficeCustomerBundle:SettingsJuridique')->findAll();
    $civilities = array();
    /**
     * @var SettingsJuridique $civ
     */
    foreach ($civs as $civ) {
        $civilities[$civ->getId()] = $civ->getIntitule();
    }
    $form = $this->createFormBuilder()->add('code','text',array(
    ))->add('juridique','choice',array(
        'choices' => $civilities
    ))->getForm();
    return $this->render('BackOfficeCustomerBundle:Default:settingsJuridique.html.twig',array(
        'form' => $form->createView()
    ));
}

    public function parametrageQualityAction()
    {
        /**
         * @var InformationsManager $manager
         */
        $manager = $this->get('backoffice.informationsmanager');
        $em = $this->getDoctrine()->getManager();
        $qualitiesPar = $em->getRepository('BackOfficeCustomerBundle:SettingsQuality')->findAll();
        $qualities = array();
        /**
         * @var SettingsQuality $setting
         */
        foreach ($qualitiesPar as $setting) {
            $qualities[$setting->getId()] = $setting->getIntitule();
        }
        $form = $this->createFormBuilder()->add('forme_juridique','choice',array(
            'choices' => $manager->getListJuridiqueForm()
        ))->add('quality','choice',array(
            'choices' => $qualities
        ))->getForm();
        return $this->render('BackOfficeCustomerBundle:Default:settingsQuality.html.twig',array(
            'form' => $form->createView()
        ));
    }

    public function parametrageCategoryAction()
    {
        /**
         * @var InformationsManager $manager
         */
        $manager = $this->get('backoffice.informationsmanager');
        $em = $this->getDoctrine()->getManager();
        $qualitiesPar = $em->getRepository('BackOfficeCustomerBundle:SettingsCategorie')->findAll();
        $qualities = array();
        /**
         * @var SettingsCategorie $setting
         */
        foreach ($qualitiesPar as $setting) {
            $qualities[$setting->getId()] = $setting->getCode();
        }
        $form = $this->createFormBuilder()->add('forme_juridique_cat','choice',array(
            'choices' => $manager->getListJuridiqueForm()
        ))->add('category','choice',array(
            'choices' => $qualities
        ))->getForm();
        return $this->render('BackOfficeCustomerBundle:Default:settingsCategory.html.twig',array(
            'form' => $form->createView()
        ));
    }

    //Requêtes AJAX

    public function getDeptAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $code = $request->get('code');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $codes = $em->getRepository('BackOfficeCustomerBundle:SettingsResp')->findAll();
            //$data['others'] = array();
            $data = array();
            $data['own']=array();
            $data['others']=array();
            /**
             * @var SettingsResp $cod
             */
            foreach($codes as $cod)
            {
                if($cod->getCodeResponsable() == $code )
                {
                    $data['own'] = $cod->getDepartement();
                }
                else
                {
                    foreach($cod->getDepartement() as $dept)
                    {
                        array_push($data['others'],$dept);
                    }
                }
            }
            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
    }

    public function searchRespAction()
    {
        $request = $this->get('request');
        $manager = $this->get('backoffice.informationsmanager');
        $dept = $request->get('dept');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax


            $codes = $manager->getCodes();

            $i=0;
            $data['result'] = null;
            $resp = $manager->getResponsable($dept);
            foreach($codes as $code)
            {
                if($code['BAS006004'] == $resp)
                {
                    $data['result'] = $i;
                    break;
                }
                $i++;
            }

            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
    }

    public function addCivilityAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('backoffice.informationsmanager');
        $code = $request->get('code');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var InformationsManager $manager
             */
            if($manager->checkForme($forme))
            {
                /**
                 * @var SettingsCivility $civility
                 */
                $civility = $em->getRepository('BackOfficeCustomerBundle:SettingsCivility')->findOneBy(array('id'=>$code));
                $test = $civility->addForme($forme);
                if($test)
                {
                    $em->flush();
                    $data = array();
                    $data['code'] = $civility->getCivilityCode();
                    $data['nope'] = false;
                }
                else
                {
                    $data['nope'] = true;
                }
            }
            else
            {
                $data['nope'] = true;
            }





            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }

    }

    public function removeCivilityAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $code = $request->get('code');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsCivility $civility
             */
            $civility = $em->getRepository('BackOfficeCustomerBundle:SettingsCivility')->findOneBy(array('id'=>$code));
            $civility->delForme($forme);
            $em->flush();
            $data = array();
            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function initCivilityAction(){
        $request = $this->get('request');
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsCivility $civility
             */
            $civility = $em->getRepository('BackOfficeCustomerBundle:SettingsCivility')->findOneBy(array('id'=>$id));
            $response = new Response();
            $data['code'] = $civility->getCivilityCode();
            $data['formes'] = $civility->getJuridiqueForm();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function addStateAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('backoffice.informationsmanager');
        $code = $request->get('code');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var InformationsManager $manager
             */
            if($manager->checkFormeState($forme))
            {
                $data['nope'] = false;

                /**
                 * @var SettingsStateCode $state
                 */
                $state = $em->getRepository('BackOfficeCustomerBundle:SettingsStateCode')->findOneBy(array('id'=>$code));
                $test = $state->addForme($forme);
                if($test)
                {
                    $em->flush();
                    $data = array();
                    $data['nope'] = false;
                }
                else
                {
                    $data['nope'] = true;
                }
            }
            else
            {
                $data['nope'] = true;
            }





            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }

    }

    public function removeStateAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $code = $request->get('code');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsStateCode $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsStateCode')->findOneBy(array('id'=>$code));
            $state->delForme($forme);
            $em->flush();
            $data = array();
            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function initStateAction(){
        $request = $this->get('request');
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsStateCode $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsStateCode')->findOneBy(array('id'=>$id));
            $response = new Response();
            $data = array();

            if($state->getJuridiqueForme() != null)
            {
                $data['formes'] = $state->getJuridiqueForme();
            }
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function addNaceAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('backoffice.informationsmanager');
        $nace = $request->get('nace');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var InformationsManager $manager
             */
            if($manager->checkNace($nace))
            {
                $data['nope'] = false;

                /**
                 * @var SettingsJuridique $state
                 */
                $state = $em->getRepository('BackOfficeCustomerBundle:SettingsJuridique')->findOneBy(array('id'=>$forme));
                $test = $state->addNace($nace);
                if($test)
                {
                    $em->flush();
                    $data = array();
                    $data['nope'] = false;
                }
                else
                {
                    $data['nope'] = true;
                }
            }
            else
            {
                $data['nope'] = true;
           }





            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function addQualityAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('backoffice.informationsmanager');
        $quality = $request->get('quality');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var InformationsManager $manager
             */
            if($manager->checkFormeQuality($forme))
            {
                $data['nope'] = false;

                /**
                 * @var SettingsQuality $state
                 */
                $state = $em->getRepository('BackOfficeCustomerBundle:SettingsQuality')->findOneBy(array('id'=>$quality));
                $test = $state->addForme($forme);
                if($test)
                {
                    $em->flush();
                    $data = array();
                    $data['nope'] = false;
                }
                else
                {
                    $data['nope'] = true;
                }
            }
            else
            {
                $data['nope'] = true;
            }





            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function initNaceAction(){
        $request = $this->get('request');
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsJuridique $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsJuridique')->findOneBy(array('id'=>$id));
            $response = new Response();
            $data = array();

            if($state->getNace() != null)
            {
                $data['naces'] = $state->getNace();
            }
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }
    public function initQualityAction(){
        $request = $this->get('request');
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsQuality $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsQuality')->findOneBy(array('id'=>$id));
            $response = new Response();
            $data = array();

            if($state->getFormes() != null)
            {
                $data['formes'] = $state->getFormes();
            }
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function initCategoryAction()
    {
        $request = $this->get('request');
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsCategorie $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsCategorie')->findOneBy(array('id'=>$id));
            $response = new Response();
            $data = array();

            if($state->getFormes() != null)
            {
                $data['formes'] = $state->getFormes();
            }
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }
    public function removeNaceAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $code = $request->get('id');
        $nace = $request->get('nace');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsJuridique $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsJuridique')->findOneBy(array('id'=>$code));
            $state->delnace($nace);
            $em->flush();
            $data = array();
            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function removeCategoryAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $code = $request->get('id');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsCategorie $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsCategorie')->findOneBy(array('id'=>$code));
            $state->delForme($forme);
            $em->flush();
            $data = array();
            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }


    public function addCategoryAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('backoffice.informationsmanager');
        $categorie = $request->get('categorie');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var InformationsManager $manager
             */
            if($manager->checkFormeCategorie($forme))
            {
                $data['nope'] = false;

                /**
                 * @var SettingsCategorie $state
                 */
                $state = $em->getRepository('BackOfficeCustomerBundle:SettingsCategorie')->findOneBy(array('id'=>$categorie));
                $test = $state->addForme($forme);
                if($test)
                {
                    $em->flush();
                    $data = array();
                    $data['nope'] = false;
                }
                else
                {
                    $data['nope'] = true;
                }
            }
            else
            {
                $data['nope'] = true;
            }





            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    public function removeQualityAction()
    {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $code = $request->get('id');
        $forme = $request->get('forme');
        // pour vérifier la présence d'une requete Ajax
        if ($request->isXmlHttpRequest()) {

            /**
             * @var SettingsQuality $state
             */
            $state = $em->getRepository('BackOfficeCustomerBundle:SettingsQuality')->findOneBy(array('id'=>$code));
            $state->delForme($forme);
            $em->flush();
            $data = array();
            $response = new Response();
            $data = json_encode($data);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;

        }
    }

    }
