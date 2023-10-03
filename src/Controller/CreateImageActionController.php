<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateImageActionController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $postImages = [];

        $postImagesData = $request->files->all();
        $postId = $request->request->get('post_id');

        $post = $em->getRepository(Post::class)->find($postId);
        if (!$post) {
            throw new BadRequestHttpException('Post not found');
        }
        foreach ($postImagesData as $fileData) {
            if ($fileData instanceof UploadedFile) {
                $postImage = new PostImage();
                $postImage->setPost($post);
                $postImage->setFile($fileData);
                $em->persist($postImage);
                
                $postImage->setContentUrl($request->server->get('BASE_URL') . '/images/posts/' . $postImage->filePath);
                $postImages[] = $postImage;
            }
        }

        $em->flush();

        return new JsonResponse($postImages, 201);
    }
}