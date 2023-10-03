<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserTradeController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {

    }

    public function __invoke($id): array
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $applicantTrades = ['applicant' => $user->getTrades()];

        $userPostTrades = ['user_post' => $this->em->getRepository(Trade::class)->findBy(['userPostOwner' => $user])];

        $allTrades = array_merge($applicantTrades, $userPostTrades);

        return $allTrades;
    }
}
