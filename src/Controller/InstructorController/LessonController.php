<?php


namespace App\Controller\InstructorController;

use App\Repository\LessonRepository;
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
}