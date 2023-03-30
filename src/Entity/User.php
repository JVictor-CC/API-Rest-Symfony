<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $saved_recipes = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Recipe::class)]
    private Collection $created_recipes;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?UserCredentials $credentials = null;

    public function __construct()
    {
        $this->created_recipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSavedRecipes(): array
    {
        return $this->saved_recipes;
    }

    public function setSavedRecipes(?array $saved_recipes): self
    {
        $this->saved_recipes = $saved_recipes;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->created_recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        $this->created_recipes->add($recipe);
        $recipe->setUser($this);
        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        $this->created_recipes->removeElement($recipe);
        return $this;
    }

    public function getCredentials(): ?UserCredentials
    {
        return $this->credentials;
    }

    public function setCredentials(UserCredentials $credentials): self
    {
        $this->credentials = $credentials;

        return $this;
    }

}
