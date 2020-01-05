<?php

namespace App\Repository;

use App\Entity\Instructor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Instructor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instructor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instructor[]    findAll()
 * @method Instructor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstructorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instructor::class);
    }

    public function getWithSearchQueryBuilder(?string $term)
    {
        $qb = $this->createQueryBuilder('i');
        if ($term) {
            $qb->andWhere('i.firstname LIKE :term OR i.surname LIKE :term OR i.surnamePrepositions LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('i.surname', 'ASC');
    }
}
