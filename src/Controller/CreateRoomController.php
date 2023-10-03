<?php

namespace App\Controller;

use App\Repository\PostCommentRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Room;

class CreateRoomController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private RoomRepository $repository)
    {
    }

    public function __invoke($room): Room
    {
        $existingRoom = $this->repository->findOneBy([
            'trade' => $room->getTrade(),
        ]);

        if ($existingRoom) {
            throw new \Exception('Room with the provided trade already exists.');
        }

        $this->em->persist($room);
        $this->em->flush();

        return $room;
    }

}