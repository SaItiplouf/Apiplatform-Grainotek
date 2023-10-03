<?php

namespace App\Controller;

use App\Repository\PostImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostImageController extends AbstractController
{
    public function getByPost(int $postId, PostImageRepository $repository): JsonResponse
    {
        $images = $repository->findByPost($postId);

        return new JsonResponse($images);
    }
}