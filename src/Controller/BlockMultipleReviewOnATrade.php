<?php

namespace App\Controller;

use App\Entity\UserReview;
use App\Repository\UserReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlockMultipleReviewOnATrade extends AbstractController
{
    public function __construct(private UserReviewRepository $reviewRepository, private EntityManagerInterface $em)
    {
    }

    public function __invoke(UserReview $review): UserReview
    {
        $currentUser = $review->getUser();

        $trade = $review->getTrade();

        $existingReview = $this->reviewRepository->findOneBy(['user' => $currentUser, 'trade' => $trade]);

        if ($existingReview) {
            throw new \Exception("L'utilisateur a déjà posté une revue pour cet échange.");
        }

        $this->em->persist($review);
        $this->em->flush();

        return $review;
    }
}