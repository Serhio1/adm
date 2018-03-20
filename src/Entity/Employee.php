<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
//use App\Entity\Person;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmployeeRepository")
 */
class Employee extends Person
{
    /**
     * @ORM\Column(type="integer")
     */
    protected $number;

    // add your own fields
}
