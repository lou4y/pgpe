<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Jour = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $debut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $fin = null;

    #[ORM\ManyToOne(inversedBy: 'matieres')]
    #[ORM\JoinColumn(nullable: true,onDelete: 'SET NULL')]
    private ?Professeur $Professeur = null;

    #[ORM\ManyToOne(inversedBy: 'matieres',)]
    #[ORM\JoinColumn(nullable: true,onDelete: 'SET NULL')]
    private ?Groupes $Groupe = null;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: Absence::class)]
    private Collection $Absence;

    public function __construct()
    {
        $this->Absence = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getJour(): ?string
    {
        return $this->Jour;
    }

    public function setJour(string $Jour): static
    {
        $this->Jour = $Jour;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): static
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): static
    {
        $this->fin = $fin;

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->Professeur;
    }

    public function setProfesseur(?Professeur $Professeur): static
    {
        $this->Professeur = $Professeur;

        return $this;
    }

    public function getGroupe(): ?Groupes
    {
        return $this->Groupe;
    }

    public function setGroupe(?Groupes $Groupe): static
    {
        $this->Groupe = $Groupe;

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsence(): Collection
    {
        return $this->Absence;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->Absence->contains($absence)) {
            $this->Absence->add($absence);
            $absence->setMatiere($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->Absence->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getMatiere() === $this) {
                $absence->setMatiere(null);
            }
        }

        return $this;
    }
}
