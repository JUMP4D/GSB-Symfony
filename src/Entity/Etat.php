<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, FicheFrais>
     */
    #[ORM\OneToMany(targetEntity: FicheFrais::class, mappedBy: 'etat')]
    private Collection $ficheFrais;

    public function __construct()
    {
        $this->ficheFrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, FicheFrais>
     */
    public function getFicheFrais(): Collection
    {
        return $this->ficheFrais;
    }

    public function addFicheFrai(FicheFrais $ficheFrai): static
    {
        if (!$this->ficheFrais->contains($ficheFrai)) {
            $this->ficheFrais->add($ficheFrai);
            $ficheFrai->setFicheFrais($this);
        }

        return $this;
    }

    public function removeFicheFrai(FicheFrais $ficheFrai): static
    {
        if ($this->ficheFrais->removeElement($ficheFrai)) {
            // set the owning side to null (unless already changed)
            if ($ficheFrai->getFicheFrais() === $this) {
                $ficheFrai->setFicheFrais(null);
            }
        }

        return $this;
    }
}
