<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Prestation;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }
    public function findOverlappingDemands(\DateTime $start, \DateTime $end, Prestation $prestation): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.prestation = :prestation')
            ->andWhere('d.dateDebutPrestation < :end')
            ->andWhere('d.dateFinPrestation > :start')
            ->setParameter('prestation', $prestation)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les demandes liées à une prestation.
     */
    public function findByPrestation(Prestation $prestation): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.prestation = :prestation')
            ->setParameter('prestation', $prestation)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Demande[] Returns an array of Demande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Demande
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
