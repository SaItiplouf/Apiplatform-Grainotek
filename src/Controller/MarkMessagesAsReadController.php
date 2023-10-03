<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkMessagesAsReadController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['user']['id'] ?? null;
        $roomId = $data['room']['id'] ?? null;

        if (!$userId || !$roomId) {
            return new JsonResponse(['error' => 'Invalid data provided'], Response::HTTP_BAD_REQUEST);
        }

        $room = $this->em->getRepository(Room::class)->find($roomId);
        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        foreach ($room->getMessages() as $message) {
            if ($message->getUser()->getId() !== $userId) {
                $message->setReaded(false);
            }
        }

        $this->em->flush();

        return new JsonResponse([
            'message' => sprintf(
                'Messages of the Room %d have been marked as read for the User %d',
                $roomId,
                $userId
            )
        ], Response::HTTP_OK);
    }
}
