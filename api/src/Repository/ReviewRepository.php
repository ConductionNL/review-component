<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function calculateRating($organization, $resource = false): int
    {
        $query = $this->createQueryBuilder('r')
            ->andWhere('r.organization LIKE :organization')
            ->setParameter('organization', $organization)
            ->select('AVG(r.rating) as rating');

        if($resource){
            $query
                ->andWhere('r.resource LIKE :resource')
                ->setParameter('resource', $resource);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function calculateReviews($organization, $resource = false): int
    {
        $query = $this->createQueryBuilder('r')
            ->andWhere('r.organization LIKE :organization')
            ->setParameter('organization', $organization)
            ->select('COUNT(r.id) as reviews');

        if($resource){
            $query
                ->andWhere('r.resource LIKE :resource')
                ->setParameter('resource', $resource);
        }

        return $query->getQuery()->getSingleScalarResult();
    }


    // /**
    //  * @return Review[] Returns an array of Review objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
