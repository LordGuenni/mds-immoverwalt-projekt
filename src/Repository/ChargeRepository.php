<?php

namespace App\Repository;

use App\Entity\Charge;
use App\Entity\Tenancy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Charge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Charge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Charge[]    findAll()
 * @method Charge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Charge::class);
    }

    /**
     * @return Charge[]
     */
    public function findForTenancy(Tenancy $tenancy): array
    {
        return $this->findBy(['tenancy' => $tenancy], ['dueDate' => 'ASC', 'position' => 'ASC']);
    }

    /**
     * offene Sollstellungen, älteste zuerst
     *
     * @return Charge[]
     */
    public function findOpenForTenancy(Tenancy $tenancy): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.tenancy = :t')
            ->andWhere("c.status != 'PAID'")
            ->setParameter('t', $tenancy)
            ->orderBy('c.dueDate', 'ASC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function existsForMonth(Tenancy $tenancy, string $month): bool
    {
        return null !== $this->findOneBy(['tenancy' => $tenancy, 'month' => $month]);
    }
}
