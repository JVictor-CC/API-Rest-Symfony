<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $recipe_name = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $ingredients = [];

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $preparation_time = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $cuisine_type = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $meal_type = null;

    #[ORM\Column(length: 400)]
    private ?string $recipe_instructions = null;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipeName(): ?string
    {
        return $this->recipe_name;
    }

    public function setRecipeName(string $recipe_name): self
    {
        $this->recipe_name = $recipe_name;

        return $this;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function setIngredients(array $ingredients): self
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getPreparationTime(): ?string
    {
        return $this->preparation_time;
    }

    public function setPreparationTime(?string $preparation_time): self
    {
        $this->preparation_time = $preparation_time;

        return $this;
    }

    public function getCuisineType(): ?string
    {
        return $this->cuisine_type;
    }

    public function setCuisineType(?string $cuisine_type): self
    {
        $this->cuisine_type = $cuisine_type;

        return $this;
    }

    public function getMealType(): ?string
    {
        return $this->meal_type;
    }

    public function setMealType(?string $meal_type): self
    {
        $this->meal_type = $meal_type;

        return $this;
    }

    public function getRecipeInstructions(): ?string
    {
        return $this->recipe_instructions;
    }

    public function setRecipeInstructions(string $recipe_instructions): self
    {
        $this->recipe_instructions = $recipe_instructions;

        return $this;
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
