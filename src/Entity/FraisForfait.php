<?php

namespace App\Entity;

use App\Repository\FraisForfaitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FraisForfaitRepository::class)]
class FraisForfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $montant = null;

    /**
     * @var Collection<int, LigneFraisForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisForfait::class, mappedBy: 'fraisForfait')]
    private Collection $ligneFraisForfait;

    public function __construct()
    {
        $this->ligneFraisForfait = new ArrayCollection();
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

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return Collection<int, LigneFraisForfait>
     */
    public function getLigneFraisForfait(): Collection
    {
        return $this->ligneFraisForfait;
    }

    public function addLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): static
    {
        if (!$this->ligneFraisForfait->contains($ligneFraisForfait)) {
            $this->ligneFraisForfait->add($ligneFraisForfait);
            $ligneFraisForfait->setFraisForfait($this);
        }

        return $this;
    }

    public function removeLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): static
    {
        if ($this->ligneFraisForfait->removeElement($ligneFraisForfait)) {
            // set the owning side to null (unless already changed)
            if ($ligneFraisForfait->getFraisForfait() === $this) {
                $ligneFraisForfait->setFraisForfait(null);
            }
        }

        return $this;
    }
}
