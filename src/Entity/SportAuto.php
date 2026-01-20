<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sport_auto")]
class SportAuto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100)]
    private string $title;

    #[ORM\Column(type: "string", length: 100)]
    private string $location; // Lieu de la course

    #[ORM\Column(type: "string", length: 50)]
    private string $carCategory; // Formule 1, WEC, GT, etc.

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null; // Petite description

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $circuitImage = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date;

    // ===== GETTERS / SETTERS =====

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getCarCategory(): string
    {
        return $this->carCategory;
    }
    public function setCarCategory(string $carCategory): self
    {
        $this->carCategory = $carCategory;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCircuitImage(): ?string
    {
        return $this->circuitImage;
    }
    public function setCircuitImage(?string $circuitImage): self
    {
        $this->circuitImage = $circuitImage;
        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }
    public function setVideo(?string $video): self
    {
        $this->video = $video;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
}
