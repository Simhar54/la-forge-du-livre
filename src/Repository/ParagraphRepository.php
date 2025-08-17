<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Paragraph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paragraph>
 *
 * @method Paragraph|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paragraph|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paragraph[]    findAll()
 * @method Paragraph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParagraphRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paragraph::class);
    }

    public function save(Paragraph $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Paragraph $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve le paragraphe de départ d'une histoire
     */
    public function findStartParagraphByStory(int $storyId): ?Paragraph
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.story = :storyId')
            ->andWhere('p.isStartParagraph = :isStart')
            ->setParameter('storyId', $storyId)
            ->setParameter('isStart', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
