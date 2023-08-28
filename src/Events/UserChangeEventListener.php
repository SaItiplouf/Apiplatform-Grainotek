<?php

namespace App\Events;

use App\Entity\User;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
readonly class UserChangeEventListener
{
    public function __construct(private ContainerBagInterface $params)
    {

    }

    public function postUpdate(User $user, PostUpdateEventArgs $event): void
    {

        try {
            $user->setPictureUrl(
                $this->params->get('BASE_URL') . "/images/profiles/" . $user->getPicture()
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

    }
}