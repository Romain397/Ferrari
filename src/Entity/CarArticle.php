<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class CarArticle
{
    #[Assert\NotBlank(message: 'Le modèle est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le modèle doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le modèle ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $model = null;

    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $title = null;

    // Le champ description n’est plus obligatoire
    #[Assert\Length(
        max: 1000,
        maxMessage: 'Le contenu ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $content = null;

    private bool $highlight = false;

    #[Assert\NotBlank(message: 'L’année est obligatoire')]
    #[Assert\Positive(message: 'L’année doit être un nombre positif')]
    #[Assert\Range(
        min: 1900,
        max: 2100,
        notInRangeMessage: 'L’année doit être comprise entre {{ min }} et {{ max }}'
    )]
    private ?int $year = null;

    // ---------- Getters & Setters ----------

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function isHighlight(): bool
    {
        return $this->highlight;
    }

    public function setHighlight(bool $highlight): self
    {
        $this->highlight = $highlight;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }

    // ---------- Debug helper ----------
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'title' => $this->title,
            'content' => $this->content,
            'highlight' => $this->highlight,
            'year' => $this->year,
        ];
    }
}
