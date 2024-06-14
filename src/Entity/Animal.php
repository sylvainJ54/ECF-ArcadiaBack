<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $state = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VeterinaryReport $veterinaryReport = null;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: Breed::class, orphanRemoval: true)]
    private Collection $breeds;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: Habitat::class, orphanRemoval: true)]
    private Collection $habitats;

    public function __construct()
    {
        $this->breeds = new ArrayCollection();
        $this->habitats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getVeterinaryReport(): ?VeterinaryReport
    {
        return $this->veterinaryReport;
    }

    public function setVeterinaryReport(?VeterinaryReport $veterinaryReport): static
    {
        $this->veterinaryReport = $veterinaryReport;

        return $this;
    }

    /**
     * @return Collection<int, Breed>
     */
    public function getBreeds(): Collection
    {
        return $this->breeds;
    }

    public function addBreed(Breed $breed): static
    {
        if (!$this->breeds->contains($breed)) {
            $this->breeds->add($breed);
            $breed->setAnimal($this);
        }

        return $this;
    }

    public function removeBreed(Breed $breed): static
    {
        if ($this->breeds->removeElement($breed)) {
            
            if ($breed->getAnimal() === $this) {
                $breed->setAnimal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Habitat>
     */
    public function getHabitats(): Collection
    {
        return $this->habitats;
    }

    public function addHabitat(Habitat $habitat): static
    {
        if (!$this->habitats->contains($habitat)) {
            $this->habitats->add($habitat);
            $habitat->setAnimal($this);
        }

        return $this;
    }

    public function removeHabitat(Habitat $habitat): static
    {
        if ($this->habitats->removeElement($habitat)) {
            // set the owning side to null (unless already changed)
            if ($habitat->getAnimal() === $this) {
                $habitat->setAnimal(null);
            }
        }

        return $this;
    }
}
