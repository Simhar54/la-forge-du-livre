<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une histoire interactive
 */
#[ORM\Entity(repositoryClass: StoryRepository::class)]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: Paragraph::class, orphanRemoval: true)]
    private Collection $paragraphs;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: StatDefinition::class, orphanRemoval: true)]
    private Collection $statDefinitions;

    public function __construct()
    {
        $this->paragraphs = new ArrayCollection();
        $this->statDefinitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    /**
     * @return Collection<int, Paragraph>
     */
    public function getParagraphs(): Collection
    {
        return $this->paragraphs;
    }

    public function addParagraph(Paragraph $paragraph): static
    {
        if (!$this->paragraphs->contains($paragraph)) {
            $this->paragraphs->add($paragraph);
            $paragraph->setStory($this);
        }
        return $this;
    }

    public function removeParagraph(Paragraph $paragraph): static
    {
        if ($this->paragraphs->removeElement($paragraph)) {
            if ($paragraph->getStory() === $this) {
                $paragraph->setStory(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, StatDefinition>
     */
    public function getStatDefinitions(): Collection
    {
        return $this->statDefinitions;
    }

    public function addStatDefinition(StatDefinition $statDefinition): static
    {
        if (!$this->statDefinitions->contains($statDefinition)) {
            $this->statDefinitions->add($statDefinition);
            $statDefinition->setStory($this);
        }
        return $this;
    }

    public function removeStatDefinition(StatDefinition $statDefinition): static
    {
        if ($this->statDefinitions->removeElement($statDefinition)) {
            if ($statDefinition->getStory() === $this) {
                $statDefinition->setStory(null);
            }
        }
        return $this;
    }
}
