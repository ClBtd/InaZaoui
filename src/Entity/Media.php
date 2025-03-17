<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "medias", fetch: "EAGER")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Album::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Album $album = null;

    #[ORM\Column]
    private string $path;

    #[ORM\Column]
    private string $title;

    private ?UploadedFile $file = null;

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour l'utilisateur
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setter pour l'utilisateur
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Getter pour le chemin du fichier
    public function getPath(): string
    {
        return $this->path;
    }

    // Setter pour le chemin du fichier
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    // Getter pour le titre du fichier
    public function getTitle(): string
    {
        return $this->title;
    }

    // Setter pour le titre du fichier
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    // Getter pour le fichier UploadedFile
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    // Setter pour le fichier UploadedFile
    public function setFile(?UploadedFile $file): static
    {
        $this->file = $file;

        return $this;
    }

    // Getter pour l'album
    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    // Setter pour l'album
    public function setAlbum(?Album $album): static
    {
        $this->album = $album;

        return $this;
    }
}
