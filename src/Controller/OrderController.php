<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Entity\Shipment;
use App\Entity\Category;

class OrderController extends Controller
{
    /**
     * @Route("/", name="order")
     */
    public function index()
    {
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();

        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(99.99);

        $shipment = new Shipment();
        $shipment->setName('Nova poshta 9');

        $product->setShipment($shipment);

        $em->persist($product);
        $em->persist($shipment);
        $em->flush();
        
        return new Response($product->getId() . ' / ' . $shipment->getId());
    }

    /**
     * @Route("/view/{id}", name="order", requirements={"id"="\d+"})
     */
    public function view($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $shipment = $product->getShipment();
        
        return new Response('Product: ' . $product->getName()
            . ' / Shipment: ' . $shipment->getName());
    }

    /**
     * @Route("/category/create", name="category")
     */
    public function createCategory()
    {
        $em = $this->getDoctrine()->getManager();

        $category = new Category;
        $category->setName('periferals');

        $shipment = new Shipment();
        $shipment->setName('ukrposhta');

        $product = new Product();
        $product->setName('flashdrive');
        $product->setPrice(10.45);
        $product->setShipment($shipment);
        $product->setCategory($category);

        $em->persist($category);
        $em->persist($shipment);
        $em->persist($product);
        $em->flush();

        return new Response('category: ' . $category->getName() . 
            'shipment: ' . $shipment->getName() .
            'product: ' . $product->getName() . ' ' . $product->getPrice());
    }

    /**
     * @Route("/category/{name}", name="viewcategory")
     */
    public function viewCategory($name)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(array('name' => $name));

        return new Response($category[0]->getId() . $category[0]->getName());
    }
}
