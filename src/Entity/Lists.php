<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListsRepository")
 */
class Lists
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var VolunteerEntity $volunteerId
     * @ORM\ManyToOne(targetEntity="App\Entity\VolunteerEntity", inversedBy="lists")
     */
    private $volunteerId;

    public function __construct()
    {
        $this->volunteerId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return VolunteerEntity
     */
    public function getVolunteerId(): VolunteerEntity
    {
        return $this->volunteerId;
    }

    /**
     * @param VolunteerEntity $volunteerId
     */
    public function setVolunteerId(VolunteerEntity $volunteerId): void
    {
        $this->volunteerId = $volunteerId;
    }


}
