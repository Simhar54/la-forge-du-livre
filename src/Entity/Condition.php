<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ConditionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une condition pour afficher un choix
 */
#[ORM\Entity(repositoryClass: ConditionRepository::class)]
class Condition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $comparisonOperator = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'conditions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Choice $choice = null;

    #[ORM\ManyToOne(inversedBy: 'conditions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatDefinition $statDefinition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComparisonOperator(): ?string
    {
        return $this->comparisonOperator;
    }

    public function setComparisonOperator(string $comparisonOperator): static
    {
        $this->comparisonOperator = $comparisonOperator;
        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getChoice(): ?Choice
    {
        return $this->choice;
    }

    public function setChoice(?Choice $choice): static
    {
        $this->choice = $choice;
        return $this;
    }

    public function getStatDefinition(): ?StatDefinition
    {
        return $this->statDefinition;
    }

    public function setStatDefinition(?StatDefinition $statDefinition): static
    {
        $this->statDefinition = $statDefinition;
        return $this;
    }
}
