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
    #[Assert\NotBlank(message: 'Le modèle est obligatoire')]
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
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'La vidéo doit être une URL valide')]
    private ?string $video = null;

    #[ORM\Column(length: 255, nullable: true)]
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

    // ───────────── GETTERS / SETTERS ─────────────

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

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Debug helper
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'title' => $this->title,
            'content' => $this->content,
            'highlight' => $this->highlight,
            'createdAt' => $this->createdAt,
            'image' => $this->image,
            'video' => $this->video,
        ];
    }

    // ───────────── VALIDATION PERSONNALISÉE ─────────────
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        // Si c'est une VOITURE (Home)
        if ($this->category === Category::Voiture) {
            // Ne pas permettre les champs de course
            if (!empty($this->circuitImage)) {
                $context->buildViolation('Pour une voiture, l\'image du circuit ne doit pas être remplie.')
                    ->atPath('circuitImage')
                    ->addViolation();
            }
            if (!empty($this->raceDate)) {
                $context->buildViolation('Pour une voiture, la date de course ne doit pas être remplie.')
                    ->atPath('raceDate')
                    ->addViolation();
            }
            // Image de voiture obligatoire
            if (empty($this->image)) {
                $context->buildViolation('Pour une voiture, l\'image est obligatoire.')
                    ->atPath('image')
                    ->addViolation();
            }
        }
        // Si c'est une COURSE (Sport Auto)
        elseif ($this->category === Category::Course) {
            // Ne pas permettre les champs de voiture
            if (!empty($this->model)) {
                $context->buildViolation('Pour une course, le modèle de voiture ne doit pas être rempli.')
                    ->atPath('model')
                    ->addViolation();
            }
            if (!empty($this->year)) {
                $context->buildViolation('Pour une course, l\'année ne doit pas être remplie.')
                    ->atPath('year')
                    ->addViolation();
            }
            if (!empty($this->image)) {
                $context->buildViolation('Pour une course, l\'image de voiture ne doit pas être remplie.')
                    ->atPath('image')
                    ->addViolation();
            }
            if (!empty($this->video)) {
                $context->buildViolation('Pour une course, la vidéo ne doit pas être remplie.')
                    ->atPath('video')
                    ->addViolation();
            }
            if (!empty($this->highlight)) {
                $context->buildViolation('Pour une course, l\'option "À la une" ne doit pas être cochée.')
                    ->atPath('highlight')
                    ->addViolation();
            }
        }
    }
}
