<?php

namespace App\Controller;

use App\Form\UserForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Schedule;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Roles;
use App\Entity\Workshift;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends Controller
{
    public function renderTheme($data = array())
    {
        $request = Request::createFromGlobals();


        // default theme data
        $defaults = array(
            'theme' => 'Bootstrap',
            'layout' => '12',
            'global_vars' => array(
            ),
        );
        $data = array_merge($data, $defaults);

        return $this->render('layout.html.twig', $data);
    }

    public function home()
    {

        return $this->renderTheme(array(
            'items' => array(
                'block1' => array(
                    'main_title' => array(
                        'view' => 'Components/navbar_toggle.html.twig',
                        'vars' => array(
                            'list' => array(
                                'title' => 'Menu',
                                'children' => array(
                                    array(
                                        'title' => 'My schedule',
                                        'url' => $this->generateUrl(
                                            'user_schedule',
                                            array(),
                                            UrlGeneratorInterface::ABSOLUTE_URL
                                        )
                                    )
                                )
                            )
                        )
                    )
                ),
                'block2' => [
                    'iframes' => [
                        'view' => 'Components/mainpage.html.twig'
                    ]
                ] //block2
            )
        ));
    }

    public function logout()
    {
        // replace this line with your own code!
        //return new Response('ok');
    }

    public function create(Request $request, Roles $subRoles)
    {
        $issuedUser = new User();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $subRoles = $subRoles->getAll($user);
        $form = $this->createForm(UserForm::class, $issuedUser, [
            'mode' => 'create',
            'subRoles' => $subRoles,
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData as $key => $val) {
                $setter = 'set' . ucfirst($key);
                $issuedUser->$setter($val);
            }
            $em->persist($issuedUser);
            $em->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('User/form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/edit", name="user_selfedit")
     */
    public function editSelfUser(Request $request)
    {
        $issuedUser = $this->get('security.token_storage')->getToken()->getUser();
        if (!$issuedUser) {
            throw $this->createNotFoundException('No user found');
        }
        $this->denyAccessUnlessGranted('edit', $issuedUser);
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(UserForm::class, $issuedUser, [
            'selfEdit' => true,
            'issuedUserId' => $user->getId(),
            'mode' => 'edit',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData as $key => $val) {
                $setter = 'set' . ucfirst($key);
                $issuedUser->$setter($val);
            }
            $em->persist($issuedUser);
            $em->flush();

            return $this->redirectToRoute('user_selfedit');
        }

        return $this->render('User/form.html.twig',array('form' => $form->createView()));
    }

    /**
     * @Route("/user/resetpassword", name="user_resetpassword")
     */
    public function resetUserPassword(Request $request)
    {
        $issuedUser = $this->get('security.token_storage')->getToken()->getUser();
        if (!$issuedUser) {
            throw $this->createNotFoundException('No user found');
        }
        $this->denyAccessUnlessGranted('resetPassword', $issuedUser);
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(UserForm::class, $issuedUser, [
            'selfEdit' => true,
            'issuedUserId' => $user->getId(),
            'mode' => 'resetPassword',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData as $key => $val) {
                $setter = 'set' . ucfirst($key);
                $issuedUser->$setter($val);
            }
            $em->persist($issuedUser);
            $em->flush();

            return $this->redirectToRoute('user_selfedit');
        }

        return $this->render('User/form.html.twig',array('form' => $form->createView()));
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(Request $request, $id, Roles $subRoles)
    {
        $em = $this->getDoctrine()->getManager();
        $issuedUser = $em->getRepository('App:User')->find($id);
        if (!$issuedUser) {
            throw $this->createNotFoundException('No user found');
        }
        $this->denyAccessUnlessGranted('edit', $issuedUser);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $subRoles = $subRoles->getAll($user);

        $selfEdit = false;
        if ($this->getUser()->getId() == $id) {
            $selfEdit = true;
        }
        $form = $this->createForm(UserForm::class, $issuedUser, [
            'selfEdit' => $selfEdit,
            'issuedUserId' => $id,
            'mode' => 'edit',
            'subRoles' => $subRoles,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData as $key => $val) {
                $setter = 'set' . ucfirst($key);
                $issuedUser->$setter($val);
            }
            $em->persist($issuedUser);
            $em->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('User/form.html.twig',array('form' => $form->createView()));
    }

    public function list()
    {
        $em = $this->getDoctrine()->getManager();
        $list = $em->getRepository("App:User")->findAll();

        $this->denyAccessUnlessGranted('multipleView', $list);

        return $this->render('entitylist.twig.html',array('list' => $list));
    }

    public function view($id)
    {
        return new Response('user view');
    }

    public function remove($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository("App:User")->find($id);
        $em->remove($entity);
        $em->flush();

        return new Response('user removed');
    }

    public function scheduleView()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $schedules = $user->getSchedules();
        if (!$schedules[0]) {
            return new Response('No schedule for this user');
        }
        $date = new \DateTime();
        $week = $date->format("W");

        $schedulesArr = array();
        foreach ($schedules as $key => $schedule) {
            // need to be 3 for now
            if (($week % ($schedule->getWeek()+1)) == 3) {
                $schedulesArr[$key]['current'] = $schedule->getId();
            }
            $schedulesArr[$key]['mapping'] = (array)json_decode($schedule->getMapping());
            $schedulesArr[$key]['id'] = $schedule->getId();
            $schedulesArr[$key]['name'] = $schedule->getName();
        }

        $workshifts = $em->getRepository('App:Workshift')->findAll();
        $workshiftArr = array();
        foreach ($workshifts as $workshift) {
            $workshiftArr[$workshift->getName()] = array(
                'color' => $workshift->getColor(),
                'title' => $workshift->getDescription()
            );
        }




        return $this->render('Schedule/userview.html.twig', array(
            'schedules' => $schedulesArr,
            'workshifts' => $workshiftArr
        ));
    }
}
