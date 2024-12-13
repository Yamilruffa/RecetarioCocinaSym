<?php

namespace App\Repository;

use App\Entity\Paso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paso>
 *
 * @method Paso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paso[]    findAll()
 * @method Paso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paso::class);
    }

//    /**
//     * @return Paso[] Returns an array of Paso objects
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

//    public function findOneBySomeField($value): ?Paso
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
