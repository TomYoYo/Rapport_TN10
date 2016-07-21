<?php

namespace BackOffice\HabilitationBundle\Controller;


use BackOffice\HabilitationBundle\Entity\UserSab;
use BackOffice\HabilitationBundle\Form\CollaterauxType;
use BackOffice\HabilitationBundle\Form\FiltresType;
use BackOffice\HabilitationBundle\Form\ServiceType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\DefaultView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    //page Accueil liste des utilisateurs SAB
    public function indexAction($desactivate = false,$page = 1)
    {
            $request = $this->get('request');
            $session = $request->getSession();
            if(!$session->has('filter'))
            {
                $session->set('filter',false);
            }
            $manager = $this->get('backoffice.userManager');
            $em = $this->getDoctrine()->getManager();

            $data = null;

            $metierForm = array();
            $donneesForm = array();
            $menusForm = array();

            $metiers = $manager->getHabilitation(4);
            $donnees = $manager->getHabilitation(3);
            $menus = $manager->getHabilitation(2);

            $i = 0;
            foreach ($metiers as $metier) {
                $metierForm[$i] = $metier['MNUGRPNOM'];
                $i++;
            }
            $i = 0;
            foreach ($donnees as $donnee) {
                $donneesForm[$i] = $donnee['MNUGRPNOM'];
                $i++;
            }
            $i = 0;
            foreach ($menus as $menu) {
                $menusForm[$i] = $menu['MNUGRPNOM'];
                $i++;
            }

            $form = $this->createFormBuilder()->add('filtres', new FiltresType(array('menu' => $menusForm, 'metier' => $metierForm, 'donnees' => $donneesForm)), array(
                'required' => false

            ))->add('desactivate','choice',array(
                'choices' => array(
                    1 => 'Non',
                    2 => 'Oui'
                ),
                'empty_value' => '',
                'required' => false
            ))->getForm();
            $metier = null;
            $menu = null;
            $donnee = null;

        if($page == 'reset')
        {
            $session->set('filter',false);
            $session->set('login',null);
            $session->set('desactivate',0);
            $page = 1;
        }

        if($page == 'desactivate')
        {
            $users = $manager->getAllUsers(null,null,null,null,true);
        }
        else
        {
            //récupération des utilisateurs
            if ($request->getMethod() == 'GET' ) {
                    if($session->get('filter') == false)
                    {
                        $users = $manager->getAllUsers(null);

                    }else
                    {
                    if ($session->get('filtres')['metier'] !== null) {
                        $metier = $metierForm[$session->get('filtres')['metier']];
                        $form->get('filtres')['metier']->setData($session->get('filtres')['metier']);
                    }
                    if ($session->get('filtres')['menu'] !== null) {
                        $menu = $menusForm[$session->get('filtres')['menu']];
                        $form->get('filtres')['menu']->setData($session->get('filtres')['menu']);

                    }
                    if ($session->get('filtres')['donnees'] !== null) {
                        $donnee = $donneesForm[$session->get('filtres')['donnees']];
                        $form->get('filtres')['donnees']->setData($session->get('filtres')['donnees']);
                    }
                        $form->get('desactivate')->setData($session->get('desactivate'));
                        $users = $manager->getAllUsers($session->get('login'), $menu, $metier, $donnee,$session->get('desactivate'));
                    }
            }
            else // récupération des utilisateurs filtrés
            {
                $data = $request->request->get('Login');
                $form->handleRequest($request);
                $datas = $form->getData();
                $filtres = $datas['filtres'];
                $session->set('filter',true);
                $session->set('login',$data);
                $session->set('filtres',$filtres);
                if ($filtres['metier'] !== null) {
                    $metier = $metierForm[$filtres['metier']];
                }
                if ($filtres['menu'] !== null) {
                    $menu = $menusForm[$filtres['menu']];
                }
                if ($filtres['donnees'] !== null) {
                    $donnee = $donneesForm[$filtres['donnees']];
                }
                $session->set('desactivate',$datas['desactivate']);
                $users = $manager->getAllUsers($data, $menu, $metier, $donnee,$session->get('desactivate'));
            }
        }
        $newUsers = array();
        foreach($users as $user)
        {
            if($user['hab2'] == '')
            {
                $userBFI = $em->getRepository('BackOfficeHabilitationBundle:UserSab')->findOneBy(array('idsab'=>(int)$user['code']));
                if($userBFI)
                {
                    $user['hab2'] = $userBFI->getGroupe2();
                    $user['hab3'] = $userBFI->getGroupe3();
                    $user['hab4'] = $userBFI->getGroupe4();
                }
            }
            array_push($newUsers,$user);
        }
        $users = array();
        $users = $newUsers;
            //informations de réussite ou d'échec de procédure du module
        $error = $request->get('error');
        $info = $request->get('info');
        if($page != 'desactivate')
        {

        $adapter = new ArrayAdapter($users);
        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setCurrentPage($page);

        return $this->render('BackOfficeHabilitationBundle:Default:index.html.twig', array(
            'users' => $pagerFanta->getCurrentPageResults(),
            'data' => $session->get('login'),
            'error' => $error,
            'info' => $info,
            'form' => $form->createView(),
            'pager' => $pagerFanta
        ));
        }else
        {
            return $this->render('BackOfficeHabilitationBundle:Default:index.html.twig', array(
                'users' => $users,
                'data' => $data,
                'error' => $error,
                'info' => $info,
                'form' => $form->createView(),
                'pager' => false
            ));
        }

    }
    //désactivation de l'entrée utilisateur
    public function userDesactivateAction($id)
    {
        $logManager = $this->get('backoffice_monitoring.logManager');
        $em = $this->getDoctrine()->getManager();
        $manager = $this->get('backoffice.userManager');

        //infos utilisateur
        $zmnuuti0 = $manager->getMoreInformations($id);
        //vérification de la présence de l'utilisateur en base BFI
        $user = $em->getRepository('BackOfficeHabilitationBundle:UserSab')->findOneBy(array('idsab'=>(int)$id));

        //si pas d'utilisateur : création
        if(!$user)
        {
            $user = new UserSab();
            $user->setIdSab($id);
        }

        //mise a jour infos utilisateur base BFI
        $user->setAgence($zmnuuti0['MNUUTIAGE']);
        $user->setGroupe2($zmnuuti0['MNUUTIGR2']);
        $user->setGroupe3($zmnuuti0['MNUUTIGR3']);
        $user->setGroupe4($zmnuuti0['MNUUTIGR4']);
        $user->setMenu($zmnuuti0['MNUUTIMSE']);
        $user->setService($zmnuuti0['MNUUTISER']);
        $user->setSousService($zmnuuti0['MNUUTISRV']);
        $user->setFile($zmnuuti0['MNUUTIOUT']);

        $em->persist($user);
        $em->flush();

        //méthode de désactivation
        $manager->updateEnter($id,false,$zmnuuti0);

        return $this->redirect($this->generateUrl('back_office_habilitation',array('info' => "L'utilisateur a été desactivé")));
    }

    public function userActivateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $logManager = $this->get('backoffice_monitoring.logManager');
        $manager = $this->get('backoffice.userManager');

        //vérification de la présence de l'utilisateur en base BFI
        $user = $em->getRepository('BackOfficeHabilitationBundle:UserSab')->findOneBy(array('idsab'=>(int)$id));

        //si user présent : réactivation, si non : log erreur l'utilisateur a été désactivé hors BFI
        if($user)
        {
            $manager->updateEnter($id,true,$user);
        }
        else
        {
            //log erreur
            $libellé = "Echec lors de l'activation de l'utilisateur : " .$id;
            $logManager->addError($libellé,"BackOffice > Habilitations SAB","Activation Utilisateur");
            return $this->redirect($this->generateUrl('back_office_habilitation',array('error' => "Cet utilisateur n'a pas pu être réactivé, contactez votre administrateur")));

        }
        return $this->redirect($this->generateUrl('back_office_habilitation',array('info' =>"L'utilisateur a été réactivé")));

    }

    public function createUserAction()
    {
        $logManager = $this->get('backoffice_monitoring.logManager');
        $request = $this->get('request');
        $session = $request->getSession();
        $manager = $this->get('backoffice.userManager');
        $user = $this->get('security.context')->getToken()->getUser();
        $rcci = false;
        if($user->hasRole('ROLE_RCCI'))
        {
            $rcci = true;
        }
        //libellé groupe habilitation
        $metiers = $manager->getHabilitation(4);
        $donnees = $manager->getHabilitation(3);
        $menus = $manager->getHabilitation(2);

        //noms utilisateur SAB
        $users = $manager->getAllUsersRespName();
        //libellés agences
        $agences = $manager->getAgences();
        //libellés workflow
        $workflows = $manager->getWorkflow();
        //libellés files d'attentes
        $files = $manager->getFiles();
        //initialisation des tableaux de données pour formulaire
        $usersForm = array();
        $metierForm = array();
        $donneesForm = array();
        $menusForm = array();
        $agencesForm = array();
        $filesForm = array();
        //code responsable
        $codeResp = $manager->getCodeRes();

        //variables post login : duplication, login_modif : modification
        $login = $request->get('login');
        $login_modif = $request->get('login_modif');

        if($request->get('error'))
        {
            $error = $request->get('error');
        }
        else
        {
            $error = '';
        }

        //variables booléennes pour rendu template
        $modification = false;
        $code = false;
        $button = true;


        if($request->getMethod() != 'POST')
        {
            //POST
            if($login != '')
            {
                //DUPLICATION
                $libellé = "Démarrage de la duplication de l'utilisateur : ".$login;
                $action = 'Duplication Utilisateur';
                $session->set('login_dup',$login);
            }
            elseif($login_modif != '')
            {
                //MODIFICATION
                $libellé = "Démarrage de la Modification de l'utilisateur : ".$login_modif;
                $action = 'Modification Utilisateur';
            }
            else
            {
                //CREATION
                $libellé = "Démarrage de la création d'un nouvel utilisateur";
                $action = 'Création Utilisateur';
            }
            //log info
            $logManager->addInfo($libellé,'BackOffice > Habilitation SAB',$action);
        }

        //mise en forme des données du formulaire (valeur string non supporté)
        $i=0;
        foreach($users as $user)
        {
            $usersForm[$i] = $user['BAS006011'];
            $i++;
        }
        $i=0;
        foreach ($metiers as $metier) {
            $metierForm[$i] = $metier['MNUGRPNOM'];
            $i++;
        }
        $i=0;
        foreach ($donnees as $donnee) {
            $donneesForm[$i] = $donnee['MNUGRPNOM'];
            $i++;
        }
        $i=0;
        foreach ($menus as $menu) {
            $menusForm[$i] = $menu['MNUGRPNOM'];
            $i++;
        }
        foreach($agences as $agence)
        {
            $agencesForm[$agence['MNUAGEAGE']] = $agence['MNUAGELIB'];
        }
        $i=0;
        foreach($files as $file)
        {
            $filesForm[$i] = $file['MNUOUQOUT'];
            $i++;
        }
        $profils = array(
            1 => 'CRC',
            2 => 'DIRECT',
            3 => 'RESCRC',
            4 => 'RESEAU'
        );

        //PRT01 par défaut dans la liste
        $filesForm = array_reverse($filesForm);

        //création formulaire
        $form = $this->createFormBuilder()
            ->add('menu','choice',array(
                'choices' => $menusForm,
                'empty_value' => 'Choisissez une option',
                'required' => true
            ))
            ->add('collateraux', 'collection', array(
                'type' => new CollaterauxType(),
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true
            ))
            ->add('collaterauxother','collection',array(
                'type' => new CollaterauxType(),
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true
            ))
            ->add('contentieux','choice',array(
                'choices' => array(
                    2 => 'Non',
                     1 => 'Oui',
                )
            ))
            ->add('workflow','choice',array(
                'choices' => $workflows,
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ))

            ->add('file','choice',array(
                'required' => true,
                'choices' =>$filesForm
            ))
            ->add('dossier','choice',array(
                'required' => false,
                'choices' => array(
                    'N' => 'Non',
                    'O' => 'Oui'

                ),
                'empty_value' => false
            ))
            ->add('service','hidden',array(
                'required' => true,
            ))
            ->add('sservice','hidden',array(
                'required' => true,
            ))
            ->add('isCode','checkbox',array(
                'required' => false,
                'label' => "Code responsable requis"
            ))->add('compteRendu','checkbox',array(
                'required' => false,
            ))
            ->add('correspondant','choice',array(
                'choices' => array('PM '=>'Personne Morale','PP '=>'Personne Physique'),
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ));

        //champ différents si modification (read_only, empty value, required)
        if($login_modif != '')
        {
            $modification = true;
            $form->add('name','text',array(
                'required' => true,
                'read_only' => true
            ))
                ->add('firstName','text',array(
                    'required'=> true,
                    'read_only' => true
                ))
                ->add('metiers','choice',array(
                    'choices'=> $metierForm,
                    'required' => true
                ))
                ->add('donnees','choice',array(
                    'choices' => $donneesForm,
                    'required' => true
                ))
                ->add('agences','choice',array(
                    'choices' => $agencesForm,
                    'required' => true,
                ))
                ->add('superieur','choice',array(
                    'required' => false,
                    'choices' => $usersForm,
                    'empty_value'=> false
                ))
                ->add('abbr','text',array(
                        'required' => false,
                        'read_only' => false
                    )
                )
                ->add('lib','text',array(
                        'required' => false,
                        'read_only' => false
                    )
                )
                ->add('profil','choice',array(
                    'required' => true,
                    'choices' => $profils,
                    'empty_value'=> false,
                    'read_only' => false
                ));
        }
        else
        {
            $form->add('name','text',array(
            'required' => true
             ))
            ->add('firstName','text',array(
                'required'=> true
            ))
                ->add('codeRes','text',array(
                    'required' => false,
                ))
                ->add('repertoire','checkbox',array(
                    'required' => true,
                    'label' => "J'ai créé les répertoires sur le serveur WAS"
                ))
                ->add('metiers','choice',array(
                    'choices'=> $metierForm,
                    'empty_value' => 'Choisissez une option',
                    'required' => true
                ))
                ->add('donnees','choice',array(
                    'choices' => $donneesForm,
                    'empty_value' => 'Choisissez une option',
                    'required' => true
                ))
                ->add('agences','choice',array(
                    'choices' => $agencesForm,
                    'required' => true,
                    'empty_value' => 'Choisissez une Agence'
                ))
                ->add('superieur','choice',array(
                    'required' => false,
                    'choices' => $usersForm,
                ))            ->add('abbr','text',array(
                        'required' => false,
                    )
                )
                ->add('lib','text',array(
                        'required' => false
                    )
                )
                ->add('profil','choice',array(
                    'required' => true,
                    'choices' => $profils,
                    'empty_value'=> false
                ));
        }

        //creation formulaire pour creation utilisateur
        if($login == '' && $login_modif == '')
        {
            $form = $form->add('servicess',new ServiceType(),array(
                'required' => false,
            ))->getForm();
        }
        else
        {//modification ou duplication
            if($login != '')
            {
                $userData = $manager->getUserData($login);
                //récupération des collatéraux de l'utilisateur et mise en forme pour affichage sur le formulaire
                if(array_key_exists('collateral',$userData)) {
                    if((string)$userData['service'] != 'AG')
                    {
                        if (!empty($userData['collateral'])) {
                            $newCol['user'] = $login;
                            $newCol['Date'] = '';
                            array_unshift($userData['collateral'], $newCol);
                        }
                    }
                }
                if(array_key_exists('collateralother',$userData))
                {
                    if((string)$userData['service'] != 'AG')
                    {
                        if(!empty(['collateralother'])) {
                            $newCol['user'] = $login;
                            $newCol['Date'] = '';
                            array_unshift($userData['collateralother'], $newCol);
                        }
                    }
                }


            }
            else
            {
                $userData = $manager->getUserData($login_modif);
                if(array_key_exists('code',$userData))
                {
                    $form                ->add('codeRes','text',array(
                        'required' => false,
                        'read_only' => true
                    ))  ;
                    $code = true;

                }
                else
                {
                    $form ->add('codeRes','text',array(
                        'required' => false,
                    ))  ;
                }
            }

            $service = $manager->getServicesWithAbbr($userData['service'],false)['MNURSELIB'];
            $sous_service = $manager->getServicesWithAbbr($userData['sous_service'],true)['MNURSSLIB'];

            $form = $form->add('servicess',new ServiceType(array('service'=>$service,'sous_service'=>$sous_service)),array(
                'required' => false,
            ))->getForm();

            //fin création form
            if($request->getMethod() != 'POST') {
                //remplissage des champs avec valeurs des données de l'utilisateur POST
                $form->get('file')->setData(array_keys($filesForm, $userData['file'])[0]);
                $form->get('metiers')->setData(array_keys($metierForm, $userData['metier'])[0]);
                $form->get('donnees')->setData(array_keys($donneesForm, $userData['donnees'])[0]);
                $form->get('menu')->setData(array_keys($menusForm, $userData['menu'])[0]);

                if (array_key_exists('superieur', $userData)) {
                    $form->get('superieur')->setData(array_keys($usersForm, $userData['superieur'])[0]);
                }

                if (array_key_exists('collateral', $userData)) {
                    $form->get('collateraux')->setData($userData['collateral']);
                }

                if (array_key_exists('collateralother', $userData)) {
                    $form->get('collaterauxother')->setData($userData['collateralother']);
                }

                $form->get('workflow')->setData($userData['workflow']);
                if (array_key_exists('correspondant', $userData)) {
                    $form->get('correspondant')->setData($userData['correspondant']);
                }
                $form->get('service')->setData($userData['service']);
                $form->get('agences')->setData($userData['agence']);
                $form->get('sservice')->setData($userData['sous_service']);

                if (array_key_exists('contentieux', $userData)) {
                    $form->get('contentieux')->setData($userData['contentieux']);
                }
                else {
                    $form->get('contentieux')->setData(2);
                }

                if (array_key_exists('dossier', $userData)) {
                    $form->get('dossier')->setData($userData['dossier']);
                }
                if (array_key_exists('cr', $userData)) {
                    if($userData['cr'] == 1)
                    {
                        $form->get('compteRendu')->setData(true);
                    }else
                    {
                        $form->get('compteRendu')->setData(false);
                    }
                }



                if ($login_modif != '') {

                    $form->get('name')->setData($userData['name']);
                    $form->get('firstName')->setData($userData['firstName']);

                    if (array_key_exists('code', $userData)) {
                        $button = false;
                        $form->get('codeRes')->setData($userData['code']);
                        $form->get('isCode')->setData(true);

                    }

                    if (array_key_exists('abbr', $userData)) {
                        $form->get('abbr')->setData($userData['abbr']);
                    }

                    if (array_key_exists('lib', $userData)) {
                        $form->get('lib')->setData($userData['lib']);
                    }
                    if (array_key_exists('profil', $userData)) {
                        $form->get('profil')->setData(array_search($userData['profil'],$profils));
                    }
                }
                elseif($login != '')
                {
                    if(array_key_exists('code', $userData))
                    {
                        $form->get('isCode')->setData(true);
                        $button = false;
                        if(!is_numeric($userData['code']))
                        {
                            $form->get('codeRes')->setData('XXX');

                        }
                        else
                        {
                            $form->get('codeRes')->setData($codeResp);
                        }
                    }
                }
            }
        }

        //traitement de l'envoi du formualire
        if($request->getMethod() == 'POST')
        {
            //données du formulaire
            $form->handleRequest($request);
            $data = $form->getData();

            //traitement pour creation ou duplication utilisateur
            if($login_modif == '') {
                if (!$form->isValid()) {
                    $error = "Formulaire non valide";
                    if($login = $session->get('login_dup'))
                    {
                        $session->clear();
                        return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                            'login' => $login,
                            'error' => $error
                        )));
                    }else
                    {
                        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                            'form' => $form->createView(),
                            'code' => $codeResp,
                            'data' => $data,
                            'login' => $login,
                            'modification' => $modification,
                            'coderes' => $code,
                            'loginmodif' => $login_modif,
                            'error'=> $error,
                            'button' => $button,
                            'rcci' => $rcci
                        ));
                    }
                } elseif($data['isCode'])
                {
                if(($data['collateraux'] != null || $data['collaterauxother'] != null) && !$data['codeRes']) {
                    //vérification de la cohérence des données du formulaire : code res et collatéraux
                    $error = "Vous ne pouvez ajouter de collatéral à l'utilisateur si celui ci ne possède pas de code responsable";
                    if($login = $session->get('login_dup'))
                    {
                        $session->clear();
                        return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                            'login' => $login,
                            'error' => $error
                        )));
                    }else
                    {
                        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                            'form' => $form->createView(),
                            'code' => $codeResp,
                            'error' => $error,
                            'data' => $data,
                            'login' => $login,
                            'modification' => $modification,
                            'coderes' => $code,
                            'loginmodif' => $login_modif,
                            'button' => $button,
                            'rcci' => $rcci

                        ));
                    }
                } elseif (is_numeric($data['codeRes']) && $data['codeRes'] < $codeResp) {
                    //vérification de la cohérence du code res donné
                    $error = "Code responsable déjà utilisé";
                    if($login = $session->get('login_dup'))
                    {
                        $session->clear();
                        return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                            'login' => $login,
                            'error' => $error
                        )));
                    }else
                    {
                        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                            'form' => $form->createView(),
                            'code' => $codeResp,
                            'error' => $error,
                            'data' => $data,
                            'login' => $login,
                            'modification' => $modification,
                            'coderes' => $code,
                            'loginmodif' => $login_modif,
                            'button' => $button,
                            'rcci' => $rcci

                        ));
                    }
                } elseif (  strlen($data['codeRes']) != 3) {
                    //contentieux sans code res impossible (schéma BDD SAB)
                    $error = "Code responsable invalide";
                    if($login = $session->get('login_dup'))
                    {
                        $session->clear();
                        return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                            'login' => $login,
                            'error' => $error
                        )));
                    }else
                    {
                        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                            'form' => $form->createView(),
                            'code' => $codeResp,
                            'error' => $error,
                            'data' => $data,
                            'login' => $login,
                            'modification' => $modification,
                            'coderes' => $code,
                            'loginmodif' => $login_modif,
                            'button' => $button,
                            'rcci' => $rcci

                        ));
                    }
                }elseif(!is_numeric($data['codeRes']))
                {
                    if ($manager->checkCodeRes($data['codeRes']))
                    {
                        $error = "Code responsable déjà utilisé";
                        if($login = $session->get('login_dup'))
                        {
                            $session->clear();
                            return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                                'login' => $login,
                                'error' => $error
                            )));
                        }else
                        {
                            return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                                'form' => $form->createView(),
                                'code' => $codeResp,
                                'error' => $error,
                                'data' => $data,
                                'login' => $login,
                                'modification' => $modification,
                                'coderes' => $code,
                                'loginmodif' => $login_modif,
                                'button' => $button,
                                'rcci' => $rcci

                            ));
                        }
                    }
                }elseif(is_numeric($data['codeRes']) && $data['codeRes'] - $codeResp >10)
                {
                    $error = "Code responsable trop grand";
                    if($login = $session->get('login_dup'))
                    {
                        $session->clear();
                        return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                            'login' => $login,
                            'error' => $error
                        )));
                    }else
                    {
                        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                            'form' => $form->createView(),
                            'code' => $codeResp,
                            'error' => $error,
                            'data' => $data,
                            'login' => $login,
                            'modification' => $modification,
                            'coderes' => $code,
                            'loginmodif' => $login_modif,
                            'button' => $button,
                            'rcci' => $rcci

                        ));
                    }
                }elseif($manager->checkAbbr($data['abbr']))
                {
                    $error = "Abrégé déja utilisé";
                    if($login = $session->get('login_dup'))
                    {
                        $session->clear();
                        return $this->redirect($this->get('router')->generate('back_office_habilitation_user_duplicate', array(
                            'login' => $login,
                            'error' => $error
                        )));
                    }
                    else
                    {

                        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
                            'form' => $form->createView(),
                            'code' => $codeResp,
                            'error' => $error,
                            'data' => $data,
                            'login' => $login,
                            'modification' => $modification,
                            'coderes' => $code,
                            'loginmodif' => $login_modif,
                            'button' => $button,
                            'rcci' => $rcci

                        ));
                    }
                }
            }
                $session->clear();
            }
            //traitement pour modification utilisateur
            if($login_modif != '')
            {
                //routine de modification des données utilisateur pour chaque champ du formulaire
                $userData = $manager->getUserData($login_modif);
                if($userData['service'] != $form->get('service')->getData())
                {
                    //modification service
                    $manager->updateService($form->get('service')->getData(),$login_modif);
                }
                if($userData['sous_service'] != $form->get('sservice')->getData())
                {
                    //modification Sous Service
                    $manager->updateSservice($form->get('sservice')->getData(),$login_modif);
                }
                if($userData['agence'] != $form->get('agences')->getData())
                {
                    //modification agence
                    $manager->updateAgence($form->get('agences')->getData(),$login_modif);
                }
                if(strcmp($userData['file'],$filesForm[$form->get('file')->getData()]) != 0)
                {
                    //modification file d'attente
                    $manager->updateFile($filesForm[$form->get('file')->getData()],$login_modif);
                }
                if($userData['metier'] != $metierForm[$form->get('metiers')->getData()])
                {
                    //modification habilitation métier
                    $manager->updateGroupe($metierForm[$form->get('metiers')->getData()],$login_modif,4);
                }
                if($userData['donnees'] != $donneesForm[$form->get('donnees')->getData()])
                {
                    //modification habilitation données
                    $manager->updateGroupe($donneesForm[$form->get('donnees')->getData()],$login_modif,3);
                }
                if($userData['menu'] != $menusForm[$form->get('menu')->getData()])
                {
                    //modification habilitation menu
                    $manager->updateGroupe($menusForm[$form->get('menu')->getData()],$login_modif,2);
                }
                foreach($form->get('correspondant')->getData() as $crd)
                {
                    if(!in_array($crd,$userData['correspondant']))
                    {
                        $manager->addCRD($login_modif,$crd,$data['codeRes']);
                    }
                }
                foreach($userData['correspondant'] as $crd)
                {
                    if(!in_array($crd,$form->get('correspondant')->getData()))
                    {
                        $manager->removeCRD($login_modif,$crd,$data['codeRes']);
                    }
                }
                //modification des données liées à la présence d'un code responsable (collatéraux, contentieux, supérieur, dossier)
                if($data['codeRes'] != null)
                {


                    if(($form->get('compteRendu')->getData() == true && $userData['cr'] != 1) || ($form->get('compteRendu')->getData() == false && $userData['cr'] == 1))
                    {
                        $manager->updateCR($metierForm[$form->get('metiers')->getData()],$form->get('compteRendu')->getData(),$data['codeRes']);
                    }

                    if($userData['contentieux'] != $form->get('contentieux')->getData())
                    {
                        //modification contentieux
                        $manager->updateContentieux($login_modif,$form->get('contentieux')->getData(),$data['codeRes']);
                    }
                    if($userData['dossier'] != $form->get('dossier')->getData())
                    {
                        //modification gestion des dossiers clients
                        $manager->updateDossier($form->get('dossier')->getData(),$data['codeRes']);
                    }
                    if($userData['superieur'] != $usersForm[$form->get('superieur')->getData()])
                    {
                        //modification supérieur
                        $manager->updateSuperieur($data['codeRes'], $usersForm[$form->get('superieur')->getData()]);
                    }
                    if($userData['abbr'] != $form->get('abbr')->getData() && !$manager->checkAbbr($form->get('abbr')->getData()))
                    {
                        //modification supérieur
                        $manager->updateAbrege($data['codeRes'], $form->get('abbr')->getData());
                    }
                    if($userData['lib'] != $form->get('lib')->getData())
                    {
                        //modification supérieur
                        $manager->updateLibelle($data['codeRes'], $form->get('lib')->getData());
                    }

                    if($userData['profil'] != $profils[$form->get('profil')->getData()])
                    {
                        //modification supérieur
                        $manager->updateProfil($data['codeRes'], $profils[$form->get('profil')->getData()]);
                    }
                        //agorithme de gestion de la modification des collatéraux
                        $there = false;
                        //pour chaque collatéral avant modification
                        foreach($userData['collateral'] as $oldCol)
                        {
                            //pour chaque collatéral après modification
                            foreach($form->get('collateraux')->getData() as $newCol)
                            {
                                //recherche des occurences des collatéraux dans les deux tableaux
                                if(strcmp(rtrim($oldCol['user']),$newCol['user']) == 0)
                                {
                                    $there = true;
                                }

                            }
                            if(!$there)
                            {
                                //si le collatéral n'a pas été trouvé dans les 2 tableaux c'est que celui ci a été supprimé de la liste
                                //méthode de suppression des collatéraux d'un utilisateur
                                $manager->removeCollateral($login_modif,$oldCol['user']);
                            }
                            else
                            {
                                $there = false;
                            }
                        }
                        $there = false;
                        //pour chaque collatéral après modification
                        foreach($form->get('collateraux')->getData() as $newCol)
                        {
                            //pour chaque collatéral avant modification
                            foreach($userData['collateral'] as $oldCol){
                                //recherche des occurences des collatéraux dans les deux tableaux
                                if(strcmp(rtrim($oldCol['user']),$newCol['user']) == 0)
                                {
                                    $there = true;
                                }

                            }
                            if($there)
                            {
                                $there = false;
                            }else
                            {
                                //si le collatéral n'a pas été trouvé dans les 2 tableaux c'est que celui ci a été ajouté a la liste
                                //méthode d'ajout de collatéral
                                $manager->addCollateral($login_modif,$newCol['user'],$newCol['Date']);
                            }
                        }
                        //idem pour les utilisateurs ayant pour collatéral l'utilisateur modifié
                        $there = false;
                        foreach($userData['collateralother'] as $oldCol)
                        {
                            foreach($form->get('collaterauxother')->getData() as $newCol)
                            {
                                if(strcmp(rtrim($oldCol['user']),$newCol['user']) == 0)
                                {
                                    $there = true;
                                }

                            }
                            if(!$there)
                            {
                                $manager->removeCollateral($oldCol['user'],$login_modif);
                            }
                            else
                            {
                                $there = false;
                            }
                        }
                        $there = false;
                        foreach($form->get('collaterauxother')->getData() as $newCol)
                        {
                            foreach($userData['collateralother'] as $oldCol){
                                if(strcmp(rtrim($oldCol['user']),$newCol['user']) == 0)
                                {
                                    $there = true;
                                }

                            }
                            if($there)
                            {
                                $there = false;
                            }else
                            {
                                $manager->addCollateral($newCol['user'],$login_modif,$newCol['Date']);
                            }
                        }
                }
                    //idem pour la liste des workflows
                    $there = false;
                    foreach($userData['workflow'] as $oldWork)
                    {
                        foreach($form->get('workflow')->getData() as $newWork)
                        {
                            if(strcmp($oldWork,$newWork) == 0)
                            {
                                $there = true;
                            }

                        }
                        if(!$there)
                        {
                            $manager->removeWorkflow($login_modif,$oldWork);
                        }
                        else
                        {
                            $there = false;
                        }
                    }
                    $there = false;
                    foreach($form->get('workflow')->getData() as $newWork)
                    {
                        foreach($userData['workflow'] as $oldWork){
                            if(strcmp($oldWork,$newWork) == 0)
                            {
                                $there = true;
                            }

                        }
                        if($there)
                        {
                            $there = false;
                        }else
                        {
                            $manager->addWorkflow($login_modif,$newWork);
                        }
                    }
                return $this->redirect($this->generateUrl('back_office_habilitation',array('info'=>'Utilisateur modifié')));
            }

            //mise en forme des données pour insertion BDD SAB
            $data['menu'] = $menusForm[$data['menu']];
            $data['donnees'] = $donneesForm[$data['donnees']];
            $data['metiers'] = $metierForm[$data['metiers']];
            $data['file'] = $filesForm[$data['file']];
            $data['profil'] = $profils[$data['profil']];
            if($data['superieur']) {
                $data['superieur'] = $usersForm[$data['superieur']];
            }
            //méthode de création utilisateur
            $manager->createUser($data);
            //retour page acceuil
            return $this->redirect($this->generateUrl('back_office_habilitation',array('info'=> $login)));
        }
        //rendu formulaire
        return $this->render('BackOfficeHabilitationBundle:UserCreation:create.html.twig', array(
            'form' => $form->createView(),
            'code'=>$codeResp,
            'login' => $login,
            'modification' => $modification,
            'coderes' => $code,
            'loginmodif' => $login_modif,
            'button' => $button,
            'rcci' => $rcci,
            'error' => $error
        ));
    }


    //réinitialisation mot de passe utilisateur
    public function userReinitiateAction($login)
    {
        $manager = $this->get('backoffice.userManager');
        //méthode de réinitialisation du mot de passe de l'utilisateur
        $manager->reinitiatePassword($login);
        return $this->redirect($this->generateUrl('back_office_habilitation'));
    }

    public function servicesAction()
    {
        $request = $this->get('request');
        if($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $id = '';
            $id = $request->get('id');

            if ($id != '') {

                $manager = $this->get('backoffice.userManager');
                $services = $manager->getServices($id);

                $response = new Response();

                $data = json_encode($services); // formater le résultat de la requête en json

                $response->headers->set('Content-Type', 'application/json');
                $response->setContent($data);

                return $response;
            }

        } else {

            return new Response('raté"');
        }

    }

    public function sousServicesAction()
    {
        $request = $this->get('request');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $id_agence = $request->get('id_agence');
            $id_service = $request->get('id_service');


            $manager = $this->get('backoffice.userManager');
            $services = $manager->getSousServices($id_agence, $id_service);

            $response = new Response();

            $data = json_encode($services); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }

    }

    public function usersAjaxAction()
    {
        $request = $this->get('request');
        $name = $request->get('name');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $manager = $this->get('backoffice.userManager');
            $users = $manager->getUserAjax($name);

            $response = new Response();

            $data = json_encode($users); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
    }

    public function userNameAjaxAction()
    {
        $request = $this->get('request');
        $login = $request->get('login');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $manager = $this->get('backoffice.userManager');
            $users = $manager->getUserNameAjax($login);

            $response = new Response();

            $data = json_encode($users); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
    }

    public function superieurAction()
    {
        $request = $this->get('request');
        $manager = $this->get('backoffice.userManager');
        $users = $manager->getAllUsersRespName();
        $usersForm = array();
        foreach($users as $user)
        {
            array_push($usersForm,$user['BAS006011']);
        }
        $form = $this->createFormBuilder()
            ->add('oldSup','choice',array(
                'choices' => $usersForm,
                'empty_value' => "",
                'required' => false

        ))
            ->add('newSup','choice',array(
                'choices' => $usersForm,
                'empty_value' => "",
                'required' => true
            ))->add('sub', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true
            ))->getForm();

        if($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);
            $data = $form->getData();
            if($form->isValid())
            {
                if($data['oldSup'] != null){
                 $data['oldSup'] = $usersForm[$data['oldSup']];
                }
                $data['newSup'] = $usersForm[$data['newSup']];
                $manager->changeSuperieur($data);
                return $this->redirect($this->generateUrl('back_office_habilitation',array('info' =>"Supérieur changé")));
            }
        }

        return $this->render('BackOfficeHabilitationBundle:Superieur:superieur.html.twig', array(
            'form' => $form->createView()
        ));

    }

    public function getSubAction()
    {
        $request = $this->get('request');
        $login = $request->get('name');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $manager = $this->get('backoffice.userManager');
            $users = $manager->getSub($login);

            $response = new Response();

            $data = json_encode($users); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
    }

    public function checkLoginAction()
    {
        $request = $this->get('request');
        $login = $request->get('name');
        if ($request->isXmlHttpRequest()) { // pour vérifier la présence d'une requete Ajax

            $manager = $this->get('backoffice.userManager');
            $users = $manager->checkLoginResp($login);

            $response = new Response();

            $data = json_encode($users); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);

            return $response;
        }
    }

}
