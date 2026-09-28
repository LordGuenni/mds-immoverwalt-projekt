<?php

namespace App\Repository;

use App\Entity\RentalUnit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RentalUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method RentalUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method RentalUnit[]    findAll()
 * @method RentalUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RentalUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RentalUnit::class);
    }

    /**
     * Einheiten für die Übersichtsliste (inkl. Objekt und Gebäude).
     *
     * @return RentalUnit[]
     */
    public function findForList(?string $status = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('b', 'p')
            ->join('u.building', 'b')
            ->join('b.property', 'p')
            ->where('u.showInList = true')
            ->orderBy('p.name', 'ASC')
            ->addOrderBy('u.unitNumber', 'ASC');

        if ($status) {
            $qb->andWhere('u.status = :status')->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return RentalUnit[]
     */
    public function findAvailable(): array
    {
        return $this->findForList(RentalUnit::AVAILABLE);
    }
}
