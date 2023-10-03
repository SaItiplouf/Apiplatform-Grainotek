<?php

namespace App\Service;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class MercureNotifier
{
    private PublisherInterface $publisher;

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    public function sendNotification(string $topic, array $data)
    {
        // Create an instance of Update for the notification
        $update = new Update(
            $topic,
            json_encode($data)
        );

        // Publish the update to Mercure
        $this->publisher->__invoke($update);
    }
}
