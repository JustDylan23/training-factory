<?php


namespace App\Controller\instructor;

use App\Entity\Lesson;
use App\Entity\Member;
use App\Form\LessonFormType;
use App\Form\MemberFormType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_INSTRUCTOR")
 */
class LessonController extends AbstractController
{

    /**
     * @Route("/lessons", name="app_instructor_lessons")
     */
    public function index(Request $request, LessonRepository $repository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilder($search);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('views/instructor/lesson/index.html.twig', [
            'title' => 'Lessons',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/lesson/add", name="app_instructor_lesson_add")
     */
    public function addLesson(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(LessonFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Lesson $lesson */
            $lesson = $form->getData();

            $lesson->setInstructor($this->getUser());

            $em->persist($lesson);
            $em->flush();

            $this->addFlash('success', 'Lesson planned!');

            return $this->redirectToRoute('app_instructor_lessons');
        }

        return $this->render('views/instructor/lesson/form.html.twig', [
            'title' => 'Plan lesson',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/lesson/edit/{id}", name="app_admin_lesson_edit")
     */
    public function editMember(Lesson $lesson, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(LessonFormType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lesson);
            $em->flush();

            $this->addFlash('success', 'Applied changes!');
            return $this->redirectToRoute('app_instructor_lessons');
        }

        return $this->render('views/admin/member/form.html.twig', [
            'title' => 'Edit lesson',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/lesson/remove/{id}", name="app_admin_lesson_remove")
     */
    public function removeLesson(Lesson $lesson, EntityManagerInterface $em)
    {
        $em->remove($lesson);
        $em->flush();
        $this->addFlash('success', 'Removed successfully');
        return $this->redirectToRoute('app_instructor_lessons');
    }
}