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
    private int $id;

    #[ORM\Column(type: "string", length: 100)]
    private string $title;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $circuitImage = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string", length: 50)]
    private string $category; // Exemple: "Formule 1", "WEC"

    // GETTERS / SETTERS

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

    public function getCategory(): string
    {
        return $this->category;
    }
    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }
}
