<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CrudController extends Controller
{
    /**
     * @var $entity - name of crud entity in lowercase (user, blog, etc)
     */
    protected $entity;

    protected $entityPath = 'App\Entity\\';
    protected $formPath = 'App\Form\\';

    protected $templates = array(
        'create' => 'Crud/form.html.twig',
        'edit' => 'Crud/form.html.twig',
        'delete' => 'Crud/form.html.twig',
        'view' => 'Crud/entity_view.html.twig',
        'viewAll' => 'Crud/entity_list.html.twig',
    );
    protected $templateData = array();
    protected $formData = array(
        'create' => array(
            'mode' => 'create',
        ),
        'edit' => array(
            'mode' => 'edit',
        ),
        'delete' => array(
            'mode' => 'delete',
        ),
    );

    public function create(Request $request)
    {
        $entityPath = $this->entityPath . $this->entity;
        $entity = new $entityPath();
        $form = $this->createForm(
            $this->formPath . ucfirst($this->entity) . 'Form',
            $entity, ['mode' => 'create',]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData as $key => $val) {
                $setter = 'set' . ucfirst($key);
                $entity->$setter($val);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute(strtolower($this->entity) . '_view', array(
                'id' => $entity->getId()
            ));
        }
        $this->templateData['form'] = $form->createView();

        return $this->render($this->templates['create'], $this->templateData);
    }

    public function edit($id, Request $request)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($entityPath)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No entity found');
        }
        //$this->denyAccessUnlessGranted('edit', $issuedUser);

        $form = $this->createForm(
            $this->formPath . ucfirst($this->entity) . 'Form',
            $entity, $this->formData['edit']
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData as $key => $val) {
                $setter = 'set' . ucfirst($key);
                $entity->$setter($val);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute(strtolower($this->entity) . '_view', array(
                'id' => $entity->getId()
            ));
        }
        $this->templateData['form'] = $form->createView();

        return $this->render($this->templates['edit'], $this->templateData);
    }

    public function view($id, Request $request)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($entityPath)->find($id);
        $fields = $entity->publicFields;
        $entityData = array();
        foreach ($fields as $field) {
            $getter = 'get' . ucfirst($field);
            $entityData[$field] = $entity->$getter();
        }
        $this->templateData['entity'] = $entityData;

        return $this->render($this->templates['view'], $this->templateData);
    }

    public function viewAll(Request $request)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $list = $em->getRepository($entityPath)->findAll();
        $this->templateData['list'] = $list;

        return $this->render($this->templates['viewAll'], $this->templateData);
    }

    public function delete(Request $request)
    {
        return new Response('crud delete page');
    }
}