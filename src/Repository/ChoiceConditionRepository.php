<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ChoiceCondition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChoiceCondition>
 *
 * @method ChoiceCondition|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChoiceCondition|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChoiceCondition[]    findAll()
 * @method ChoiceCondition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChoiceConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoiceCondition::class);
    }

    public function save(ChoiceCondition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChoiceCondition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
