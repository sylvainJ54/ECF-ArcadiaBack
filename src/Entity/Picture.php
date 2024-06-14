<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BLOB)]
    private $pictureData = null;

    #[ORM\ManyToMany(targetEntity: Habitat::class, inversedBy: 'pictures')]
    private Collection $habitat;

    public function __construct()
    {
        $this->habitat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureData()
    {
        return $this->pictureData;
    }

    public function setPictureData($pictureData): static
    {
        $this->pictureData = $pictureData;

        return $this;
    }

    /**
     * @return Collection<int, Habitat>
     */
    public function getHabitat(): Collection
    {
        return $this->habitat;
    }

    public function addHabitat(Habitat $habitat): static
    {
        if (!$this->habitat->contains($habitat)) {
            $this->habitat->add($habitat);
        }

        return $this;
    }

    public function removeHabitat(Habitat $habitat): static
    {
        $this->habitat->removeElement($habitat);

        return $this;
    }
}
