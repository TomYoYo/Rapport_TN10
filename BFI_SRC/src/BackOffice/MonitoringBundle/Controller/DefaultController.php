<?php

namespace BackOffice\MonitoringBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use BackOffice\UserBundle\Entity\Profil;
use BackOffice\MonitoringBundle\Entity\CodeOperation;
use Fiscalite\ODBundle\Entity\CorrespondanceComptes;
use Editique\MasterBundle\Entity\CorrespondanceReleve;
use Fiscalite\ODBundle\Entity\Action;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /*
     * Fonction qui liste les entitées demandées
     */
    private function listEntities($request, $parameters, $paramTpl = array(), $isAdapter = false)
    {
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $datas = $request->request->get('search');
        $sort = $request->query->get('sort');

        if ($sort) {
            $session->set('sort/' . $parameters['session'], $sort);
        } elseif ($session->get('sort/' . $parameters['session'])) {
            $sort = $session->get('sort/' . $parameters['session']);
        }

        if ($datas) {
            $session->set('datas/' . $parameters['session'], $datas);
            $entities = $em->getRepository($parameters['repository'])->search($datas, $sort, $isAdapter);
        } else {
            if ($datas = $session->get('datas/' . $parameters['session'])) {
                $entities = $em->getRepository($parameters['repository'])->search($datas, $sort, $isAdapter);
            } else {
                $entities = $em->getRepository($parameters['repository'])->search(null, $sort, $isAdapter);
            }
        }

        if (!$isAdapter) {
            $adapter = new ArrayAdapter($entities);
        } else {
            $adapter = $entities;
        }

        $pagerfanta = new Pagerfanta($adapter);

        if ($number = $request->request->get('number')) {
            $session->set('datas/numberPagination', $number);
            $pagerfanta->setMaxPerPage($number);
        } elseif ($number = $session->get('datas/numberPagination')) {
            $pagerfanta->setMaxPerPage($number);
        }

        $page = $request->query->get('page') ? $request->query->get('page') : 1;
        $pagerfanta->setCurrentPage($page);

        $paramTpl += array(
            'entities' => $pagerfanta,
            'datas'    => $datas,
            'sort'     => $sort,
            'number'   => $number
        );
        $content = $this->renderView(
            'BackOfficeMonitoringBundle:Default:' . $parameters['template'],
            $paramTpl
        );

        return $content;
    }

    /*
     * Liste les logs
     */
    public function logAction(Request $request)
    {
        $modules = $this->container->getParameter('modules');
        $em = $this->getDoctrine()->getManager();
        $usernames = array();
        $users = $em->getRepository('BackOfficeUserBundle:Profil')->findAll();
        foreach ($users as $user) {
            $usernames[$user->getId()] = $user->getUsername();
        }

        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'BackOfficeMonitoringBundle:Log',
                'session'    => 'logs',
                'template'   => 'log.html.twig'
            ),
            array(
                'modules' => $modules,
                'users'=>$usernames
            ),
            true
        );

        return new Response($render);
    }

    /*
     * Liste les actions
     */
    public function actionAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'BackOfficeActionBundle:Action',
                'session'    => 'actions',
                'template'   => 'action.html.twig'
            )
        );

        return new Response($render);
    }

    /**
     * Relance une action KO
     */
    public function retryActionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $action = $em->getRepository('BackOfficeActionBundle:Action')->find($id);

        $action->setEtat('attente');

        $em->persist($action);
        $em->flush();

        return $this->redirect($this->generateUrl('back_office_monitoring_action'));
    }

    /*
     * Liste les éditiques
     */
    public function editiqueAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'EditiqueMasterBundle:Editique',
                'session'    => 'editiques',
                'template'   => 'editique.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Liste les éditiques
     */
    public function tauxAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'EditiqueMasterBundle:TauxCredit',
                'session'    => 'taux',
                'template'   => 'taux.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Liste les éditiques
     */
    public function p30SAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'MonetiqueCardBundle:P30S',
                'session'    => 'p30s',
                'template'   => 'p30s.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Liste les Opérations Diverses
     */
    public function odOperationsAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'FiscaliteODBundle:Operation',
                'session'    => 'operations',
                'template'   => 'od_operations.html.twig'
            )
        );

        return new Response($render);
    }

    public function odChangeStatusAction($id, $statusCode)
    {
        $em = $this->getDoctrine()->getManager();
        $operation = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);
        $status = $em->getRepository('FiscaliteODBundle:Statut')->findOneByIdStatut($statusCode);

        if (!$operation || !$status) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Une erreur est survenue pendant la mise à jour du statut. Modifcation non effectuée.'
            );

            return $this->redirect($this->generateUrl('back_office_monitoring_od_operations'));
        }

        $operation
            ->setStatutPrec($operation->getStatut())
            ->setDateStatutPrec($operation->getDateStatut())
            ->setStatut($status)
            ->setDateStatut(new \Datetime())
        ;

        $em->persist($operation);
        $em->flush();

        // Ajout d'une action dans le fichier d'audit
        $action = new Action();
        $action
            ->setProfil($this->getUser())
            ->setOperation($operation)
            ->setDateAction(new \Datetime())
            ->setLibelleAction("Admin: modif. statut")
        ;

        $em->persist($action);
        $em->flush();

        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Modification du statut de l\'Opération numéro '.$id.' réussie.'
        );

        return $this->redirect($this->generateUrl('back_office_monitoring_od_operations'));
    }

    /*
     * Liste les Actions sur OD
     */
    public function odActionsAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'FiscaliteODBundle:Action',
                'session'    => 'opeActions',
                'template'   => 'od_actions.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Liste les correspondances de comptes
     */
    public function corresComptesAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'FiscaliteODBundle:CorrespondanceComptes',
                'session'    => 'comptes',
                'template'   => 'od_corres_comptes.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Nouvelle correspondance de comptes
     */
    public function corresComptesNewAction()
    {
        return $this->render('BackOfficeMonitoringBundle:Default:od_corres_comptes_new.html.twig');
    }

    /*
     * Création d'une correspondance de comptes
     */
    public function corresComptesCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $count = 0;

        $internes = $request->request->get('interne');
        $externes = $request->request->get('externe');

        foreach ($internes as $key => $interne) {
            if ($externes[$key]) {
                $count++;
                $compte = new CorrespondanceComptes();
                $compte
                    ->setNumCompteInterne($interne)
                    ->setNumCompteExterne($externes[$key]);

                $em->persist($compte);
            }
        }

        $em->flush();
        $lm->addInfo(
            $count .
            ' correspondances de compte ont été ajoutées par ' .
            $this->get('security.context')->getToken()->getUser()->getUsername() .
            '.'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Création de '.$count.' nouvelle(s) correspondance(s) effectuée avec succès.'
        );

        return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
    }

    /*
     * Edition du correspondance de comptes
     */
    public function corresComptesEditAction($numCompteInterne)
    {
        $em = $this->getDoctrine()->getManager();
        $mapping =
            $em->getRepository('FiscaliteODBundle:CorrespondanceComptes')->findOneByNumCompteInterne($numCompteInterne);

        if (!$mapping) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier cette correspondance. Celle-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
        }

        return $this->render(
            'BackOfficeMonitoringBundle:Default:od_corres_comptes_edit.html.twig',
            array(
                'mapping' => $mapping
            )
        );
    }

    /*
     * Mise à jour d'une correspondance de compes
     */
    public function corresComptesUpdateAction(Request $request, $numCompteInterne)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $mapping =
            $em->getRepository('FiscaliteODBundle:CorrespondanceComptes')->findOneByNumCompteInterne($numCompteInterne);

        if (!$mapping) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier cette correspondance. Celle-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
        }

        $datas = $request->request->get('mapping');
        if ($datas['interne'] && $datas['externe']) {
            $mapping
                ->setNumCompteInterne($datas['interne'])
                ->setNumCompteExterne($datas['externe']);

            $em->persist($mapping);
            $em->flush();

            $lm->addInfo(
                'Une correspondance de compte (nouveau numéro interne : ' . $datas['interne'] .
                ') a été modifiée par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
                'BackOffice > Monitoring',
                'Modification de données depuis le back-office'
            );
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Modification de la correspondance effectuée avec succès.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
        }

        return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
    }

    /*
     * Suppression d'une correspondance de comptes
     */
    public function corresComptesDeleteAction($numCompteInterne)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        $mapping =
            $em->getRepository('FiscaliteODBundle:CorrespondanceComptes')->findOneByNumCompteInterne($numCompteInterne);

        if (!$mapping) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de supprimer cette correspondance. Celle-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
        }

        $em->remove($mapping);
        $em->flush();

        $lm->addAlert(
            'Une correspondance de compte (numéro interne : ' . $datas['interne'] .
            ') a été supprimée par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
            'BackOffice > Monitoring',
            'Suppression de données depuis le back-office'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Suppression de la correspondance effectuée avec succès.'
        );
        return $this->redirect($this->generateUrl('back_office_monitoring_od_correspondance_comptes'));
    }

    /*
     * Liste les correspondances de comptes
     */
    public function corresReleveAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'EditiqueMasterBundle:CorrespondanceReleve',
                'session'    => 'typeReleve',
                'template'   => 'editique_corres_releve.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Nouvelle correspondance de comptes
     */
    public function corresReleveNewAction()
    {
        return $this->render('BackOfficeMonitoringBundle:Default:editique_corres_releve_new.html.twig');
    }

    /*
     * Création d'une correspondance de comptes
     */
    public function corresReleveCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $count = 0;

        $libelles = $request->request->get('libelle');
        $types = $request->request->get('type');

        foreach ($libelles as $key => $libelle) {
            if ($types[$key]) {
                $count++;
                $correspondance = new CorrespondanceReleve();
                $correspondance
                    ->setLibelle($libelle)
                    ->setType($types[$key]);

                $em->persist($correspondance);
            }
        }

        $em->flush();
        $lm->addInfo(
            $count .
            ' correspondances de relevé ont été ajoutées par ' .
            $this->get('security.context')->getToken()->getUser()->getUsername() .
            '.'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Création de '.$count.' nouvelle(s) correspondance(s) effectuée avec succès.'
        );

        return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
    }

    /*
     * Edition du correspondance de comptes
     */
    public function corresReleveEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $mapping = $em->getRepository('EditiqueMasterBundle:CorrespondanceReleve')->find($id);

        if (!$mapping) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier cette correspondance. Celle-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
        }

        return $this->render(
            'BackOfficeMonitoringBundle:Default:editique_corres_releve_edit.html.twig',
            array(
                'mapping' => $mapping
            )
        );
    }

    /*
     * Mise à jour d'une correspondance de compes
     */
    public function corresReleveUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $mapping = $em->getRepository('EditiqueMasterBundle:CorrespondanceReleve')->find($id);

        if (!$mapping) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier cette correspondance. Celle-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
        }

        $datas = $request->request->get('mapping');
        if ($datas['libelle'] && $datas['type']) {
            $mapping
                ->setLibelle($datas['libelle'])
                ->setType($datas['type']);

            $em->persist($mapping);
            $em->flush();

            $lm->addInfo(
                'Une correspondance de relevé (id : ' . $id .
                ') a été modifiée par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
                'BackOffice > Monitoring',
                'Modification de données depuis le back-office'
            );
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Modification de la correspondance effectuée avec succès.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
        }

        return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
    }

    /*
     * Suppression d'une correspondance de comptes
     */
    public function corresReleveDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        $mapping = $em->getRepository('EditiqueMasterBundle:CorrespondanceReleve')->find($id);

        if (!$mapping) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de supprimer cette correspondance. Celle-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
        }

        $em->remove($mapping);
        $em->flush();

        $lm->addAlert(
            'Une correspondance de relevé (id : ' . $id .
            ') a été supprimée par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
            'BackOffice > Monitoring',
            'Suppression de données depuis le back-office'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Suppression de la correspondance effectuée avec succès.'
        );
        return $this->redirect($this->generateUrl('back_office_monitoring_editique_correspondance_releve'));
    }

    /*
     * Liste les utilisateurs
     */
    public function usersAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'BackOfficeUserBundle:Profil',
                'session'    => 'users',
                'template'   => 'users.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Nouvel utilisateur
     */
    public function userNewAction()
    {
        return $this->render('BackOfficeMonitoringBundle:Default:user_new.html.twig');
    }

    /*
     * Création d'un utilisateur
     */
    public function userCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        $datas = $request->request->get('user');

        if ($datas['email'] && $datas['name'] && $datas['firstName']) {
            if ($this->userAlreadyExists($datas['email'])) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Un utilisateur avec la même adresse E-Mail existe déjà.'
                );
                return $this->redirect($this->generateUrl('back_office_monitoring_user_new'));
            }

            $username = $this->generateUsername($datas['firstName'], $datas['name']);
            $codeuser = $datas['codeUser'] ? $datas['codeUser'] : "9999";
            $password = $this->generatePassword();
            $user = new Profil();

            $user
                ->setLogin($username)
                ->setUsername($username)
                ->setNom($datas['name'])
                ->setPrenom($datas['firstName'])
                ->setCodeUser($codeuser)
                ->setEmail($datas['email'])
                ->setPlainPassword($password)
                ->setCodeEtabl("0001")
                ->setCodeAgence("0102")
                ->setCodeService("RC")
                ->setCodeSsService("RC")
                ->setEnabled(true);

            if (isset($datas['role']['roleSuperAdmin'])) {
                $user->addRole("ROLE_SUPER_ADMIN");
            }
            if (isset($datas['role']['roleAdmin'])) {
                $user->addRole("ROLE_ADMIN");
            }
            if (isset($datas['role']['roleSuperComptable'])) {
                $user->addRole("ROLE_SUPER_COMPTABLE");
            }
            if (isset($datas['role']['roleComptable'])) {
                $user->addRole("ROLE_COMPTABLE");
            }
            if (isset($datas['role']['roleAssistance'])) {
                $user->addRole("ROLE_ASSISTANCE");
            }
            if (isset($datas['role']['roleRCCI'])) {
                $user->addRole("ROLE_RCCI");
            }
            if (isset($datas['role']['roleControleur'])) {
                $user->addRole("ROLE_CONTROLEUR");
            }
            if (isset($datas['role']['roleAgence'])) {
                $user->addRole("ROLE_AGENCE");
            }
            if (isset($datas['role']['roleReglementaire'])) {
                $user->addRole("ROLE_REGLEMENTAIRE");
            }
            if (isset($datas['role']['roleCommercial'])) {
                $user->addRole("ROLE_COMMERCIAL");
            }
            if (isset($datas['role']['roleSAB'])) {
                $user->addRole("ROLE_SAB");
            }
            if (isset($datas['role']['roleFirme'])) {
                $user->addRole("ROLE_FIRME");
            }
            if (isset($datas['role']['roleBackoffice'])) {
                $user->addRole("ROLE_BACKOFFICE");
            }

            $em->persist($user);
            $em->flush();

            $lm->addInfo(
                'Un nouvel utilisateur (' . $username .
                ') a été créé, par ' . $this->get('security.context')->getToken()->getUser()->getUsername() .
                ', sans connaissance du mot de passe.',
                'BackOffice > Monitoring',
                'Ajout de données depuis le Back-Office'
            );

            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Création du nouvel utilisateur effectuée avec succès.'
            );

            // Envoi mail à l'utilisateur
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'From: BFI <noreply@banque-fiducial.fr>' . "\r\n";

            mail(
                $user->getEmail(),
                "Votre compte Fiducial Banque",
                $this->renderView('FrontOfficeMainBundle:Mail:mail.html.twig', array(
                    'parts' => array(
                        array(
                            'title' => 'Votre compte Fiducial Banque',
                            'content' => $this->renderView(
                                'BackOfficeMonitoringBundle:Mailing:mail_user_creation.html.twig',
                                array(
                                    'username' => $username,
                                    'password' => $password
                                )
                            )
                        )
                    )
                )),
                $headers
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Des erreurs de saisies ont été détectées. ' . 'Merci de re-saisir le formulaire.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_user_new'));
        }

        return $this->redirect($this->generateUrl('back_office_monitoring_users'));
    }

    /*
     * Edition d'un utilisateur
     */
    public function userEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('BackOfficeUserBundle:Profil')->find($id);

        if (!$user || $user->isLocked()) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Modification de l\'utilisateur impossible. Ce dernier n\'existe pas ou est désactivé.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_users'));
        }

        return $this->render(
            'BackOfficeMonitoringBundle:Default:user_edit.html.twig',
            array(
                'user' => $user
            )
        );
    }

    /*
     * Mise à jour d'un utilisateur
     */
    public function userUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $user = $em->getRepository('BackOfficeUserBundle:Profil')->find($id);

        if (!$user || $user->isLocked()) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Modification de l\'utilisateur impossible. Ce dernier n\'existe pas ou son compte est désactivé.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_users'));
        }

        $datas = $request->request->get('user');
        if ($datas['email']) {
            // Vérification du mail
            if ($this->userAlreadyExists($datas['email']) and $datas['email'] != $user->getEmail()) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Un utilisateur avec la même adresse E-Mail existe déjà.'
                );
                return $this->redirect(
                    $this->generateUrl('back_office_monitoring_user_edit', array('id' => $user->getId()))
                );
            }
            
            $user
                ->setEmail($datas['email'])
                ->setCodeUser($datas['codeUser']);

            if (isset($datas['role']['roleSuperAdmin'])) {
                if (!$user->hasRole("ROLE_SUPER_ADMIN")) {
                    $user->addRole("ROLE_SUPER_ADMIN");
                }
            } else {
                if ($user->hasRole("ROLE_SUPER_ADMIN")) {
                    $user->removeRole("ROLE_SUPER_ADMIN");
                }
            }
            if (isset($datas['role']['roleAdmin'])) {
                if (!$user->hasRole("ROLE_ADMIN")) {
                    $user->addRole("ROLE_ADMIN");
                }
            } else {
                if ($user->hasRole("ROLE_ADMIN")) {
                    $user->removeRole("ROLE_ADMIN");
                }
            }
            if (isset($datas['role']['roleSuperComptable'])) {
                if (!$user->hasRole("ROLE_SUPER_COMPTABLE")) {
                    $user->addRole("ROLE_SUPER_COMPTABLE");
                }
            } else {
                if ($user->hasRole("ROLE_SUPER_COMPTABLE")) {
                    $user->removeRole("ROLE_SUPER_COMPTABLE");
                }
            }
            if (isset($datas['role']['roleComptable'])) {
                if (!$user->hasRole("ROLE_COMPTABLE")) {
                    $user->addRole("ROLE_COMPTABLE");
                }
            } else {
                if ($user->hasRole("ROLE_COMPTABLE")) {
                    $user->removeRole("ROLE_COMPTABLE");
                }
            }
            if (isset($datas['role']['roleAssistance'])) {
                if (!$user->hasRole("ROLE_ASSISTANCE")) {
                    $user->addRole("ROLE_ASSISTANCE");
                }
            } else {
                if ($user->hasRole("ROLE_ASSISTANCE")) {
                    $user->removeRole("ROLE_ASSISTANCE");
                }
            }
            if (isset($datas['role']['roleRCCI'])) {
                if (!$user->hasRole("ROLE_RCCI")) {
                    $user->addRole("ROLE_RCCI");
                }
            } else {
                if ($user->hasRole("ROLE_RCCI")) {
                    $user->removeRole("ROLE_RCCI");
                }
            }
            if (isset($datas['role']['roleControleur'])) {
                if (!$user->hasRole("ROLE_CONTROLEUR")) {
                    $user->addRole("ROLE_CONTROLEUR");
                }
            } else {
                if ($user->hasRole("ROLE_CONTROLEUR")) {
                    $user->removeRole("ROLE_CONTROLEUR");
                }
            }
            if (isset($datas['role']['roleReglementaire'])) {
                if (!$user->hasRole("ROLE_REGLEMENTAIRE")) {
                    $user->addRole("ROLE_REGLEMENTAIRE");
                }
            } else {
                if ($user->hasRole("ROLE_REGLEMENTAIRE")) {
                    $user->removeRole("ROLE_REGLEMENTAIRE");
                }
            }
            if (isset($datas['role']['roleAgence'])) {
                if (!$user->hasRole("ROLE_AGENCE")) {
                    $user->addRole("ROLE_AGENCE");
                }
            } else {
                if ($user->hasRole("ROLE_AGENCE")) {
                    $user->removeRole("ROLE_AGENCE");
                }
            }
            if (isset($datas['role']['roleCommercial'])) {
                if (!$user->hasRole("ROLE_COMMERCIAL")) {
                    $user->addRole("ROLE_COMMERCIAL");
                }
            } else {
                if ($user->hasRole("ROLE_COMMERCIAL")) {
                    $user->removeRole("ROLE_COMMERCIAL");
                }
            }
            if (isset($datas['role']['roleSAB'])) {
                if (!$user->hasRole("ROLE_SAB")) {
                    $user->addRole("ROLE_SAB");
                }
            } else {
                if ($user->hasRole("ROLE_SAB")) {
                    $user->removeRole("ROLE_SAB");
                }
            }
            if (isset($datas['role']['roleFirme'])) {
                if (!$user->hasRole("ROLE_FIRME")) {
                    $user->addRole("ROLE_FIRME");
                }
            } else {
                if ($user->hasRole("ROLE_FIRME")) {
                    $user->removeRole("ROLE_FIRME");
                }
            }
            if (isset($datas['role']['roleBackoffice'])) {
                if (!$user->hasRole("ROLE_BACKOFFICE")) {
                    $user->addRole("ROLE_BACKOFFICE");
                }
            } else {
                if ($user->hasRole("ROLE_BACKOFFICE")) {
                    $user->removeRole("ROLE_BACKOFFICE");
                }
            }

            $em->persist($user);
            $em->flush();

            $lm->addInfo(
                'Le profil de ' . $user->getUsername() .
                ' a été modifiée par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
                'BackOffice > Monitoring',
                'Modification de données depuis le back-office'
            );
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Modification de l\'utilisateur effectuée avec succès.'
            );
        }

        return $this->redirect($this->generateUrl('back_office_monitoring_users'));
    }

    /*
     * Suppression d'un utilisateur
     */
    public function userDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        $user = $em->getRepository('BackOfficeUserBundle:Profil')->find($id);

        if (!$user || $user->isLocked()) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Désactivation du compte de l\'utilisateur impossible. Ce dernier n\'existe pas ou son' .
                ' compte est déjà désactivé.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_users'));
        }

        $user->setLocked(true);

        $em->persist($user);
        $em->flush();

        $lm->addAlert(
            'Le profil de ' . $user->getUsername() .
            ' a été désactivé par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
            'BackOffice > Monitoring',
            'Suppression de données depuis le back-office'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Désactivation de l\'utilisateur effectuée avec succès.'
        );

        return $this->redirect($this->generateUrl('back_office_monitoring_users'));
    }

    /*
     * Activation d'un utilisateur
     */
    public function userActivateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        $user = $em->getRepository('BackOfficeUserBundle:Profil')->find($id);

        if (!$user || !$user->isLocked()) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Activation du compte de l\'utilisateur impossible. Ce dernier n\'existe pas ou ' .
                'son compte est déjà activé.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_users'));
        }

        $user->setLocked(false);

        $em->persist($user);
        $em->flush();

        $lm->addInfo(
            'Le profil de ' . $user->getUsername() .
            ' a été réactivé par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
            'BackOffice > Monitoring',
            'Modification de données depuis le back-office'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Activation de l\'utilisateur effectuée avec succès.'
        );

        return $this->redirect($this->generateUrl('back_office_monitoring_users'));
    }

    /*
     * Génération d'un mot de passe aléatoire
     */
    public function generatePassword($length = 8)
    {
        $chars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    /*
     * Génération du nom d'utilisateur
     */
    public function generateUsername($firstName, $name)
    {
        $em = $this->getDoctrine()->getManager();
        $username = $this->supprSpecials(strtolower(substr($firstName, 0, 1) . "." . $name));
        $countSameUsername = $em->getRepository('BackOfficeUserBundle:Profil')->countSameUsername($username);

        if ($countSameUsername > 0) {
            $username = $username . ($countSameUsername + 1);
        }

        return $username;
    }

    private function supprSpecials($chaine)
    {
        $chaine = str_replace(array('à', 'á', 'â', 'ã', 'ä', 'å'), 'a', $chaine);
        $chaine = str_replace('ç', 'c', $chaine);
        $chaine = str_replace(array('é', 'è', 'ê', 'ë'), 'e', $chaine);
        $chaine = str_replace(array('ì', 'í', 'î', 'ï'), 'i', $chaine);
        $chaine = str_replace(array('ð', 'ò', 'ó', 'ô', 'õ', 'ö'), 'o', $chaine);
        $chaine = str_replace(array('ù', 'ú', 'û', 'ü'), 'u', $chaine);
        $chaine = str_replace(array('ý', 'ÿ'), 'y', $chaine);
        $chaine = str_replace(array('-', ' ', '@', '_', '&'), '', $chaine);

        return $chaine;
    }

    /*
     * Vérification de l'existence d'un mail
     */
    public function userAlreadyExists($email)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('BackOfficeUserBundle:Profil')->findOneByEmail($email);

        if ($user) {
            return true;
        }

        return false;
    }

    public function treeAction()
    {
        return $this->render('BackOfficeMonitoringBundle:Default:tree_exchange.html.twig');
    }

    /*
     * Supprime les filtres de recherche
     */
    public function deleteFiltersAction($page, Request $request)
    {
        $session = new Session();

        switch ($page) {
            case 'logs':
                $session->remove('datas/logs');
                $route = 'back_office_monitoring_log';
                break;
            case 'actions':
                $session->remove('datas/actions');
                $route = 'back_office_monitoring_action';
                break;
            case 'editiques':
                $session->remove('datas/editiques');
                $route = 'back_office_monitoring_editique';
                break;
            case 'taux':
                $session->remove('datas/taux');
                $route = 'back_office_monitoring_taux';
                break;
            case 'p30s':
                $session->remove('datas/p30s');
                $route = 'back_office_monitoring_p30s';
                break;
            case 'od_operations':
                $session->remove('datas/operations');
                $route = 'back_office_monitoring_od_operations';
                break;
            case 'od_actions':
                $session->remove('datas/opeActions');
                $route = 'back_office_monitoring_od_actions';
                break;
            case 'users':
                $session->remove('datas/users');
                $route = 'back_office_monitoring_users';
                break;
            case 'corres_comptes':
                $session->remove('datas/comptes');
                $route = 'back_office_monitoring_od_correspondance_comptes';
                break;
            case 'corres_releve':
                $session->remove('datas/typeReleve');
                $route = 'back_office_monitoring_editique_correspondance_releve';
                break;
            case 'code_operation':
                $session->remove('datas/codeOperation');
                $route = 'back_office_monitoring_casa_code_operation';
                break;
            case 'all':
                $session->remove('datas/logs');
                $session->remove('datas/actions');
                $session->remove('datas/editiques');
                $session->remove('datas/taux');
                $session->remove('datas/p30s');
                $session->remove('datas/operations');
                $session->remove('datas/opeActions');
                $session->remove('datas/users');
                $session->remove('datas/comptes');
                $session->remove('datas/typeReleve');
                $session->remove('datas/codeOperation');
                $session->remove('datas/numberPagination');
                $route = 'back_office_monitoring_index';
                break;
        }

        return $this->redirect($this->generateUrl($route));
    }
    
    public function notificationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $users = $em->getRepository('BackOfficeUserBundle:Profil')->findByLocked(false);
        
        return $this->render(
            'BackOfficeMonitoringBundle:Default:notifications.html.twig',
            array(
                'users' => $users
            )
        );
    }
    
    public function notificationsManageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $datas = $request->request->get('notification');
        $users = $em->getRepository('BackOfficeUserBundle:Profil')->findByLocked(false);
        
        foreach ($users as $user) {
            if (isset($datas[$user->getId()])) {
                $user->setNotifications(array_keys($datas[$user->getId()]));
            } else {
                $user->setNotifications(null);
            }

            $em->persist($user);
            $em->flush();
        }
        
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Mise à jour des abonnements réalisée avec succès.'
        );
        
        return $this->render(
            'BackOfficeMonitoringBundle:Default:notifications.html.twig',
            array(
                'users' => $users
            )
        );
    }
    
    /*
     * Liste les codes opérations surveillés
     */
    public function codeOperationAction(Request $request)
    {
        $render = $this->listEntities(
            $request,
            array(
                'repository' => 'BackOfficeMonitoringBundle:CodeOperation',
                'session'    => 'codeOperation',
                'template'   => 'casa_code_operation.html.twig'
            )
        );

        return new Response($render);
    }

    /*
     * Nouveau code opération surveillé
     */
    public function codeOperationNewAction()
    {
        return $this->render('BackOfficeMonitoringBundle:Default:casa_code_operation_new.html.twig');
    }

    /*
     * Création d'un code opération
     */
    public function codeOperationCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        
        if ($request->request->get('libelle')) {
            $codeOperation = new CodeOperation();
            $codeOperation
                ->setCode($request->request->get('code'))
                ->setLibelle($request->request->get('libelle'))
                ->setTypePresence($request->request->get('type'));

            $em->persist($codeOperation);
            $em->flush();
            
            $lm->addInfo(
                'Un code opérations a été ajouté à la surveillance par ' .
                $this->get('security.context')->getToken()->getUser()->getUsername() .
                '.'
            );
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Ajout du nouveau code opération à la surveillance effectuée avec succès.'
            );
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Ajout du nouveau code opération à la surveillance non effectuée.'
            );
        }

        return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
    }

    /*
     * Edition du code opération surveillé
     */
    public function codeOperationEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $codeOperation = $em->getRepository('BackOfficeMonitoringBundle:CodeOperation')->find($id);

        if (!$codeOperation) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier ce code. Celui-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
        }

        return $this->render(
            'BackOfficeMonitoringBundle:Default:casa_code_operation_edit.html.twig',
            array(
                'codeOperation' => $codeOperation
            )
        );
    }

    /*
     * Mise à jour d'un code opération surveillé
     */
    public function codeOperationUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');
        $codeOperation = $em->getRepository('BackOfficeMonitoringBundle:CodeOperation')->find($id);

        if (!$codeOperation) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de modifier ce code. Celui-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
        }

        $datas = $request->request->get('codeOperation');
        
        if ($datas['code'] && $datas['libelle'] && isset($datas['type'])) {
            $codeOperation
                ->setCode($datas['code'])
                ->setLibelle($datas['libelle'])
                ->setTypePresence($datas['type']);

            $em->persist($codeOperation);
            $em->flush();

            $lm->addInfo(
                'Un code opération surveillé (id : ' . $id .
                ') a été modifié par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
                'BackOffice > Monitoring',
                'Modification de données depuis le back-office'
            );
            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Modification du code opération surveillé effectuée avec succès.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
        }

        return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
    }

    /*
     * Suppression d'un code opération surveillé
     */
    public function codeOperationDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $lm = $this->container->get('backoffice_monitoring.logManager');

        $codeOperation = $em->getRepository('BackOfficeMonitoringBundle:CodeOperation')->find($id);

        if (!$codeOperation) {
            $this->container->get('session')->getFlashBag()->add(
                'error',
                'Impossible de supprimer ce code. Celui-ci n\'existe pas.'
            );
            return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
        }

        $em->remove($codeOperation);
        $em->flush();

        $lm->addAlert(
            'Un code opération surveillé (id : ' . $id .
            ') a été supprimé par ' . $this->get('security.context')->getToken()->getUser()->getUsername() . '.',
            'BackOffice > Monitoring',
            'Suppression de données depuis le back-office'
        );
        $this->container->get('session')->getFlashBag()->add(
            'success',
            'Suppression du code opération surveillé effectuée avec succès.'
        );
        return $this->redirect($this->generateUrl('back_office_monitoring_casa_code_operation'));
    }
    
    public function printTXTAction($directory)
    {
        $dirSortie = $this->container->getParameter('dirSortieDivers');

        if ($directory != '') {
            $headers = array(
                'Content-Type'        => 'text/plain',
                'Content-Disposition' => 'inline; filename="XCT6P30S0.dat"'
            );

            return new Response(file_get_contents($dirSortie . $directory . "/XCT6P30S0.dat"), 200, $headers);
        }
    }
    
    public function logsSymfonyAction(Request $request)
    {
        $dirname = __DIR__.'/../../../../app/logs/';
        
        // Téléchargement du fichier demandé
        if ($filename = $request->get('file')) {
            header('Content-Type: application/octet-stream');
            header('Content-Length: '. filesize($dirname.$filename));
            header('Content-disposition: attachment; filename='.$filename);
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            readfile($dirname.$filename);
        }
        
        // Lecture des fichiers présents
        $dir = opendir($dirname);
        
        while ($file = readdir($dir)) {
            if ($file != '.' && $file != '..' && !is_dir($dirname.$file)) {
                $files[] = $file;
            }
        }
        
        // Retour de réponse
        return $this->render(
            'BackOfficeMonitoringBundle:Default:logs_symfony.html.twig',
            array(
                'files' => $files
            )
        );
    }
    
    public function unversionedFilesAction(Request $request)
    {
        $dirname = '/app/bfi/';
        
        // Téléchargement du fichier demandé
        if ($filename = $request->get('file')) {
            header('Content-Type: application/octet-stream');
            header('Content-Length: '. filesize($dirname.$filename));
            header('Content-disposition: attachment; filename='.$filename);
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            readfile($dirname.$filename);
        }
        
        // Lecture des fichiers présents
        $dir = opendir($dirname);
        
        while ($file = readdir($dir)) {
            if ($file != '.' && $file != '..' && !is_dir($dirname.$file)) {
                $files[] = $file;
            }
        }
        
        // Retour de réponse
        return $this->render(
            'BackOfficeMonitoringBundle:Default:unversioned_files.html.twig',
            array(
                'files' => $files
            )
        );
    }
}
