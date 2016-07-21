<?php

namespace BackOffice\EvolutionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use BackOffice\EvolutionBundle\Entity\Evolution;
use BackOffice\EvolutionBundle\Form\EvolutionType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Evolution controller.
 *
 */
class EvolutionController extends Controller
{

    /**
     * Lists all Evolution entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BackOfficeEvolutionBundle:Evolution')->findAll();
        $adapter = new ArrayAdapter($entities);
        $pagerfanta = new Pagerfanta($adapter);
        
        if ($number = $request->request->get('number')) {
            $pagerfanta->setMaxPerPage($number);
        }
        
        $page = $request->query->get('page') ? $request->query->get('page') : 1;
        $pagerfanta->setCurrentPage($page);

        return $this->render('BackOfficeEvolutionBundle:Evolution:index.html.twig', array(
            'entities' => $pagerfanta,
            'number'   => $number
        ));
    }
    /**
     * Creates a new Evolution entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Evolution();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evolution_show', array('id' => $entity->getId())));
        }

        return $this->render('BackOfficeEvolutionBundle:Evolution:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Evolution entity.
     *
     * @param Evolution $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Evolution $entity)
    {
        $form = $this->createForm(new EvolutionType(), $entity, array(
            'action' => $this->generateUrl('evolution_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Evolution entity.
     *
     */
    public function newAction()
    {
        $entity = new Evolution();
        $form   = $this->createCreateForm($entity);

        return $this->render('BackOfficeEvolutionBundle:Evolution:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Evolution entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackOfficeEvolutionBundle:Evolution')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evolution entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BackOfficeEvolutionBundle:Evolution:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Evolution entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackOfficeEvolutionBundle:Evolution')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evolution entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BackOfficeEvolutionBundle:Evolution:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Evolution entity.
    *
    * @param Evolution $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Evolution $entity)
    {
        $form = $this->createForm(new EvolutionType(), $entity, array(
            'action' => $this->generateUrl('evolution_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Evolution entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackOfficeEvolutionBundle:Evolution')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evolution entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('evolution_show', array('id' => $id)));
        }

        return $this->render('BackOfficeEvolutionBundle:Evolution:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Evolution entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackOfficeEvolutionBundle:Evolution')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Evolution entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('evolution'));
    }

    /**
     * Creates a form to delete a Evolution entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evolution_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
