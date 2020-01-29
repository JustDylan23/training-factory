<?php

namespace App\Repository;

use App\Entity\Instructor;
use App\Entity\Lesson;
use App\Entity\User;
use DateTimeInterface;
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

    public function getWithSearchQueryBuilder(?string $search, ?DateTimeInterface $startDate, ?Instructor $instructor)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.id, t.name, t.duration, l.location, l.time, i.email, l.maxPeople, COUNT(r_count.lesson) AS relations')
            ->innerJoin('l.training', 't')
            ->innerJoin('l.instructor', 'i')
            ->leftJoin('l.registrations', 'r_count')
            ->groupBy('l.id');
        if ($instructor) {
            $qb->andWhere('i = :id')
                ->setParameter('id', $instructor->getId());
        }
        if ($search) {
            $qb->andWhere($qb->expr()->like('t.name', ':term'))
                ->setParameter('term', '%' . $search . '%');
        }
        if ($startDate) {
            $qb->andWhere($qb->expr()->gt('l.time', ':since'))
                ->setParameter('since', $startDate);
        }
        return $qb->orderBy('l.time', 'ASC');
    }

    public function getWithSearchQueryBuilderAndSignedUp(?string $search, User $member)
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.training', 't')
            ->innerJoin('l.registrations', 'r')
            ->andWhere('r.member = :member')
            ->setParameter('member', $member)
            ->addSelect('t');
        if ($search) {
            $qb->andWhere($qb->expr()->like('t.name', ':term'))
                ->andWhere($qb->expr()->gt('CURRENT_DATE()', 'l.time'))
                ->setParameter('term', '%' . $search . '%');
        }
        return $qb->orderBy('l.time', 'ASC');
    }

    public function getWithSearchQueryBuilderAndNotSignedUp(?string $search, ?DateTimeInterface $since, User $member)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.id, l.location, l.time, l.maxPeople as max_relations')
            ->innerJoin('l.training', 't')
            ->addSelect('t.name, t.duration')
            ->leftJoin('l.registrations', 'r', Join::WITH, 'r.member = :member')
            ->where('r.lesson is null')
            ->setParameter('member', $member)
            ->leftJoin('l.registrations', 'r_count')
            ->addSelect('COUNT(r_count.lesson) AS relations')
            ->groupBy('l.id');
        if ($search) {
            $qb->andWhere($qb->expr()->like('t.name', ':term'))
                ->setParameter('term', '%' . $search . '%');
        }
        if ($since) {
            $qb->andWhere($qb->expr()->gt('l.time', ':since'))
                ->setParameter('since', $since);
        } else {
            $qb->andWhere($qb->expr()->gt('l.time', 'CURRENT_DATE()'));
        }
        return $qb->orderBy('l.time', 'ASC');
    }
}
