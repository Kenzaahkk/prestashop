<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebutPrestation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinPrestation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutPrestation(): ?\DateTimeInterface
    {
        return $this->dateDebutPrestation;
    }

    public function setDateDebutPrestation(\DateTimeInterface $dateDebutPrestation): static
    {
        $this->dateDebutPrestation = $dateDebutPrestation;

        return $this;
    }

    public function getDateFinPrestation(): ?\DateTimeInterface
    {
        return $this->dateFinPrestation;
    }

    public function setDateFinPrestation(\DateTimeInterface $dateFinPrestation): static
    {
        $this->dateFinPrestation = $dateFinPrestation;

        return $this;
    }


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Prestation::class, inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): self
    {
        $this->prestation = $prestation;

        return $this;
    }
}
