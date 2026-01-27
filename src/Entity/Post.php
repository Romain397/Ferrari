<?php

namespace App\Entity;

use App\Config\Category;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    #[Assert\Length(min: 3, max: 100)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000)]
    private ?string $content = null;

    #[ORM\Column]
    private bool $highlight = false;

    #[ORM\Column(nullable: true)]
    private ?int $year = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L’image doit être une URL valide')]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'La vidéo doit être une URL valide')]
    private ?string $video = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'L’image du circuit doit être une URL valide')]
    private ?string $circuitImage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $raceDate = null;

    #[ORM\Column(type: Types::ENUM)]
    private Category $category;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /* ───────────── GETTERS / SETTERS ───────────── */

    public function getId(): ?int
    {
        return $this->id;
    }

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
    public function setTitle(string $title): self
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

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): self
    {
        $this->image = $image;
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

    public function getCircuitImage(): ?string
    {
        return $this->circuitImage;
    }
    public function setCircuitImage(?string $circuitImage): self
    {
        $this->circuitImage = $circuitImage;
        return $this;
    }

    public function getRaceDate(): ?\DateTime
    {
        return $this->raceDate;
    }
    public function setRaceDate(?\DateTime $raceDate): self
    {
        $this->raceDate = $raceDate;
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /* ───────────── VALIDATION CONDITIONNELLE ───────────── */

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->category === Category::Voiture) {

            if (empty($this->model)) {
                $context->buildViolation('Le modèle est obligatoire pour une voiture.')
                    ->atPath('model')
                    ->addViolation();
            }

            if (empty($this->image)) {
                $context->buildViolation('L’image de la voiture est obligatoire.')
                    ->atPath('image')
                    ->addViolation();
            }

            if (!empty($this->circuitImage)) {
                $context->buildViolation('Une voiture ne peut pas avoir d’image de circuit.')
                    ->atPath('circuitImage')
                    ->addViolation();
            }

            if (!empty($this->raceDate)) {
                $context->buildViolation('Une voiture ne peut pas avoir de date de course.')
                    ->atPath('raceDate')
                    ->addViolation();
            }
        }

        if ($this->category === Category::Course) {

            if (empty($this->circuitImage)) {
                $context->buildViolation('L’image du circuit est obligatoire pour une course.')
                    ->atPath('circuitImage')
                    ->addViolation();
            }
        }
    }
}
