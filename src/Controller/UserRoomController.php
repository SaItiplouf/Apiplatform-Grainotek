<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserRoomController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {

    }

    public function __invoke($id): Collection
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        return $user->getRooms();
    }
}
