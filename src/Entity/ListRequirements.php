<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListRequirementsRepository")
 */
class ListRequirements
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lists")
     * @ORM\JoinColumn(nullable=false,  onDelete="CASCADE")
     */
    private $listId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $listItem;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=8)
     * @var string $shopperId
     */
    private $shopperId;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime $dateTimeChanged
     */
    private $dateTimeStatusChanged;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListId(): ?Lists
    {
        return $this->listId;
    }

    public function setListId(?Lists $listId): self
    {
        $this->listId = $listId;

        return $this;
    }

    public function getListItem(): ?string
    {
        return $this->listItem;
    }

    public function setListItem(string $listItem): self
    {
        $this->listItem = $listItem;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        $this->dateTimeStatusChanged = new DateTime('now');
        return $this;
    }

    public function toArray(){

        return array(
            'id' => $this->id,
            'listItem' => $this->listItem,
            'quantity' => $this->quantity
        );
    }

    /**
     * @return DateTime
     */
    public function getDateTimeStatusChanged(): DateTime
    {
        return $this->dateTimeStatusChanged;
    }

    /**
     * @return string
     */
    public function getShopperId(): string
    {
        return $this->shopperId;
    }

    /**
     * @param string $shopperId
     */
    public function setShopperId(string $shopperId): void
    {
        $this->shopperId = $shopperId;
    }
}
