<?php

namespace App\Repository;

use App\Entity\PostCommentLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostCommentLike>
 *
 * @method PostCommentLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostCommentLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostCommentLike[]    findAll()
 * @method PostCommentLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostCommentLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostCommentLike::class);
    }

//    /**
//     * @return PostCommentLike[] Returns an array of PostCommentLike objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PostCommentLike
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
