<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/v1/recipes/')]
class RecipeController extends AbstractController
{
    private $userRepo;
    private $entityManager;
    private $validator;
    private $recipeRepo;

    public function __construct( UserRepository $userRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator, RecipeRepository $recipeRepository )
    {
        $this->userRepo = $userRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->recipeRepo = $recipeRepository;
    }

    #[Route('list', name: 'app_recipe')]
    public function listRecipes(Request $req): JsonResponse
    {
        $limit = $req->query->get('limit', 10);
        $recipes = $this->recipeRepo->findBy([], ['id' => 'ASC'], $limit);
        return new JsonResponse($recipes);
    }

    #[Route('new_recipe', methods: 'POST')]
    public function create_recipe( Request $req, SessionInterface $session ): JsonResponse
    {
        if($session->get('authenticated'))
        {
            $data = json_decode($req->getContent(), true);
            $user = $this->userRepo->findOneBy(['email' => $session->get('email')]);
            $recipe = new Recipe;
            $recipe->setUser($user);
            $recipe->setRecipeName($data['recipe_name']);
            $recipe->setDescription($data['description']);
            $recipe->setIngredients($data['ingredients']);
            $recipe->setInstructions($data['instructions']);

            $recipe->setPreparationTime($data['preparation_time'] ?? $recipe->getPreparationTime());
            $recipe->setCuisineType($data['cuisine_type'] ?? $recipe->getCuisineType());
            $recipe->setMealType($data['meal_type'] ?? $recipe->getMealType());

            $user->addRecipe($recipe);

            $errors = $this->validator->validate($recipe);
            if (count($errors) > 0 ) 
            {
                return new JsonResponse(['errors' => (string) $errors]);
            }

            try
            {
                $this->entityManager->persist($user);
                $this->entityManager->persist($recipe);
                $this->entityManager->flush();
                return new JsonResponse("recipe created", 200);
            }
            catch(\Exception $e)
            {
                return new JsonResponse(['Caught exception:' => $e->getMessage()]);
            }   
        }
        else
        {
            return new JsonResponse("You need to loggin to create a recipe", 409);
        }
    }

    #[Route('update_recipe/{id}', methods: 'POST')]
    public function update_recipe( Request $req, int $id, SessionInterface $session): JsonResponse
    {
        if($session->get('authenticated'))
        {
            $data = json_decode($req->getContent(), true);
            $recipe = $this->recipeRepo->findOneBy(['id' => $id]);
            
            if($session->get('email') === $recipe->getUser()->getEmail())
            {
                $recipe->setRecipeName($data['recipe_name'] ?? $recipe->getRecipeName());
                $recipe->setDescription($data['description'] ?? $recipe->getDescription());
                $recipe->setIngredients($data['ingredients'] ?? $recipe->getIngredients());
                $recipe->setInstructions($data['instructions'] ?? $recipe->getInstructions());
                $recipe->setPreparationTime($data['preparation_time'] ?? $recipe->getPreparationTime());
                $recipe->setCuisineType($data['cuisine_type'] ?? $recipe->getCuisineType());
                $recipe->setMealType($data['meal_type'] ?? $recipe->getMealType());
                $errors = $this->validator->validate($recipe);
                if (count($errors) > 0 ) 
                {
                    return new JsonResponse(['errors' => (string) $errors]);
                }

                try
                {
                    $this->entityManager->persist($recipe);
                    $this->entityManager->flush();
                    return new JsonResponse(['message' => 'Recipe updated successfully.']);
                }
                catch(\Exception $e)
                {
                    return new JsonResponse(['Caught exception:' => $e->getMessage()]);
                } 
            }
            else
            {
                return new JsonResponse(['message' => 'You can only change recipes tha you created.']);
            }
        }
        else
        {
            return new JsonResponse([ 'message' => 'You need to loggin to update a recipe'], 409);
        }
        
    }
}
