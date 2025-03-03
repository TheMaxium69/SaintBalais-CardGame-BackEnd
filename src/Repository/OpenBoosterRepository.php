<?php

namespace App\Repository;

use App\Entity\OpenBooster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpenBooster>
 */
class OpenBoosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpenBooster::class);
    }

    public function findTwoLatestBoosterByUser($user){


        $result = $this->createQueryBuilder('ob')
            ->andWhere('ob.user_id = :user')
            ->setParameter('user', $user)
            ->orderBy('ob.open_at', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();

        return $result;

    }



    //    /**
    //     * @return OpenBooster[] Returns an array of OpenBooster objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OpenBooster
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
