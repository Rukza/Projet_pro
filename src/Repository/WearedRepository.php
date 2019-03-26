<?php

namespace App\Repository;

use App\Entity\Weared;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Weared|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weared|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weared[]    findAll()
 * @method Weared[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WearedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Weared::class);
    }

   
    // /**
    //  * @return Weared[] Returns an array of Weared objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Weared
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
