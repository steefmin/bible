<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Line::class, mappedBy="book", orphanRemoval=true)
     * @OrderBy({"number" = "ASC"})
     */
    private $lines;

    /**
     * @ORM\OneToOne(targetEntity=Location::class, mappedBy="book", cascade={"persist", "remove"})
     */
    private $location;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getSpacelessAuthor(): ?string
    {
        return str_replace(' ', '', $this->author);
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSpacelessTitle(): ?string
    {
        return str_replace(' ', '', $this->title);
    }

    /**
     * @return Collection|Line[]
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(Line $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setBook($this);
        }

        return $this;
    }

    public function removeLine(Line $line): self
    {
        if ($this->lines->removeElement($line)) {
            // set the owning side to null (unless already changed)
            if ($line->getBook() === $this) {
                $line->setBook(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        // set the owning side of the relation if necessary
        if ($location->getBook() !== $this) {
            $location->setBook($this);
        }

        return $this;
    }
}
