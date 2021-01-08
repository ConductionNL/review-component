<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function calculateLikes($organization, $resource = false)
    {

        $query = $this->createQueryBuilder('r')
            ->andWhere('r.organization LIKE :organization')
            ->setParameter('organization', $organization)
            ->select('COUNT(r.id) as likes');

        if($resource){
            $query
                ->andWhere('r.resource LIKE :resource')
                ->setParameter('resource', $resource);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function checkLiked($organization, $resource = false, $user = false)
    {
        return false;
    }

}
