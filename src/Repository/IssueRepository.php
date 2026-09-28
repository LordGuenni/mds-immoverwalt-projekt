<?php

namespace App\Repository;

use App\Entity\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Issue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Issue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Issue[]    findAll()
 * @method Issue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);
    }

    /**
     * @return Issue[]
     */
    public function findByStatusFilter(?string $filter): array
    {
        $qb = $this->createQueryBuilder('i')
            ->addSelect('p')
            ->join('i.property', 'p')
            ->orderBy('i.priority', 'DESC')
            ->addOrderBy('i.createdAt', 'DESC');

        if ($filter === 'open') {
            $qb->where("i.status NOT IN ('CLOSED', 'REJECTED')");
        } elseif ($filter) {
            $qb->where('i.status = :status')->setParameter('status', $filter);
        }

        return $qb->getQuery()->getResult();
    }

    public function countOpen(): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where("i.status NOT IN ('RESOLVED', 'CLOSED', 'REJECTED')")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function nextNumber(): string
    {
        $count = (int) $this->createQueryBuilder('i')->select('COUNT(i.id)')->getQuery()->getSingleScalarResult();

        return sprintf('ME-%05d', 2000 + $count + 1);
    }
}
