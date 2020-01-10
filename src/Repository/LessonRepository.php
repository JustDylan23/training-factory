<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function getWithSearchQueryBuilder(?string $term)
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.training', 't')
            ->addSelect('t');
        if ($term) {
            $qb->andWhere($qb->expr()->like('t.name', ':term'))
                ->andWhere($qb->expr()->gt('CURRENT_DATE()', 'l.time'))
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('l.time', 'ASC');
    }

    public function getWithSearchQueryBuilderAndSignedUp(?string $term, User $member)
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.training', 't')
            ->innerJoin('l.registrations', 'r')
            ->andWhere('r.member = :member')
            ->setParameter('member', $member->getId())
            ->addSelect('t');
        if ($term) {
            $qb->andWhere($qb->expr()->like('t.name', ':term'))
                ->andWhere($qb->expr()->gt('CURRENT_DATE()', 'l.time'))
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('l.time', 'ASC');
    }

    public function getWithSearchQueryBuilderAndNotSignedUp(?string $term, User $member)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->innerJoin('l.training', 't')
            ->addSelect('t')
            ->leftJoin('l.registrations', 'r', Join::WITH, 'r.member = :member')
            ->where('r.lesson is null')
            ->setParameter('member', $member->getId());
        if ($term) {
            $qb->andWhere($qb->expr()->like('t.name', ':term'))
                ->andWhere($qb->expr()->gt('CURRENT_DATE()', 'l.time'))
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb->orderBy('l.time', 'ASC');
    }
}
