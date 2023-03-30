<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserCredentials;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('api/v1/user/')]
class UserController extends AbstractController
{

    private $entityManager;
    private $validator;

    public function __construct( EntityManagerInterface $entityManager, ValidatorInterface $validator )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }


    #[Route('register', methods: 'POST')]
    public function register(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent(), true);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        $credentials = new UserCredentials();
        $credentials->setPassword($data['password']);
        $credentialsErrors = $this->validator->validate($credentials);

        if (count($credentialsErrors) > 0 ) 
        {
            return new JsonResponse(['errors' => (string) $credentialsErrors]);
        }

        $hashedPassword = password_hash( $data['password'], PASSWORD_ARGON2I );
        $credentials->setPassword($hashedPassword);
        $user->setCredentials($credentials);
        $credentials->setUser($user);

        $userErrors = $this->validator->validate($user);

        if (count($userErrors) > 0 ) 
        {
            return new JsonResponse(['errors' => (string) $userErrors]);
        }
            
        try
        {
            $this->entityManager->persist($user);
            $this->entityManager->persist($credentials);
            $this->entityManager->flush();
        } 
        catch (\Exception $e) 
        {
            return new JsonResponse(['Caught exception:' => $e->getMessage()]);
        }
        
        if( $this->entityManager->contains($user) )
        {
            return new JsonResponse("User successfully created", 200);
        }
        else
        {
            return new JsonResponse("Error creating user", 409);
        }
    }

}
