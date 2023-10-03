<?php

namespace App\Events;

use App\Entity\Message;
use App\Service\MercureNotifier;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Message::class)]
class MessageEventSubscriber
{
    private MercureNotifier $mercureNotifier;

    public function __construct(MercureNotifier $mercureNotifier)
    {
        $this->mercureNotifier = $mercureNotifier;
    }

    public function postPersist(Message $entity, PostPersistEventArgs $event): void
    {
        $userId = $entity->getUser()->getId();
        $userList = $entity->getRoom()->getUsers();
        $data = $this->serializeMessage($entity);

        foreach ($userList as $user) {
            $topic = "https://polocovoitapi.projets.garage404.com/api/users/{$user->getId()}/rooms";
            // Serialize the Message entity to JSON

            $this->mercureNotifier->sendNotification($topic, $data);
        }
    }

    private function serializeMessage(Message $message): array
    {
        $room = $message->getRoom();
        $messages = $room->getMessages();

        // Convert each message into an associative array
        $messageList = [];
        foreach ($messages as $msg) {
            $messageList[] = [
                'id' => $msg->getId(),
                'user' => [
                    'id' => $msg->getUser()->getId(),
                    'email' => $msg->getUser()->getEmail(),
                    'roles' => $msg->getUser()->getRoles(),
                    'username' => $msg->getUser()->getUsername(),
                    'pictureUrl' => $msg->getUser()->getPictureUrl(),
                ],
                'content' => $msg->getMessage(),
                'room' => $message->getRoom(),
                'readed' => $msg->isReaded(),
                'createdAt' => $msg->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'id' => $room->getId(),
            'name' => $room->getName(),
            'trade' => $room->getTrade(),
            'users' => array_map(function ($user) {
                return [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'pictureUrl' => $user->getPictureUrl(),
                ];
            }, $room->getUsers()->toArray()),
            'messages' => $messageList,
        ];
    }

}