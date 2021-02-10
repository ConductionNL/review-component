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
            ->setParameter('organization', '%'.$organization.'%')
            ->select('COUNT(r.id) as likes');

        if ($resource) {
            $query
                ->andWhere('r.resource LIKE :resource')
                ->setParameter('resource', '%'.$resource.'%');
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function checkLiked($author, $resource, $organization = false)
    {
        if ($author and $resource) {
            $query = $this->createQueryBuilder('r')
                ->andWhere('r.author = :author')
                ->setParameter('author', $author)
                ->andWhere('r.resource = :resource')
                ->setParameter('resource', $resource)
                ->select('COUNT(r.id) as likes');

            if ($organization) {
                $query
                    ->andWhere('r.organization = :organization')
                    ->setParameter('organization', $organization);
            }

            $likes = $query->getQuery()->getSingleScalarResult();

            if ($likes > 0) {
                return true;
            }
        }

        return false;
    }
}
