<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @return Appointment[]
     */
    public function findUpcoming(int $limit = 50): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.startsAt >= :today')
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('a.startsAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Appointment[]
     */
    public function findForRef(string $refType, int $refId): array
    {
        return $this->findBy(['refType' => $refType, 'refId' => $refId], ['startsAt' => 'ASC']);
    }
}
