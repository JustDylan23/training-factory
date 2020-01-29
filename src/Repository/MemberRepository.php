<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

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
        return $this->search($qb, $term)
            ->orderBy('m.surname', 'ASC');
    }

    public function getQueryBuilderMembersNotInLesson(Lesson $lesson, ?string $search)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.registrations', 'r', Join::WITH, 'r.lesson = :lesson')
            ->setParameter('lesson', $lesson)
            ->andWhere('r.member is null');
        return $this->search($qb, $search);
    }

    private function search(QueryBuilder &$qb, ?string $search): QueryBuilder
    {
        if ($search) {
            $qb->andWhere('m.firstname LIKE :term OR m.surname LIKE :term OR m.surnamePrepositions LIKE :term OR m.email LIKE :term')
                ->setParameter('term', '%' . $search . '%');
        }
        return $qb;
    }

    public function getQueryBuilderMembersInLesson(Lesson $lesson, $search)
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.registrations', 'r', Join::WITH, 'r.lesson = :lesson')
            ->setParameter('lesson', $lesson->getId());
        return $this->search($qb, $search);
    }
}
