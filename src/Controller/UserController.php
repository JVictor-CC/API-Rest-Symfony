<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/user/')]
class UserController extends AbstractController
{
    #[Route('register')]
    public function register(Request $req, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = dump($req->request->all());

        $user = new User;
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);

        dump($user);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
