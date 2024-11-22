<?php

namespace App\Entity;

use App\Repository\PrestationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
class Prestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebutDisponibilite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinDisponibilite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixJour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutDisponibilite(): ?\DateTimeInterface
    {
        return $this->dateDebutDisponibilite;
    }

    public function setDateDebutDisponibilite(\DateTimeInterface $dateDebutDisponibilite): static
    {
        $this->dateDebutDisponibilite = $dateDebutDisponibilite;

        return $this;
    }

    public function getDateFinDisponibilite(): ?\DateTimeInterface
    {
        return $this->dateFinDisponibilite;
    }

    public function setDateFinDisponibilite(\DateTimeInterface $dateFinDisponibilite): static
    {
        $this->dateFinDisponibilite = $dateFinDisponibilite;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPrixJour(): ?string
    {
        return $this->prixJour;
    }

    public function setPrixJour(string $prixJour): static
    {
        $this->prixJour = $prixJour;

        return $this;
    }

    public function getAverageRating(): float
    {
        $commentaires = $this->getCommentaires();
        if ($commentaires->isEmpty()) {
            return 0; 
        }

        $totalNotes = array_reduce($commentaires->toArray(), function ($carry, $commentaire) {
            return $carry + $commentaire->getNote();
        }, 0);

        return $totalNotes / count($commentaires);
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $prestataire = null;

    public function getPrestataire(): ?User
    {
        return $this->prestataire;
    }

    public function setPrestataire(?User $prestataire): self
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    #[ORM\ManyToOne(targetEntity: TypePrestation::class, inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?typePrestation $typePrestation = null;

    public function getTypePrestation(): ?typePrestation
    {
        return $this->typePrestation;
    }

    public function setTypePrestation(?typePrestation $typePrestation): self
    {
        $this->typePrestation = $typePrestation;

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Commentaire::class, cascade: ['persist', 'remove'])]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Demande::class, cascade: ['persist', 'remove'])]
    private Collection $demandes;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }

    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setPrestation($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            if ($demande->getPrestation() === $this) {
                $demande->setPrestation(null);
            }
        }

        return $this;
    }

    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setPrestation($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            if ($commentaire->getPrestation() === $this) {
                $commentaire->setPrestation(null);
            }
        }

        return $this;
    }
}
