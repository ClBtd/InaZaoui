<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message:"Cette valeur n'est pas une adresse email valide.")]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password;


    /** @var array<int, string> */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    /** @var Collection<int, Media> */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'user')]
    private Collection $medias;

    #[ORM\Column]
    private ?bool $access = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Setter pour l'email
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    // Getter pour le mot de passe
    public function getPassword(): string
    {
        return $this->password;
    }

    // Setter pour le mot de passe
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    // Getter pour les rôles
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Assure que chaque utilisateur a au moins le rôle 'ROLE_USER'

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    // Setter pour les rôles
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    // Getter pour le nom d'utilisateur
    public function getUsername(): string
    {
        return $this->email;
    }

    // Alias pour la méthode getUsername
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // Méthode requise pour l'interface PasswordAuthenticatedUserInterface
    public function eraseCredentials(): void
    {}

    // Getter pour le nom
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter pour le nom
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Getter pour la description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Setter pour la description
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // Getter pour la collection des médias
     /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    // Setter pour la collection des médias
    /**
     * @param Collection<int, Media> $medias
     * @return static
     */
    public function setMedias(Collection $medias): static
    {
        $this->medias = $medias;

        return $this;
    }

    // Getter pour l'accès
    public function isAccess(): ?bool
    {
        return $this->access;
    }

    // Setter pour l'accès
    public function setAccess(bool $access): static
    {
        $this->access = $access;

        return $this;
    }
}
