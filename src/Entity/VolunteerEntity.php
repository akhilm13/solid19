<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VolunteerEntityRepository")
 */
class VolunteerEntity
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $phone;

    /**
     * @ORM\Column(type="decimal", scale=8, precision=10, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", scale=8, precision=10, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime $createdAt
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string")
     * @var string $messageToShoppers
     */
    private $messageToShoppers = "";

    /**
     * @ORM\Column(type="string")
     * @var string $messageToOtherVolunteers
     */
    private $messageToOtherVolunteers = "";

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lists", mappedBy="volunteerId")
     */
    private $lists;

    public function __construct()
    {
        $this->lists = new ArrayCollection();
        $this->createdAt = new DateTime('now');
    }


    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return Collection|Lists[]
     */
    public function getLists(): Collection
    {
        return $this->lists;
    }

    public function addList(Lists $list): self
    {
        if (!$this->lists->contains($list)) {
            $this->lists[] = $list;
            $list->addVolunteerId($this);
        }

        return $this;
    }

    public function removeList(Lists $list): self
    {
        if ($this->lists->contains($list)) {
            $this->lists->removeElement($list);
            $list->removeVolunteerId($this);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getMessageToShoppers(): string
    {
        return $this->messageToShoppers;
    }

    /**
     * @param string $messageToShoppers
     */
    public function setMessageToShoppers(string $messageToShoppers): void
    {
        if($messageToShoppers === null){
            $messageToShoppers = "";
        }
        $this->messageToShoppers = $messageToShoppers;
    }

    /**
     * @return string
     */
    public function getMessageToOtherVolunteers(): string
    {
        return $this->messageToOtherVolunteers;
    }

    /**
     * @param string $messageToOtherVolunteers
     */
    public function setMessageToOtherVolunteers(string $messageToOtherVolunteers): void
    {
        if ($messageToOtherVolunteers === null){
            $messageToOtherVolunteers = "";
        }
        $this->messageToOtherVolunteers = $messageToOtherVolunteers;
    }
}
