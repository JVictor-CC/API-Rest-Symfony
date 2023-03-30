<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('api/v1/user/')]
class AuthController extends AbstractController
{
    private $userRepo;
    private $entityManager;

    public function __construct(UserRepository $userRepo, EntityManagerInterface $entityManager)
    {
        $this->userRepo = $userRepo;
        $this->entityManager = $entityManager;
    }

    #[Route('login', methods: 'POST')]
    public function login( Request $req, SessionInterface $session ): JsonResponse
    {   
        if($session->get('authenticated'))
        {
            return new JsonResponse("Already logged in as {$session->get('username')}");
        }
        else
        {
            $data = json_decode($req->getContent(), true);
            try 
            {
                $user = $this->userRepo->findOneBy(['email' => $data['email']]);
                $credentials = $user->getCredentials()->getPassword();
                
            } catch (\Exception $e) 
            {
                return new JsonResponse(['Caught exception:' => $e->getMessage()]);
            }

            if (!$user || !password_verify($data['password'], $credentials)) {
                return new JsonResponse("Invalid password or email", 401);
            } else {
                $session->set('authenticated', true);
                $session->set('email', $user->getEmail());
                $session->set('username', $user->getUsername());
                
                return new JsonResponse("Succesfully logged in");
            }
        }
    }

    #[Route('logout', methods: 'POST')]
    public function logout( SessionInterface $session ): JsonResponse
    {
        $session->remove('authenticated');
        $session->remove('username');
        $session->invalidate();

        return new JsonResponse('Successfully Logged Out!', 200);
    }

    #[Route('reset_password', methods: 'POST')]
    public function reset_password( Request $req, SessionInterface $session ): JsonResponse
    {
        
        if($session->get('authenticated'))
        {
            $data = json_decode($req->getContent(), true);
            try
            {
                $user = $this->userRepo->findOneBy(['email' => $session->get('email')]);
                if(password_verify($data['old_password'], $user->getCredentials()->getPassword()))
                {
                    $user->getCredentials()->setPassword(password_hash( $data['new_password'], PASSWORD_ARGON2I ));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $this->logout($session);
                    return new JsonResponse('Password successfully changed!', 200);
                }
                else
                {
                    return new JsonResponse('Please, insert the correct password!', 400);
                }
            }
            catch (\Exception $e)
            {
                return new JsonResponse($e->getMessage(), 500);
            }      
        }
        else
        {
            return new JsonResponse('You have to login in order to change your password!', 400);
        }
    }
}