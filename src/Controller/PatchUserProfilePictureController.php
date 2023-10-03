<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Filesystem\Filesystem;

class PatchUserProfilePictureController extends AbstractController
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): User
    {
        if ($request->files->has('pictureFile')) {
            $oldPicturePath = 'public/images/profiles/' . $user->getPicture();

            if ($user->getPicture() !== null && $this->filesystem->exists($oldPicturePath)) {
                $this->filesystem->remove($oldPicturePath);
                $user->erasePictureFromEntity();
            }
            $user->setPicture(null);

            $user->setPictureFile($request->files->get('pictureFile'));
            $user->setUpdatedAt(new \DateTime());
            $em->persist($user);
        } else {
            throw new BadRequestHttpException('Aucun fichier de photo de profil n\'a été fourni.');
        }
        $em->flush();
        return $user;
    }

}
