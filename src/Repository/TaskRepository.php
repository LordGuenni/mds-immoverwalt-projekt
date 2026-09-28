<?php

namespace App\Repository;

use App\Entity\Issue;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function countOpenForIssue(Issue $issue): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.issue = :issue')
            ->andWhere("t.status NOT IN ('DONE', 'CANCELLED')")
            ->setParameter('issue', $issue)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Task[]
     */
    public function findOpen(): array
    {
        return $this->createQueryBuilder('t')
            ->where("t.status NOT IN ('DONE', 'CANCELLED')")
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function nextNumber(): string
    {
        $count = (int) $this->createQueryBuilder('t')->select('COUNT(t.id)')->getQuery()->getSingleScalarResult();

        return sprintf('AU-%05d', 3000 + $count + 1);
    }
}
