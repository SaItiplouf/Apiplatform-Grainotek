<?php

namespace App\Events;

use App\Entity\User;
use DateTime;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTEventListener
{

    /**
     * @throws Exception
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof User) {
            throw new Exception("User not found", 500);
        }

        $date = new DateTime(date('Y-m-d H:i:s', strtotime('+1 hour')));
        $payload['exp'] = $date->format('U');
        $payload['id'] = $user->getId();
        $payload['email'] = $user->getEmail();
        $payload['pictureUrl'] = $user->getPictureUrl();

        $header = $event->getHeader();
        $event->setData($payload);
        $event->setHeader($header);
    }
}
