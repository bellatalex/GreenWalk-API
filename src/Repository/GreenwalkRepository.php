<?php

namespace App\Repository;

use App\Entity\Greenwalk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Greenwalk|null find($id, $lockMode = null, $lockVersion = null)
 * @method Greenwalk|null findOneBy(array $criteria, array $orderBy = null)
 * @method Greenwalk[]    findAll()
 * @method Greenwalk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GreenwalkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Greenwalk::class);
    }

    // /**
    //  * @return Greenwalk[] Returns an array of Greenwalk objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Greenwalk
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
