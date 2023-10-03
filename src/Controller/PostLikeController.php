<?php


namespace App\Controller;

use App\Entity\PostComment;
use App\Entity\PostCommentLike;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostLikeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(Request $request): PostCommentLike
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['user']['id'] ?? null;
        $postcommentId = $data['postcomment']['id'] ?? null;

        if ($userId === null && $postcommentId === null) {
            throw new HttpException(400, 'Both user and postcomment IDs are null');
        }

        $user = $userId ? $this->em->getRepository(User::class)->find($userId) : null;
        $postcomment = $postcommentId ? $this->em->getRepository(PostComment::class)->find($postcommentId) : null;

        if (!$user && !$postcomment) {
            throw new HttpException(404, 'User and PostComment not found');
        }

        $existingLike = $this->em->getRepository(PostCommentLike::class)->findOneBy([
            'postcomment' => $postcomment,
            'user' => $user,
        ]);

        if ($existingLike) {
            $existingLike->setLiked(!$existingLike->isLiked());
        } else {
            $postCommentLike = new PostCommentLike();
            $postCommentLike->setUser($user);
            $postCommentLike->setPostcomment($postcomment);
            $postCommentLike->setLiked(true);
            $this->em->persist($postCommentLike);
        }

        $this->em->flush();

        return $existingLike ?? $postCommentLike;
    }

}

