<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $name;

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le nom de l'album
    public function getName(): string
    {
        return $this->name;
    }

    // Setter pour le nom de l'album
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
