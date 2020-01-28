<?php

namespace App\Controller\Admin;

use App\Entity\Instructor;
use App\Form\InstructorFormType;
use App\Repository\InstructorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class InstructorAdminController extends AbstractController
{
    /**
     * @Route("/instructors", name="app_admin_instructors")
     */
    public function index(InstructorRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilder($search);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1)
        );
        return $this->render('views/admin/instructor/index.html.twig', [
            'title' => 'Overview instructors',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/instructor/add", name="app_admin_instructor_add")
     */
    public function addInstructor(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(InstructorFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Instructor $instructor */
            $instructor = $form->getData();

            $em->persist($instructor);
            $em->flush();

            $this->addFlash('success', "Added instructor $instructor!");
            return $this->redirectToRoute('app_admin_instructors');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => 'Add instructor',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/instructor/edit/{id}", name="app_admin_instructor_edit")
     */
    public function editInstructor(Instructor $instructor, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(InstructorFormType::class, $instructor, ['isAdmin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', "Applied changes to instructor $instructor!");
            return $this->redirectToRoute('app_admin_instructors');
        }

        return $this->render('views/admin/instructor/form.html.twig', [
            'title' => 'Edit instructor',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/instructor/remove/{id}", name="app_admin_instructor_remove")
     */
    public function removeInstructor(Instructor $instructor, EntityManagerInterface $em)
    {
        $em->remove($instructor);
        $em->flush();
        $this->addFlash('success', "Removed instructor $instructor!");
        return $this->redirectToRoute('app_admin_instructors');
    }

    /**
     * @Route("/instructor/stats/{id}", name="app_admin_instructor_stats")
     *
     * SELECT seq, user.email, COUNT(registration.member_id) AS registrations,
     * CASE WHEN registration.member_id IS NOT NULL
     * THEN SUM(training.costs) ELSE 0 END AS revenue
     * FROM seq_1_to_12
     * INNER JOIN user ON user.id = 22
     * LEFT JOIN lesson ON lesson.instructor_id = user.id AND MONTH(lesson.time) = seq AND YEAR(lesson.time) = 2020
     * LEFT JOIN registration on registration.lesson_id = lesson.id
     * LEFT JOIN training ON training.id = lesson.training_id
     * GROUP BY seq
     */
    public function statistics(Instructor $instructor, EntityManagerInterface $em)
    {
        $result = $em->getConnection()->executeQuery(
            "
            SELECT seq, COUNT(training.id) AS lessons, COUNT(registration.member_id) AS registrations,
            CASE WHEN registration.member_id IS NOT NULL
            THEN SUM(training.costs) ELSE 0 END AS revenue
            FROM seq_1_to_12
            INNER JOIN user ON user.id = 142
            LEFT JOIN lesson ON lesson.instructor_id = user.id AND MONTH(lesson.time) = seq AND YEAR(lesson.time) = 2020
            LEFT JOIN registration on registration.lesson_id = lesson.id
            LEFT JOIN training ON training.id = lesson.training_id
            GROUP BY seq
            "
        )->fetchAll();
        return $this->render('views/admin/instructor/stats.html.twig', [
            'title' => 'Edit instructor',
            'instructor' => $instructor,
            'months' => $result
        ]);
    }
}