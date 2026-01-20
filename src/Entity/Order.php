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
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 20, unique: true)]
    private string $orderNumber; // Numéro unique de commande, ex: "ORD20260120-001"

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: "json")]
    private array $items = []; // Tableau JSON des produits et quantités

    #[ORM\Column(type: "float")]
    private float $total = 0;

    #[ORM\Column(type: "string", length: 50)]
    private string $status = 'pending'; // pending / paid / shipped

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    // ===== CONSTRUCTEUR =====
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->orderNumber = 'ORD' . date('YmdHis'); // simple génération par timestamp
    }

    // ===== GETTERS / SETTERS =====
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }
    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getUser(): User
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
