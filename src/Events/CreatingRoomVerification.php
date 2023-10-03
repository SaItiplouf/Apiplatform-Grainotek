<?php

namespace App\Events;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Repository\TradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\HttpFoundation\Request;


#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Room::class)]
class CreatingRoomVerification
{
    public function __construct(private EntityManagerInterface $em, private RoomRepository $roomRepository)
    {

    }

    public function prePersist(Room $room, PrePersistEventArgs $event): void
    {
        $trade = $room->getTrade();
        if ($room->getTrade()) {
            $existingRoomForTrade = $this->roomRepository->findOneBy([
                'trade' => $trade,
            ]);
            if ($existingRoomForTrade) {
                throw new \Exception('Une Room avec le trade fourni existe déjà.');
            }
            $trade->setStatut("accepted");
        }
    }
}