<?php


namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PatchUserController extends AbstractController
{
    public function __invoke(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): User
    {

        $requestData = json_decode($request->getContent(), true);


        if (isset($requestData['email'])) {
            $user->setEmail($requestData['email']);
        }

        if (isset($requestData['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $requestData['password']);
            $user->setPassword($hashedPassword);
        }

        if (isset($requestData['username'])) {
            $user->setUsername($requestData['username']);
        }

        $em->flush();
        return $user;
    }
}
