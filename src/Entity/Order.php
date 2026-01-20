<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity]
#[ORM\Table(name: "orders")]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'json')]
    private array $items = []; // tableau JSON des produits du panier

    #[ORM\Column(type: 'float')]
    private float $total;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'pending'; // pending / paid / shipped

    // GETTERS / SETTERS
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
