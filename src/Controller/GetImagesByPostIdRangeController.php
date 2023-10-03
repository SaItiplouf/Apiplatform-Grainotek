<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetImagesByPostIdRangeController extends AbstractController
{
    public function __invoke(EntityManagerInterface $em, int $start): JsonResponse
    {

        $end = $start + 10;

        $query = $em->createQuery('
            SELECT pi
            FROM App\Entity\PostImage pi
            WHERE pi.post_id >= :start AND pi.post_id < :end
        ');
        $query->setParameter('start', $start);
        $query->setParameter('end', $end);

        $result = $query->getResult();

        return new JsonResponse($result);
    }
}
