<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\KnowledgeBaseRepository::class)]
class KnowledgeBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $status = null;

    #[ORM\Column(type: 'text')]
    private ?string $michelinInfo = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $baseScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $stars = 0;

    #[ORM\Column(length: 255)]
    private ?string $searchKeywords = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $specialties = null;

    #[ORM\Column(type: 'text', nullable: true)]                                                                                 
    private ?string $atmosphere = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function setStars(?int $stars): static
    {
        $this->stars = $stars;
        return $this;
    }

    public function getMichelinInfo(): ?string
    {
        return $this->michelinInfo;
    }

    public function setMichelinInfo(string $michelinInfo): self
    {
        $this->michelinInfo = $michelinInfo;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getBaseScore(): ?float
    {
        return $this->baseScore;
    }

    public function setBaseScore(float $baseScore): self
    {
        $this->baseScore = $baseScore;
        return $this;
    }

    public function getSearchKeywords(): ?string
    {
        return $this->searchKeywords;
    }

    public function setSearchKeywords(string $searchKeywords): self
    {
        $this->searchKeywords = $searchKeywords;
        return $this;
    }

    public function getSpecialties(): ?string
    {
        return $this->specialties;
    }

    public function setSpecialties(?string $specialties): self
    {
        $this->specialties = $specialties;
        return $this;
    }

    public function getAtmosphere(): ?string
    {
        return $this->atmosphere;
    }

    public function setAtmosphere(?string $atmosphere): self
    {
        $this->atmosphere = $atmosphere;
        return $this;
    }
}
