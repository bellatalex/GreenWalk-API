<?php

namespace App\Repository;

use App\Entity\Greenwalk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

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

    // Find the location of the GreenWalk by coordinates

    public function findAllByCoordinate ($latitude, $longitude): array
    {
        $sql = '
        SELECT * , SQRT(
            POW(69.1 * (latitude - :latitude), 2) +
            POW(69.1 * (:longitude - longitude) * COS(latitude / 57.3), 2)
          ) AS distance
        FROM greenwalk g
        WHERE g.`datetime` > NOW()
        AND state = 1
        ORDER BY distance
        ';

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Greenwalk', 'g');
        $q = $this->_em->createNativeQuery($sql, $rsm);
        $q->setParameters(['longitude' => $longitude, 'latitude' => $latitude]);

        return $q->getResult();
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
