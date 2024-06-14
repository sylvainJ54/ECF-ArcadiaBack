<?php

namespace App\Repository;

use App\Entity\VeterinaryReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VeterinaryReport>
 *
 * @method VeterinaryReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method VeterinaryReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method VeterinaryReport[]    findAll()
 * @method VeterinaryReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VeterinaryReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VeterinaryReport::class);
    }

//    /**
//     * @return VeterinaryReport[] Returns an array of VeterinaryReport objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VeterinaryReport
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
