<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function getWithSearchQueryBuilder(?string $term)
    {
        $qb = $this->createQueryBuilder('m');
        if ($term) {
            $qb->andWhere('m.firstname LIKE :term OR m.surname LIKE :term OR m.surnamePrepositions LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('m.surname', 'ASC');
    }

    public function getMembersFrom(Lesson $lesson)
    {
        return $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.registrations', 'r', Join::WITH, 'r.lesson = :lesson')
            ->setParameter('lesson', $lesson)
            ->getQuery()
            ->getResult();
    }
}
