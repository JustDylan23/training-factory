<?php


namespace App\Controller\InstructorController;

use App\Entity\Lesson;
use App\Entity\Member;
use App\Form\LessonFormType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
}