<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Shipment;
use App\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Shipment")
     * @ORM\JoinColumn(name="shipment_id", referencedColumnName="id")
     */
    private $shipment;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    public function setShipment(Shipment $value)
    {
        $this->shipment = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($value)
    {
        $this->price = $value;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $value)
    {
        $this->category = $value;
    }
}
