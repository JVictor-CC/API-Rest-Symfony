<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('api/v1/user/')]
class AuthController extends AbstractController
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    #[Route('login', methods: 'POST')]
    public function login(Request $req, SessionInterface $session): JsonResponse
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
    public function logout(SessionInterface $session): JsonResponse
    {
        $session->remove('authenticated');
        $session->remove('username');

        return new JsonResponse('Successfully Logged Out!', 200);
    }
}