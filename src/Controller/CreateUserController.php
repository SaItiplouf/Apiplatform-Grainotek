<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserController extends AbstractController
{
    public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher)
    {

        $inputs = $request->toArray();
        $user = new User();

        $user->setEmail($inputs['email']);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $inputs['password']
            )
        );

        return $user;
    }
}
