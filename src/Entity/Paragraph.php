<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ParagraphRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un paragraphe d'une histoire
 */
#[ORM\Entity(repositoryClass: ParagraphRepository::class)]
class Paragraph
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column]
    private bool $isStartParagraph = false;

    #[ORM\ManyToOne(inversedBy: 'paragraphs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Story $story = null;

    #[ORM\OneToMany(mappedBy: 'sourceParagraph', targetEntity: Choice::class, orphanRemoval: true)]
    private Collection $sourceChoices;

    #[ORM\OneToMany(mappedBy: 'destinationParagraph', targetEntity: Choice::class)]
    private Collection $destinationChoices;

    public function __construct()
    {
        $this->sourceChoices = new ArrayCollection();
        $this->destinationChoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function isStartParagraph(): bool
    {
        return $this->isStartParagraph;
    }

    public function setIsStartParagraph(bool $isStartParagraph): static
    {
        $this->isStartParagraph = $isStartParagraph;
        return $this;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }

    public function setStory(?Story $story): static
    {
        $this->story = $story;
        return $this;
    }

    /**
     * @return Collection<int, Choice>
     */
    public function getSourceChoices(): Collection
    {
        return $this->sourceChoices;
    }

    public function addSourceChoice(Choice $sourceChoice): static
    {
        if (!$this->sourceChoices->contains($sourceChoice)) {
            $this->sourceChoices->add($sourceChoice);
            $sourceChoice->setSourceParagraph($this);
        }
        return $this;
    }

    public function removeSourceChoice(Choice $sourceChoice): static
    {
        if ($this->sourceChoices->removeElement($sourceChoice)) {
            if ($sourceChoice->getSourceParagraph() === $this) {
                $sourceChoice->setSourceParagraph(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Choice>
     */
    public function getDestinationChoices(): Collection
    {
        return $this->destinationChoices;
    }

    public function addDestinationChoice(Choice $destinationChoice): static
    {
        if (!$this->destinationChoices->contains($destinationChoice)) {
            $this->destinationChoices->add($destinationChoice);
            $destinationChoice->setDestinationParagraph($this);
        }
        return $this;
    }

    public function removeDestinationChoice(Choice $destinationChoice): static
    {
        if ($this->destinationChoices->removeElement($destinationChoice)) {
            if ($destinationChoice->getDestinationParagraph() === $this) {
                $destinationChoice->setDestinationParagraph(null);
            }
        }
        return $this;
    }
}
