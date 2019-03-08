<?php

namespace App\Repository;

use App\Entity\Requested;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Requested|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requested|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requested[]    findAll()
 * @method Requested[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Requested::class);
    }

    // /**
    //  * @return Requested[] Returns an array of Requested objects
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
    public function findOneBySomeField($value): ?Requested
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
