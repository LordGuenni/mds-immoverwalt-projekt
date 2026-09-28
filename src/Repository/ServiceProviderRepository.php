<?php

namespace App\Repository;

use App\Entity\ServiceProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceProvider[]    findAll()
 * @method ServiceProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceProvider::class);
    }

    /**
     * für das Auswahlfeld im Aufgabenformular
     */
    public function createActiveQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where("s.status = 'ACTIVE'")
            ->orderBy('s.company', 'ASC');
    }
}
