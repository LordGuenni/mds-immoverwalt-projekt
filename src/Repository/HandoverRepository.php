<?php

namespace App\Repository;

use App\Entity\Handover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Handover|null find($id, $lockMode = null, $lockVersion = null)
 * @method Handover|null findOneBy(array $criteria, array $orderBy = null)
 * @method Handover[]    findAll()
 * @method Handover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HandoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Handover::class);
    }
}
