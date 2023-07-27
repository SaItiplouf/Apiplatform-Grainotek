<?php
namespace App\Events;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTEventListener {

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof User) {
            throw new \Exception("User not found", 500);
        }
        $date =  new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
        $payload['exp'] = $date->format('U');
        $payload['id'] = $user->getId();
        $payload['email'] = $user->getEmail();
        $header = $event->getHeader();
        $event->setData($payload);
        $event->setHeader($header);
    }
}