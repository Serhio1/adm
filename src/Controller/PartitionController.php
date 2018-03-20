<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Participant;
use App\Entity\Partition;

class PartitionController extends Controller
{
    /**
     * @Route("/partition", name="partition")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        //$participant = new Participant();
        $partition = new Partition();

        //$participant->setName('user1');
        //$partition->setName('sometext');

        //$participant->getPartitions()->add($partition);

        //$em->persist($participant);
        $em->persist($partition);
        $em->flush();


        return new Response('Welcome to your new controller!');
    }
}
