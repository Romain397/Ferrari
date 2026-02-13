<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var Collection<int, Post> */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private Collection $posts;

    /** @var Collection<int, Product> */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'user')]
    private Collection $products;

    /** @var Collection<int, Commande> */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $commandes;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    // ===== Getters / Setters =====
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
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
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * ⚡ Hash le mot de passe directement dans le setter
     */
    public function setPassword(string $plainPassword, ?UserPasswordHasherInterface $passwordHasher = null): self
    {
        // Intelephense peut se plaindre ici, mais c'est correct
        $this->password = $passwordHasher ? $passwordHasher->hashPassword($this, $plainPassword) : $plainPassword;
        return $this;
    }

    // ===== UserInterface =====
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
    public function eraseCredentials() {}

    // ===== Posts =====
    public function getPosts(): Collection
    {
        return $this->posts;
    }
    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this); // Intelephense comprend maintenant que $this est User
        }
        return $this;
    }
    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }
        return $this;
    }

    // ===== Products =====
    public function getProducts(): Collection
    {
        return $this->products;
    }
    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUser($this);
        }
        return $this;
    }
    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }
        return $this;
    }

    // ===== Commandes =====
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }
}
