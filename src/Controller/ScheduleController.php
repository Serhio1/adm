<?php

namespace App\Controller;

use App\Entity\Schedule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Controller\CrudController;
use Symfony\Component\Form\FormError;

class ScheduleController extends CrudController
{
    protected $entity = "Schedule";

    public function create(Request $request)
    {
        //$locale = $this->get('session')->get('_locale');
        //$locale = $request->getLocale();
        //print_r($locale);
        $em = $this->getDoctrine()->getManager();
        $entityPath = $this->entityPath . $this->entity;
        $entity = new $entityPath();
        $worshifts = $em->getRepository('App:Workshift')->findAll();
        $worshiftArr = array();
        foreach ($worshifts as $worshift) {
            $worshiftArr[] = array(
                'name' => $worshift->getName(),
                'attr' => array(
                    'style' => 'background-color:'. $worshift->getColor(),
                    'title' => $worshift->getDescription()
                )
            );
        }

        $form = $this->createForm(
            $this->formPath . ucfirst($this->entity) . 'Form',
            $entity, [
                'mode' => 'create',
                'flags' => $worshiftArr
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            //Check if name exists
            $nameExists = $this->getDoctrine()
                ->getRepository($entityPath)
                ->findOneBy(array('name' => $formData->getName()));
            if (!empty($nameExists)) {
                $form->get('name')->addError(
                    new FormError('Schedule with this name already exists')
                );
                $this->templateData['form'] = $form->createView();
                return $this->render($this->templates['create'], $this->templateData);
            }
            $mapping = $formData->getMapping();
            $mappingArr = (array)json_decode($mapping);
            $weekArr = array_keys($mappingArr);
            //print_r($weekArr);
            //print_r($mappingArr);
            foreach($mappingArr as $week => $weekMapping){
                $entity = new $entityPath();
                $entity->setMapping(json_encode($weekMapping));
                $entity->setWeek(array_search($week, $weekArr));
                $entity->setName($formData->getName());
                $em->persist($entity);
            }
            //die;
            $em->flush();

            return $this->redirectToRoute(strtolower($this->entity) . '_view', array(
                'name' => $entity->getName()
            ));
        }
        $this->templateData['form'] = $form->createView();

        return $this->render($this->templates['create'], $this->templateData);
    }

    public function edit($name, Request $request)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($entityPath)->findBy(array('name'=>$name));
        if (!$entities) {
            throw $this->createNotFoundException('No entity found');
        }
        //$this->denyAccessUnlessGranted('edit', $issuedUser);

        $this->formData['edit']['entities'] = $entities;
        $worshifts = $em->getRepository('App:Workshift')->findAll();
        $worshiftArr = array();
        foreach ($worshifts as $worshift) {
            $worshiftArr[] = array(
                'name' => $worshift->getName(),
                'attr' => array(
                    'style' => 'background-color:'. $worshift->getColor(),
                    'title' => $worshift->getDescription()
                )
            );
        }

        $this->formData['edit']['flags'] = $worshiftArr;

        $form = $this->createForm(
            $this->formPath . ucfirst($this->entity) . 'Form',
            $entities[0], $this->formData['edit']
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            $mapping = json_decode($formData->getMapping());
            $weekArr = array_keys((array)$mapping);
            foreach ($entities as $entity) {
                $id = $entity->getId();
                $entity->setName($formData->getName());
                // update existing mapping

                if (property_exists($mapping, $id)) {
                    $data = json_encode($mapping->$id);
                    $entity->setMapping($data);
                    $entity->setWeek(array_search($id, $weekArr));
                    $em->persist($entity);
                }
                // remove mapping if id not delivered from form
                if (!property_exists($mapping, $id)) {
                    $em->remove($entity);
                    $em->flush();
                }

            }

            // create new mapping, if added
            foreach ($mapping as $id => $data) {
                if (strpos($id, 'new_') !== false) {
                    $entity = new $entityPath();
                    $entity->setMapping(json_encode($mapping->$id));
                    $entity->setName($formData->getName());
                    $entity->setWeek(array_search($id, $weekArr));
                    $em->persist($entity);
                }
            }
            //print_r($weekArr);
            //die;

            $em->flush();

            return $this->redirectToRoute(strtolower($this->entity) . '_view', array(
                'name' => $entity->getName()
            ));
        }
        $this->templateData['form'] = $form->createView();

        return $this->render($this->templates['edit'], $this->templateData);
    }

    public function view($name, Request $request)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository($entityPath)->findBy(array('name'=>$name));
        $fields = $entities[0]->publicFields;
        $entityData = array();
        foreach ($entities as $entity) {
            $id = $entity->getId();
            foreach ($fields as $field) {
                $getter = 'get' . ucfirst($field);
                $entityData[$id][$field] = $entity->$getter();
            }
        }
        $this->templateData['entity'] = $entityData;

        return $this->render('Schedule/view.html.twig', $this->templateData);
    }

    public function attend($userid, $name)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $schedules = $em->getRepository($entityPath)->findBy(array('name'=>$name));
        $user = $em->getRepository('App:User')->find($userid);

        if (!$schedules) {
            throw $this->createNotFoundException('No schedule found with name '.$name);
        }

        foreach ($schedules as $schedule) {
            if (!$schedule->hasAttendee($user)) {
                $schedule->getAttendees()->add($user);
            }
            $em->persist($schedule);
        }

        $em->flush();

        return new Response('ok');
    }

    public function unattend($userid, $name)
    {
        $entityPath = $this->entityPath . $this->entity;
        $em = $this->getDoctrine()->getManager();
        $schedules = $em->getRepository($entityPath)->findBy(array('name'=>$name));
        $user = $em->getRepository('App:User')->find($userid);
        if (!$schedules) {
            throw $this->createNotFoundException('No schedule found with name '.$name);
        }

        foreach ($schedules as $schedule) {
            if ($schedule->hasAttendee($user)) {
                $schedule->getAttendees()->removeElement($user);
            }
            $em->persist($schedule);
        }
        $em->flush();

        return new Response('ok');
    }
}