<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GenreRepository")
 */
class Genre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subgenre", mappedBy="genre", cascade={"persist"})
     */
    private $subgenres;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->subgenres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Subgenre[]
     */
    public function getSubgenres(): Collection
    {
        return $this->subgenres;
    }

    public function addSubgenre(Subgenre $subgenre): self
    {
        if (!$this->subgenres->contains($subgenre)) {
            $this->subgenres[] = $subgenre;
            $subgenre->setGenre($this);
        }

        return $this;
    }

    public function removeSubgenre(Subgenre $subgenre): self
    {
        if ($this->subgenres->contains($subgenre)) {
            $this->subgenres->removeElement($subgenre);
            // set the owning side to null (unless already changed)
            if ($subgenre->getGenre() === $this) {
                $subgenre->setGenre(null);
            }
        }

        return $this;
    }
}
