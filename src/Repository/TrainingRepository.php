<?php

namespace App\Repository;

use App\Entity\Training;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Training|null find($id, $lockMode = null, $lockVersion = null)
 * @method Training|null findOneBy(array $criteria, array $orderBy = null)
 * @method Training[]    findAll()
 * @method Training[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Training::class);
    }

    /**
     * @return Training[]
     */
    public function getFeaturedTrainings()
    {
        return $this->createQueryBuilder('training')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();
    }

    public function getWithSearchQueryBuilder(?string $term): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');
        if ($term) {
            $qb->andWhere('t.name LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('t.name', 'ASC');
    }
}
