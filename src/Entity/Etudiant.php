<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $N_Inscriptionn = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $CIN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nom_Ar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Prenom_Ar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nom_Fr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Prenom_Fr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Sexe = null;

    #[ORM\Column(nullable: true)]
    private ?int $Situation_Familiale = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Date_de_naissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Lieu_de_naissance_AR = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Lieu_de_naissance_FR = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Passeport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Adresse_Fr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Adresse_Ar = null;

    #[ORM\Column(nullable: true)]
    private ?int $Code_gouvernorat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Email = null;

    #[ORM\Column(nullable: true)]
    private ?int $Telephone_Fixe = null;

    #[ORM\Column(nullable: true)]
    private ?int $Telephone_Portable = null;

    #[ORM\Column(nullable: true)]
    private ?int $Code_Nature_Bac = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Inscription = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false,onDelete: 'CASCADE')]
    private ?Groupes $Groupe = null;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Absence::class)]
    private Collection $Absence;

    public function __construct()
    {
        $this->Absence = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNInscriptionn(): ?int
    {
        return $this->N_Inscriptionn;
    }

    public function setNInscriptionn(int $N_Inscriptionn): static
    {
        $this->N_Inscriptionn = $N_Inscriptionn;

        return $this;
    }

    public function getCIN(): ?string
    {
        return $this->CIN;
    }

    public function setCIN(?string $CIN): static
    {
        $this->CIN = $CIN;

        return $this;
    }

    public function getNomAr(): ?string
    {
        return $this->Nom_Ar;
    }

    public function setNomAr(?string $Nom_Ar): static
    {
        $this->Nom_Ar = $Nom_Ar;

        return $this;
    }

    public function getPrenomAr(): ?string
    {
        return $this->Prenom_Ar;
    }

    public function setPrenomAr(?string $Prenom_Ar): static
    {
        $this->Prenom_Ar = $Prenom_Ar;

        return $this;
    }

    public function getNomFr(): ?string
    {
        return $this->Nom_Fr;
    }

    public function setNomFr(?string $Nom_Fr): static
    {
        $this->Nom_Fr = $Nom_Fr;

        return $this;
    }

    public function getPrenomFr(): ?string
    {
        return $this->Prenom_Fr;
    }

    public function setPrenomFr(?string $Prenom_Fr): static
    {
        $this->Prenom_Fr = $Prenom_Fr;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->Sexe;
    }

    public function setSexe(?string $Sexe): static
    {
        $this->Sexe = $Sexe;

        return $this;
    }

    public function getSituationFamiliale(): ?int
    {
        return $this->Situation_Familiale;
    }

    public function setSituationFamiliale(?int $Situation_Familiale): static
    {
        $this->Situation_Familiale = $Situation_Familiale;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->Date_de_naissance;
    }

    public function setDateDeNaissance(?\DateTimeInterface $Date_de_naissance): static
    {
        $this->Date_de_naissance = $Date_de_naissance;

        return $this;
    }

    public function getLieuDeNaissanceAR(): ?string
    {
        return $this->Lieu_de_naissance_AR;
    }

    public function setLieuDeNaissanceAR(?string $Lieu_de_naissance_AR): static
    {
        $this->Lieu_de_naissance_AR = $Lieu_de_naissance_AR;

        return $this;
    }

    public function getLieuDeNaissanceFR(): ?string
    {
        return $this->Lieu_de_naissance_FR;
    }

    public function setLieuDeNaissanceFR(?string $Lieu_de_naissance_FR): static
    {
        $this->Lieu_de_naissance_FR = $Lieu_de_naissance_FR;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(?string $Statut): static
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getPasseport(): ?string
    {
        return $this->Passeport;
    }

    public function setPasseport(?string $Passeport): static
    {
        $this->Passeport = $Passeport;

        return $this;
    }

    public function getAdresseFr(): ?string
    {
        return $this->Adresse_Fr;
    }

    public function setAdresseFr(?string $Adresse_Fr): static
    {
        $this->Adresse_Fr = $Adresse_Fr;

        return $this;
    }

    public function getAdresseAr(): ?string
    {
        return $this->Adresse_Ar;
    }

    public function setAdresseAr(?string $Adresse_Ar): static
    {
        $this->Adresse_Ar = $Adresse_Ar;

        return $this;
    }

    public function getCodeGouvernorat(): ?int
    {
        return $this->Code_gouvernorat;
    }

    public function setCodeGouvernorat(?int $Code_gouvernorat): static
    {
        $this->Code_gouvernorat = $Code_gouvernorat;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getTelephoneFixe(): ?int
    {
        return $this->Telephone_Fixe;
    }

    public function setTelephoneFixe(?int $Telephone_Fixe): static
    {
        $this->Telephone_Fixe = $Telephone_Fixe;

        return $this;
    }

    public function getTelephonePortable(): ?int
    {
        return $this->Telephone_Portable;
    }

    public function setTelephonePortable(?int $Telephone_Portable): static
    {
        $this->Telephone_Portable = $Telephone_Portable;

        return $this;
    }

    public function getCodeNatureBac(): ?int
    {
        return $this->Code_Nature_Bac;
    }

    public function setCodeNatureBac(?int $Code_Nature_Bac): static
    {
        $this->Code_Nature_Bac = $Code_Nature_Bac;

        return $this;
    }

    public function getInscription(): ?string
    {
        return $this->Inscription;
    }

    public function setInscription(?string $Inscription): static
    {
        $this->Inscription = $Inscription;

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
            $absence->setEtudiant($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->Absence->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEtudiant() === $this) {
                $absence->setEtudiant(null);
            }
        }

        return $this;
    }
}
