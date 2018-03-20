<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
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
}
