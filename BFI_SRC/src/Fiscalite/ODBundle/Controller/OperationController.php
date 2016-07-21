<?php

namespace Fiscalite\ODBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fiscalite\ODBundle\Form\OperationType;
use Fiscalite\ODBundle\Entity\Operation;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Operation controller.
 *
 */
class OperationController extends Controller
{
    /**
     * Lists all Operations.
     *
     * @Template()
     */
    public function listingAction(Request $request)
    {
        $odm = $this->get('fiscalite.ODManager');
        $em      = $this->getDoctrine()->getManager();
        $session = new Session();

        if ($datas = $session->get('datas/OD')) {
            $entities = $em->getRepository('FiscaliteODBundle:Operation')->search($datas);
        } else {
            $entities = $em->getRepository('FiscaliteODBundle:Operation')->findByIsDeleted("0");
        }

        // Tri à la mano du tableau d'objets car le numPiece est un "string"
        // et le schema update refuse ce changement de maniere simple
        usort($entities, function ($a, $b) {
            return (int)$a->getNumPiece() < (int)$b->getNumPiece();
        });

        $adapter    = new ArrayAdapter($entities);
        $pagerfanta = new Pagerfanta($adapter);
        if ($number = $request->request->get('number')) {
            $session->set('datas/numberPageOD', $number);
            $pagerfanta->setMaxPerPage($number);
        } elseif ($number = $session->get('datas/numberPageOD')) {
            $pagerfanta->setMaxPerPage($number);
        } else {
            $pagerfanta->setMaxPerPage(20);
        }
        $page = $request->query->get('page') ? $request->query->get('page') : 1;
        $pagerfanta->setCurrentPage($page);

        return array(
            'entities' => $pagerfanta,
            'datas'    => $datas,
            'number'   => $number,
            'listCodeOpe' => $odm->getCodesOpe()
        );
    }

    /**
     * Lists searched Operations.
     *
     * @Template("FiscaliteODBundle:Operation:listing.html.twig")
     */
    public function searchAction(Request $request)
    {
        $odm = $this->get('fiscalite.ODManager');
        $em      = $this->getDoctrine()->getManager();
        $datas   = $request->request->get('search');
        $session = new Session();

        $session->set('datas/OD', $datas);
        $entities = $em->getRepository('FiscaliteODBundle:Operation')->search($datas);

        // Tri à la mano du tableau d'objets car le numPiece est un "string"
        // et le schema update refuse ce changement de maniere simple
        usort($entities, function ($a, $b) {
            return (int)$a->getNumPiece() < (int)$b->getNumPiece();
        });

        $adapter    = new ArrayAdapter($entities);
        $pagerfanta = new Pagerfanta($adapter);
        if ($number = $request->request->get('number')) {
            $session->set('datas/numberPageOD', $number);
            $pagerfanta->setMaxPerPage($number);
        } elseif ($number = $session->get('datas/numberPageOD')) {
            $pagerfanta->setMaxPerPage($number);
        } else {
            $pagerfanta->setMaxPerPage(20);
        }
        $page = $request->query->get('page') ? $request->query->get('page') : 1;
        $pagerfanta->setCurrentPage($page);

        return array(
            'entities' => $pagerfanta,
            'datas'    => $datas,
            'number'   => $number,
            'listCodeOpe' => $odm->getCodesOpe()
        );
    }

    /*
     * Supprime les filtres de recherche
     */
    public function deleteFiltersAction()
    {
        $session = new Session();

        $session->remove('datas/OD');
        $session->remove('datas/numberPageOD');

        return $this->redirect($this->generateUrl('od_listing'));
    }

