<?php

namespace App\Events;

use App\Entity\User;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\PostUpdate;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class UserChangeEventListener
{
    public function __construct(private ContainerBagInterface $params)
    {

    }

    #[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
    public function postPersist(User $user, PostPersistEventArgs $event): void
    {
        try {
            if ($user->getPicture()) {
                $user->setPictureUrl(
                    $this->params->get('BASE_URL') . "/images/profiles/" . $user->getPicture()
                );
            } else {
                $user->setPictureUrl($this->params->get('BASE_URL') . '/images/perma/ppdefault.png');
            }

        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
    }

    #[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
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