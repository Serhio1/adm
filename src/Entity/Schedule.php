<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\CrudEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule extends CrudEntity
{
    public $publicFields = array(
        'id',
        'name',
        'mapping'
    );

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $week;

    /**
     * @return mixed
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * @param mixed $week
     */
    public function setWeek($week): void
    {
        $this->week = $week;
    }

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="schedules")
     */
    protected $attendees;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param \App\Entity\User $user
     * @return bool
     */
    public function hasAttendee(User $user)
    {
        return $this->getAttendees()->contains($user);
    }

    /**
     * @ORM\Column(type="text")
     */
    private $mapping;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param mixed $mapping
     */
    public function setMapping($mapping): void
    {
        $this->mapping = $mapping;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
}