    /**
     * Creates a new Operation.
     *
     * @Template("FiscaliteODBundle:Operation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        // Initialisation des managers
        $om = $this->container->get('fiscalite.ODManager');

        // Initialisation des variables
        $errors = array();

        // Récupération des données générales
        $user    = $this->get('security.context')->getToken()->getUser();
        $comptes = $this->getDoctrine()->getManager('bfi2')->getRepository('FiscaliteODBundle:Compte')->findAll();
        list($dataOperation, $mvmtsData) = $om->getData($request);

        // Création Formulaire
        $form = $this->createCreateForm(new Operation());
        $form->handleRequest($request);

        // Vérification de l'objet formulaire
        if (!$form->isValid()) {
            $errors[] = 'Le formulaire comporte des erreurs.';
        }

        // Création des objets Operation (1) et Mouvement (x)
        $operation = $om->addOperation($dataOperation, $user);
        $arrMvmts = $om->getMvmtCollection($mvmtsData, $operation);

        // Vérification des numéros de compte / tiers / équilibre / Autorisation JC
        if (!$om->checkAccountNumbers($arrMvmts)) {
            $errors[] = 'Au moins un numéro de compte est incorrect.';
        }
        if (!$om->checkBalanceOperation($arrMvmts)) {
            $errors[] = 'L\'opération n\'est pas équilibrée.';
        }
        if (!$om->checkTiers($operation)) {
            $errors[] = 'Le tiers saisi n\'existe pas.';
        }
        if (!$om->checkAuthorisedJC($operation)) {
            $errors[] = 'La saisie d\'une OD en JC n\'est plus possible aujourd\'hui.';
        }

        if ($errors) {
            $alert = "Echec lors de la saisie de l'Opération Diverse";
            foreach ($errors as $error) {
                $alert .= ' - '.$error;
            }
            $this->generateFlashBag($alert);

            return array(
                'entity'      => new Operation(),
                'user'        => $user,
                'form'        => $form->createView(),
                'comptes'     => $comptes,
                'mouvements'  => $arrMvmts,
                'jcAutorisee' =>
                    $this->getDoctrine()->getManager()->getRepository('FiscaliteODBundle:Operation')->jcAutorise()
            );
        } else {
            $om->persistAndFlushFullOperation($operation, $arrMvmts);

            // Ajout d'une action
            $om->addAction("Saisie", $operation, $user);

            // on redirige
            $this->generateFlashBag('Saisie de l\'Opération Diverse effectuée avec succès.', 'success');
            return $this->redirect($this->generateUrl('od_show', array('id' => $operation->getNumPiece())));
        }
    }

    private function createCreateForm($entity)
    {
        $odManager = $this->container->get('fiscalite.ODManager');

        return $this->createForm(new OperationType($odManager), $entity, array(
            'action' => $this->generateUrl('od_create'),
            'method' => 'POST',
        ));
    }

    /**
     * Displays a form to create a new Operation.
     *
     * @Template()
     */
    public function newAction()
    {
        $odManager = $this->container->get('fiscalite.ODManager');
        $user   = $this->get('security.context')->getToken()->getUser();
        $entity = new Operation();

        $jcAutorisee = $this->getDoctrine()->getManager()->getRepository('FiscaliteODBundle:Operation')->jcAutorise();
        $comptes = $this->getDoctrine()->getManager('bfi2')->getRepository('FiscaliteODBundle:Compte')->findAll();

        $form = $this->createForm(new OperationType($odManager), $entity);
        $form->get('profil')->setData($user);

        return array(
            'entity'      => $entity,
            'user'        => $user,
            'comptes'     => $comptes,
            'form'        => $form->createView(),
            'jcAutorisee' => $jcAutorisee
        );
    }

    /**
     * Finds and displays an Operation.
     *
     * @Template()
     */
    public function showAction($id)
    {
        $em         = $this->getDoctrine()->getManager();
        $entity     = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);
        $mouvements = $entity->getMouvements();

        if ($redirect = $this->checkEntity($entity)) {
            return $redirect;
        }

