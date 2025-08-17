<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Condition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Condition>
 *
 * @method Condition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Condition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Condition[]    findAll()
 * @method Condition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Condition::class);
    }

    public function save(Condition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Condition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
