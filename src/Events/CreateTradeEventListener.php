<?php

namespace App\Events;

use App\Entity\Trade;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Trade::class)]
class CreateTradeEventListener
{
    public function __construct(private EntityManagerInterface $em)
    {

    }

    public function postPersist(Trade $trade, PostPersistEventArgs $event): void
    {

        if ($trade->getApplicant() === $trade->getPost()->getUser()) {
            throw new \LogicException("Un utilisateur ne peut pas postuler pour son propre post.");
        }

        $existingTrade = $this->em->getRepository(Trade::class)->findOneBy([
            'applicant' => $trade->getApplicant(),
            'post' => $trade->getPost(),
        ]);

        if ($existingTrade && $existingTrade->getId() !== $trade->getId()) {
            throw new \LogicException("L'utilisateur a déjà postulé pour ce post.");
        }
    }
}