        return array(
            'operation'  => $entity,
            'mouvements' => $mouvements
        );
    }

    /**
     * Displays a form to edit an existing Operation.
     *
     * @Template()
     */
    public function editAction($id)
    {
        $odManager   = $this->container->get('fiscalite.ODManager');
        $em          = $this->getDoctrine()->getManager();
        $user        = $this->get('security.context')->getToken()->getUser();
        $entity      = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);
        $comptes     = $this->getDoctrine()->getManager('bfi2')->getRepository('FiscaliteODBundle:Compte')->findAll();
        $jcAutorisee = $this->getDoctrine()->getManager()->getRepository('FiscaliteODBundle:Operation')->jcAutorise();

        if ($redirect = $this->checkEntity($entity, true)) {
            return $redirect;
        }

        $editForm = $this->createForm(new OperationType($odManager), $entity);

        return array(
            'entity'      => $entity,
            'user'        => $user,
            'comptes'     => $comptes,
            'form'        => $editForm->createView(),
            'jcAutorisee' => $jcAutorisee
        );
    }

    /**
     * Edits an existing Operation.
     *
     * @Template("FiscaliteODBundle:Operation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        // Initialisation des managers
        $om = $this->container->get('fiscalite.ODManager');
        $em = $this->getDoctrine()->getManager();

        // Initialisation des variables
        $errors = array();

        // Récupération des données générales
        $user      = $this->get('security.context')->getToken()->getUser();
        $operation = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);
        $comptes   = $this->getDoctrine()->getManager('bfi2')->getRepository('FiscaliteODBundle:Compte')->findAll();
        list($dataOperation, $mvmtsData) = $om->getData($request);

        // Redirection si non habitilité
        if ($redirect = $this->checkEntity($operation, true)) {
            return $redirect;
        }

        // Création Formulaire
        $editForm   = $this->createForm(new OperationType($om), $operation);
        $editForm->handleRequest($request);

        // Vérification de l'objet formulaire
        if (!$editForm->isValid()) {
            $errors[] = 'Le formulaire comporte des erreurs.';
        }

        // Création des objets Mouvement (x)
        $arrMvmts = $om->getMvmtCollection($mvmtsData, $operation);

        // Vérification des numéros de compte / équilibre / Autorisation JC
        if (!$om->checkAccountNumbers($arrMvmts)) {
            $errors[] = 'Au moins un numéro de compte est incorrect.';
        }
        if (!$om->checkBalanceOperation($arrMvmts)) {
            $errors[] = 'L\'opération n\'est pas équilibrée.';
        }
        if (!$om->checkTiers($operation)) {
            $errors[] = 'Le tiers saisi n\'existe pas.';
        }

        if ($errors) {
            $alert = "Echec lors de la saisie de l'Opération Diverse";
            foreach ($errors as $error) {
                $alert .= ' - '.$error;
            }
            $this->generateFlashBag($alert);

            return array(
                'entity'      => $operation,
                'user'        => $user,
                'form'        => $editForm->createView(),
                'comptes'     => $comptes,
                'mouvements'  => $arrMvmts,
                'jcAutorisee' =>
                    $this->getDoctrine()->getManager()->getRepository('FiscaliteODBundle:Operation')->jcAutorise()
            );
        } else {
            if ($operation->getStatut()->getIdStatut() == 'SAI') {
                $om->majStatut($operation, $user);
            } else {
                $om->majStatut($operation, $operation->getProfil());
            }
            $om->persistAndFlushFullOperation($operation, $arrMvmts);

            // Ajout d'une action
            $om->addAction("Modification", $operation, $user);

            // on redirige
            $this->generateFlashBag('Modification de l\'Opération Diverse effectuée avec succès.', 'success');
            return $this->redirect($this->generateUrl('od_show', array('id' => $operation->getNumPiece())));
        }
    }
    
    /**
     *
     * @Template()
     */
    public function validAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $om     = $this->container->get('fiscalite.ODManager');
        $entity = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);
        
        if ($redirect = $this->checkEntity($entity, true)) {
            return $redirect;
        }
        
        if ($entity->getProfil()->getId() == $this->getUser()->getId()
            || !$this->getUser()->hasRole("ROLE_SUPER_COMPTABLE")) {
            $this->generateFlashBag('Vous ne pouvez pas valider cette opération', 'error');
            return $this->redirect($this->generateUrl('od_show', array('id' => $entity->getNumPiece())));
        }
        
        $om->majStatut($entity, $entity->getProfil(), 'ENR');
        $om->majValideur($entity, $this->getUser());
        
        // Ajout d'une action
        $om->addAction("Validation", $entity, $this->getUser());
        
        // on redirige
        $this->generateFlashBag('Validation de l\'Opération Diverse effectuée avec succès.', 'success');
        return $this->redirect($this->generateUrl('od_show', array('id' => $entity->getNumPiece())));
    }

    /**
     *
     * @Template()
     */
    public function deleteLinkAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);

        if ($redirect = $this->checkEntity($entity, true)) {
            return $redirect;
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'delete_form' => $deleteForm->createView(),
            'entity'      => $entity
        );
    }

    /**
     * Delete an Operation.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $odManager = $this->container->get('fiscalite.ODManager');
        $form      = $this->createDeleteForm($id);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FiscaliteODBundle:Operation')->findOneByNumPiece($id);

            if ($redirect = $this->checkEntity($entity, true)) {
                return $redirect;
            }

            // On passe l'état is-deleted à true
            $entity->setIsDeleted(true);
            $em->persist($entity);
            $em->flush();

            // On créer une nouvelle action en base
            $odManager->addAction("Suppression", $entity, $this->get('security.context')->getToken()->getUser());

            $this->generateFlashBag('Opération Diverse supprimée avec succès.', 'success');
        }

        return $this->redirect($this->generateUrl('od_listing'));
    }

    /**
     * Creates a form to delete an Operation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('od_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * Demande la génération du CRE Journée Complémentaire
     *
     * @Template()
     */
    public function requestCreJcAction()
    {
        return array();
    }

    public function generateCreJcAction()
    {
        $odManager = $this->container->get('fiscalite.ODManager');
        $cm        = $this->container->get('fiscalite.CREManager');

        if ($cm->generate(true)) {
            $odManager->addActionTrigger();

            $this->generateFlashBag(
                'Fichier CRE généré avec succès. Il sera transféré dans quelques secondes sur SAB.',
                'success'
            );
        } else {
            $this->generateFlashBag('Fichier CRE non généré. Une erreur est survenue.');
        }

        return $this->redirect($this->generateUrl('od_listing'));
    }

    private function checkEntity($entity, $complete = false)
    {
        if (!$entity) {
            $this->generateFlashBag('Cette opération n\'existe pas.');
            return $this->redirect($this->generateUrl('od_listing'));
        } elseif ($entity->getIsDeleted()) {
            if ($complete) {
                $this->generateFlashBag('Cette opération n\'existe plus.');
                return $this->redirect($this->generateUrl('od_listing'));
            } elseif (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $this->generateFlashBag('Cette opération n\'existe plus.');
                return $this->redirect($this->generateUrl('od_listing'));
            }
        } elseif ($complete && $entity->getStatut()->getIdStatut() == "ENV") {
            $this->generateFlashBag('Cette opération ne peut plus être modifiée.');
            return $this->redirect($this->generateUrl('od_show', array('id' => $entity->getNumPiece())));
        }
    }

    private function generateFlashBag($message, $type = 'error')
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }
    
    /**
     *
     * @Template()
     */
    public function majAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $statut = $em->getRepository('FiscaliteODBundle:Statut')->findByIdStatut('ENV');
        $entities = $em->getRepository('FiscaliteODBundle:Operation')->findByStatut($statut);
        
        foreach ($entities as $entity) {
            $entity->setNumPieceTech($entity->getNumPiece());
            $em->persist($entity);
            $em->flush();
        }
        
        // on redirige
        $this->generateFlashBag('Opération effectuée avec succès.', 'success');
        return $this->redirect($this->generateUrl('front_office_main_homepage'));
    }
    
    /**
     * Lists all Operations.
     *
     * @Template()
     */
    public function exportAction()
    {
        return array();
    }
    
    public function exportingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if ($datas = $request->request->get('search')) {
            $entities = $em->getRepository('FiscaliteODBundle:Operation')->search($datas);
            return $this->getCSV($entities);
        }
    }
    
    private function getCSV($e)
    {
        $response = $this->render('FiscaliteODBundle:Export:export.csv.twig', array('entities' => $e));
        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.date('Ymd_his_').'EXPORT_OD.csv"');
        return $response;
    }
}
