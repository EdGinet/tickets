<?php

namespace App\Entity;

use App\Repository\ComptesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
 

#[ORM\Entity(repositoryClass: ComptesRepository::class)]
class Comptes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[UniqueEntity('email')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    private ?string $Mot_De_Passe = null;

    #[ORM\Column(length: 255)]
    private ?string $Sel = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $Id_Utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->Mot_De_Passe;
    }

    public function setMotDePasse(string $Mot_De_Passe): static
    {
        $this->Mot_De_Passe = $Mot_De_Passe;

        return $this;
    }

    public function getSel(): ?string
    {
        return $this->Sel;
    }

    public function setSel(string $Sel): static
    {
        $this->Sel = $Sel;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateurs
    {
        return $this->Id_Utilisateur;
    }

    public function setIdUtilisateur(Utilisateurs $Id_Utilisateur): static
    {
        $this->Id_Utilisateur = $Id_Utilisateur;

        return $this;
    }
}
