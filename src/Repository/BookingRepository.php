<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Owner;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return Booking[]
     */
    public function findForStatement(Owner $owner, Property $property, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.owner = :owner')
            ->andWhere('b.property = :property')
            ->andWhere('b.date BETWEEN :from AND :to')
            ->setParameter('owner', $owner)
            ->setParameter('property', $property)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('b.type', 'DESC')
            ->addOrderBy('b.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Booking[]
     */
    public function findLatest(int $limit = 100): array
    {
        return $this->findBy([], ['date' => 'DESC', 'id' => 'DESC'], $limit);
    }
}
