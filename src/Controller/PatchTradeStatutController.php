<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PatchTradeStatutController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private RoomRepository $roomRepository)
    {
    }

    public function __invoke(Trade $trade): JsonResponse
    {
        // Vérifier si le trade fourni est déjà associé à une Room
        $existingRoomForTrade = $this->roomRepository->findOneBy([
            'trade' => $trade,
        ]);

        if ($existingRoomForTrade) {
            throw new \Exception('Room with the provided trade already exists.');
        }

        // Vérifier si le trade fourni a une Room associée qui est également liée à un autre trade
        if ($trade->getRoom()) {
            $existingRoom = $this->roomRepository->findOneBy([
                'id' => $trade->getRoom()->getId(),
                'trade' => $trade
            ]);

            if (!$existingRoom) {
                throw new \Exception('The provided Room is already linked to another trade.');
            }
        }

        try {
            $this->em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Le trade a été modifié avec succès.',
                'trade' => $trade
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => "Une erreur est survenue: " . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}