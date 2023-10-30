<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\Criteria;
use App\Entity\PostCommentLike;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    const ITEMS_PER_PAGE = 3;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostCommentLike::class);
    }

    public function getPaginatedPostCommentLikes(User $user, EntityManagerInterface $em, $page = 1)
    {
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder
            ->select('COUNT(pcl.id)')
            ->from(PostCommentLike::class, 'pcl')
            ->where('pcl.user = :user')
            ->setParameter('user', $user);

        $totalItems = $queryBuilder->getQuery()->getSingleScalarResult();

        // Calculez le nombre total de pages
        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);

        $firstResult = ($page - 1) * self::ITEMS_PER_PAGE;

        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder
            ->select('pcl')
            ->from(PostCommentLike::class, 'pcl')
            ->where('pcl.user = :user')
            ->setParameter('user', $user)
            ->setFirstResult($firstResult)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        $doctrinePaginator = new Paginator($queryBuilder);

//        return ['data' => $doctrinePaginator, 'totalPages' => $totalPages, 'totalItems' => $totalItems];
        return $doctrinePaginator;
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
