<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    /**
     * @Route("/event/{id}/attend", name="event_attend")
     */
    public function attendEvent($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('App:Event')->find($id);
        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }
        $user = $em->getRepository('App:User')->find(3);
        if (!$event->hasAttendee($user)) {
            $event->getAttendees()->add($user);
        }
        $em->persist($event);
        $em->flush();

        return new Response('ok');
    }

    /**
     * @Route("/event/{id}/unattend", name="event_unattend")
     */
    public function unattendEvent($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('App:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

        $user = $em->getRepository('App:User')->find(2);
        if ($event->hasAttendee($user)) {
            $event->getAttendees()->removeElement($user);
        }

        $em->persist($event);
        $em->flush();

        return new Response('ok');
    }

    /**
     * @Route("/event/{id}/view", name="event_view")
     */
    public function viewEvent($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var $event \App\Entity\Event */
        $event = $em->getRepository('App:Event')->find($id);
        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }
        $users = $event->getAttendees();
        $em->persist($event);
        $em->flush();

        return $this->render('entitylist.twig.html',array('list' => $users));
    }



    /**
     * @Route("/event/add", name="event_add")
     */
    public function addEvent()
    {
        $em = $this->getDoctrine()->getManager();
        $event = new Event();
        $em->persist($event);
        $em->flush();

        return new Response('event added');
    }
}
