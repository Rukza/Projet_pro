<?php

namespace App\Repository;

use App\Entity\SerialNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SerialNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method SerialNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method SerialNumber[]    findAll()
 * @method SerialNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerialNumberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SerialNumber::class);
    }

    // /**
    //  * @return SerialNumber[] Returns an array of SerialNumber objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SerialNumber
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
