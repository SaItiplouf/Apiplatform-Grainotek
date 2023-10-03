<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserController extends AbstractController
{
    public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): User
    {
        $data = $request->request->all();
        $user = new User();
        $user->setEmail($data['email'])
            ->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Gestion de l'envoi d'image
        if ($request->files->has('pictureFile')) {
            $user->setPictureFile($request->files->get('pictureFile'));
        }
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        $em->persist($user);
        $em->flush();

        return $user;

    }
}
