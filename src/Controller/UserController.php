<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserCredentials;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('api/v1/user/')]
class UserController extends AbstractController
{
    #[Route('register', methods: 'POST')]
    public function register(Request $req, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse | Response
    {
        $data = json_decode($req->getContent(), true);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        $credentials = new UserCredentials();
        $credentials->setPassword($data['password']);
        $credentialsErrors = $validator->validate($credentials);

        if (count($credentialsErrors) > 0 ) 
        {
            return new JsonResponse(['errors' => (string) $credentialsErrors], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = password_hash( $data['password'], PASSWORD_ARGON2I );
        $credentials->setPassword($hashedPassword);
        $user->setCredentials($credentials);
        $credentials->setUser($user);

        $userErrors = $validator->validate($user);

        if (count($userErrors) > 0 ) 
        {
            return new JsonResponse(['errors' => (string) $userErrors], Response::HTTP_BAD_REQUEST);
        }
            
        try
        {
            $entityManager->persist($user);
            $entityManager->persist($credentials);
            $entityManager->flush();
        } 
        catch (\Exception $e) 
        {
            return new JsonResponse(['Caught exception:' => $e->getMessage()]);
        }
        
        
        if( $entityManager->contains($user) )
        {
            return new JsonResponse("User successfully created", 200);
        }
        else
        {
            return new JsonResponse("Error creating user", 409);
        }
    }

}
