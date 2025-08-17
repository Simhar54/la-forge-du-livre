<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StatDefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant la définition d'une statistique pour une histoire
 */
#[ORM\Entity(repositoryClass: StatDefinitionRepository::class)]
class StatDefinition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private int $initialValue = 0;

    #[ORM\ManyToOne(inversedBy: 'statDefinitions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Story $story = null;

    #[ORM\OneToMany(mappedBy: 'statDefinition', targetEntity: Condition::class)]
    private Collection $conditions;

    #[ORM\OneToMany(mappedBy: 'statDefinition', targetEntity: Action::class)]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getInitialValue(): int
    {
        return $this->initialValue;
    }

    public function setInitialValue(int $initialValue): static
    {
        $this->initialValue = $initialValue;
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
     * @return Collection<int, Condition>
     */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    public function addCondition(Condition $condition): static
    {
        if (!$this->conditions->contains($condition)) {
            $this->conditions->add($condition);
            $condition->setStatDefinition($this);
        }
        return $this;
    }

    public function removeCondition(Condition $condition): static
    {
        if ($this->conditions->removeElement($condition)) {
            if ($condition->getStatDefinition() === $this) {
                $condition->setStatDefinition(null);
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
            $action->setStatDefinition($this);
        }
        return $this;
    }

    public function removeAction(Action $action): static
    {
        if ($this->actions->removeElement($action)) {
            if ($action->getStatDefinition() === $this) {
                $action->setStatDefinition(null);
            }
        }
        return $this;
    }
}
