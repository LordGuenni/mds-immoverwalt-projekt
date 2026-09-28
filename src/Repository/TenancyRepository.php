<?php

namespace App\Repository;

use App\Entity\RentalUnit;
use App\Entity\Tenancy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tenancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tenancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tenancy[]    findAll()
 * @method Tenancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenancyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tenancy::class);
    }

    /**
     * Aktive Verträge einer Einheit. Gekündigte Verträge laufen noch und
     * zählen deshalb ebenfalls als aktiv.
     *
     * @return Tenancy[]
     */
    public function findActiveForUnit(RentalUnit $unit): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM App\Entity\Tenancy t
                 WHERE t.unit = :unit
                 AND t.status IN (:states)'
            )
            ->setParameter('unit', $unit)
            ->setParameter('states', [Tenancy::ACTIVE, Tenancy::NOTICE_GIVEN])
            ->getResult();
    }

    /**
     * Verträge der Einheit, deren Laufzeit sich mit dem Zeitraum überschneidet.
     *
     * @return Tenancy[]
     */
    public function findOverlapping(RentalUnit $unit, \DateTimeInterface $start, ?\DateTimeInterface $end, ?int $excludeId = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.unit = :unit')
            ->andWhere('t.status != :draft')
            ->andWhere('t.endDate IS NULL OR t.endDate >= :start')
            ->setParameter('unit', $unit)
            ->setParameter('draft', Tenancy::DRAFT)
            ->setParameter('start', $start);

        if ($end) {
            $qb->andWhere('t.startDate <= :end')->setParameter('end', $end);
        }
        if ($excludeId) {
            $qb->andWhere('t.id != :id')->setParameter('id', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Tenancy[]
     */
    public function findCurrent(): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('u', 'b', 'p')
            ->join('t.unit', 'u')
            ->join('u.building', 'b')
            ->join('b.property', 'p')
            ->where('t.status IN (:states)')
            ->setParameter('states', [Tenancy::ACTIVE, Tenancy::NOTICE_GIVEN])
            ->orderBy('p.name')
            ->addOrderBy('u.unitNumber')
            ->getQuery()
            ->getResult();
    }

    public function nextContractNumber(): string
    {
        $count = (int) $this->createQueryBuilder('t')->select('COUNT(t.id)')->getQuery()->getSingleScalarResult();

        return sprintf('MV-%05d', 1000 + $count + 1);
    }
}
