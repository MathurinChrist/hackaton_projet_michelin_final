<?php

namespace App\Repository;

use App\Entity\FactCheck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FactCheck>
 *
 * @method FactCheck|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactCheck|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactCheck[]    findAll()
 * @method FactCheck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactCheckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactCheck::class);
    }
}
