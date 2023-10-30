<?php

namespace App\Controller;

use App\Entity\PostCommentLike;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GetFavoriteController extends AbstractController
{
    public function __invoke(User $user, EntityManagerInterface $em, Request $request)
    {
        $page = (int)$request->query->get('page', 1);

        $postCommentLikeRepository = $em->getRepository(PostCommentLike::class);

        $postCommentLikes = $postCommentLikeRepository->getPaginatedPostCommentLikes($user, $em, $page);

        return $postCommentLikes;
    }
}
