<?php

namespace App\Events;

use App\Entity\Message;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Message::class)]
class MessageUpdatingRoomListener
{
    public function prePersist(Message $message, PrePersistEventArgs $event): void
    {
        try {
            $room = $message->getRoom();
            $room->setLastMessage(new \DateTime());

        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

    }
}