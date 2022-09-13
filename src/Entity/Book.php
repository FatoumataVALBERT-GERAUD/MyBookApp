<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 255)]
    private string $author;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Covers::class, orphanRemoval: true, cascade: ["persist"])]
    private $covers;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->covers = new ArrayCollection();
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Covers>
     */
    public function getCovers(): Collection
    {
        return $this->covers;
    }

    public function addCover(Covers $cover): self
    {
        if (!$this->covers->contains($cover)) {
            $this->covers[] = $cover;
            $cover->setBook($this);
        }

        return $this;
    }

    public function removeCover(Covers $cover): self
    {
        if ($this->covers->removeElement($cover)) {
            // set the owning side to null (unless already changed)
            if ($cover->getBook() === $this) {
                $cover->setBook(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
