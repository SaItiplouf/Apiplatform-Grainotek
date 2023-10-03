<?php

namespace App\Controller;

use ApiPlatform\Doctrine\Orm\Paginator;
use App\Repository\PostCommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetAllCommentsFromPostController extends AbstractController
{

    public function __construct(private PostCommentRepository $repository)
    {
    }

    public function __invoke(Request $request, $id): Paginator
    {
        $page = (int)$request->query->get('page', 1);
        return $this->repository->getCommentsFromPost($id, $page);
    }
}