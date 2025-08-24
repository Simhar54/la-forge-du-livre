<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un choix dans une histoire
 */
#[ORM\Entity(repositoryClass: ChoiceRepository::class)]
class Choice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\ManyToOne(inversedBy: 'sourceChoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paragraph $sourceParagraph = null;

    #[ORM\ManyToOne(inversedBy: 'destinationChoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Paragraph $destinationParagraph = null;

    #[ORM\OneToMany(mappedBy: 'choice', targetEntity: ChoiceCondition::class, orphanRemoval: true)]
    private Collection $conditions;

    #[ORM\OneToMany(mappedBy: 'choice', targetEntity: Action::class, orphanRemoval: true)]
    private Collection $actions;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function getSourceParagraph(): ?Paragraph
    {
        return $this->sourceParagraph;
    }

    public function setSourceParagraph(?Paragraph $sourceParagraph): static
    {
        $this->sourceParagraph = $sourceParagraph;
        return $this;
    }

    public function getDestinationParagraph(): ?Paragraph
    {
        return $this->destinationParagraph;
    }

    public function setDestinationParagraph(?Paragraph $destinationParagraph): static
    {
        $this->destinationParagraph = $destinationParagraph;
        return $this;
    }

    /**
     * @return Collection<int, ChoiceCondition>
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    public function addCondition(ChoiceCondition $condition): static
    {
        if (!$this->conditions->contains($condition)) {
            $this->conditions->add($condition);
            $condition->setChoice($this);
        }
        return $this;
    }

    public function removeCondition(ChoiceCondition $condition): static
    {
        if ($this->conditions->removeElement($condition)) {
            if ($condition->getChoice() === $this) {
                $condition->setChoice(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setChoice($this);
        }
        return $this;
    }

    public function removeAction(Action $action): static
    {
        if ($this->actions->removeElement($action)) {
            if ($action->getChoice() === $this) {
                $action->setChoice(null);
            }
        }
        return $this;
    }
}
