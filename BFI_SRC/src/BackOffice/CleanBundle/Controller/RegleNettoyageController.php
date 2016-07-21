<?php

namespace BackOffice\CleanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use BackOffice\CleanBundle\Entity\RegleNettoyage;
use BackOffice\CleanBundle\Form\RegleNettoyageType;
use BackOffice\ActionBundle\Entity\Action;
use BackOffice\MonitoringBundle\Entity\Log;

/**
 * RegleNettoyage controller.
 *
 */
class RegleNettoyageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // formaulaire
        $entity = new RegleNettoyage();
        $form = $this->createCreateForm($entity);

        // les regles et leurs actions
        $regles = $em->getRepository('BackOfficeCleanBundle:RegleNettoyage')->findAll();

        $actions = $em->getRepository('BackOfficeActionBundle:Action')->findBy(
            array('type' => 'NETTOYAGE', 'module' => 'REGLE', 'etat' => 'attente')
        );
        $reglesLancees = [];
        foreach ($actions as $a) {
            $reglesLancees[$a->getNumCPT()]=$a->getDtAction();
        }

        // reports des nettoyage mensuel et annuel
        $paramLogMensuel = array(
            'niveau' => Log::NIVEAU_SUCCESS,
            'module' => 'BackOffice > Nettoyage',
            'action' => 'Mensuel'
        );

        $logNettoyageMensuel = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramLogMensuel,
            array('datetime' => 'DESC')
        );

        $paramLogAnnuel = array(
            'niveau' => Log::NIVEAU_SUCCESS,
            'module' => 'BackOffice > Nettoyage',
            'action' => 'Annuel'
        );

        $logNettoyageAnnuel = $em->getRepository('BackOfficeMonitoringBundle:Log')->findOneBy(
            $paramLogAnnuel,
            array('datetime' => 'DESC')
        );

        $param = array(
            'regles'                => $regles,
            'form'                  => $form->createView(),
            'reglesLancees'         => $reglesLancees,
            'logNettoyageAnnuel'    => $logNettoyageAnnuel,
            'logNettoyageMensuel'   => $logNettoyageMensuel
        );

        return $this->render('BackOfficeCleanBundle:Default:index.html.twig', $param);
    }

    public function launchAction(RegleNettoyage $regle)
    {
        $em = $this->getDoctrine()->getManager();

        $actions = $em->getRepository('BackOfficeActionBundle:Action')->findBy(
            array('type' => 'NETTOYAGE', 'module' => 'REGLE', 'etat' => 'attente', 'numCpt'=>$regle->getId())
        );

        if (!$actions) {
            $action = new Action();

            $action
                ->setType('NETTOYAGE')
                ->setModule('REGLE')
                ->setNumCpt($regle->getId());

            $em->persist($action);
            $em->flush();
        }

        return new Response();
    }

    /**
     * Creates a new RegleNettoyage entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new RegleNettoyage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('back_office_clean_index'));
    }

    /**
     * Creates a form to create a RegleNettoyage entity.
     *
     * @param RegleNettoyage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RegleNettoyage $entity)
    {
        $form = $this->createForm(new RegleNettoyageType(), $entity, array(
            'action' => $this->generateUrl('reglenettoyage_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to edit an existing RegleNettoyage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackOfficeCleanBundle:RegleNettoyage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegleNettoyage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackOfficeCleanBundle:RegleNettoyage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a RegleNettoyage entity.
    *
    * @param RegleNettoyage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RegleNettoyage $entity)
    {
        $form = $this->createForm(new RegleNettoyageType(), $entity, array(
            'action' => $this->generateUrl('reglenettoyage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing RegleNettoyage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackOfficeCleanBundle:RegleNettoyage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegleNettoyage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('reglenettoyage_edit', array('id' => $id)));
        }

        return $this->render('BackOfficeCleanBundle:RegleNettoyage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a RegleNettoyage entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackOfficeCleanBundle:RegleNettoyage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegleNettoyage entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('back_office_clean_index'));
    }
}
