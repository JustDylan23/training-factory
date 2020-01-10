<?php


namespace App\Controller\member;

use App\Entity\Lesson;
use App\Entity\Registration;
use App\Repository\LessonRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_MEMBER")
 */
class LessonController extends AbstractController
{
    /**
     * @Route("/signup", name="app_member_signup")
     */
    public function index(Request $request, LessonRepository $repository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilderAndNotSignedUp($search, $this->getUser());
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('views/member/signup.html.twig', [
            'title' => 'Sign up for lessons',
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/member/lesson/signup/{id}", name="app_member_lesson_signup")
     */
    public function signup(Lesson $lesson, EntityManagerInterface $em)
    {
        $registration = new Registration();
        $registration->setLesson($lesson)->setMember($this->getUser());

        $em->persist($registration);
        $em->flush();
        $this->addFlash('success', 'Signed out of lesson');
        return $this->redirectToRoute('app_member_lessons');
    }

    /**
     * @Route("/my-lessons", name="app_member_lessons")
     */
    public function lessons(Request $request, LessonRepository $repository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilderAndSignedUp($search, $this->getUser());
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('views/member/lessons.html.twig', [
            'title' => 'My lessons',
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/member/lesson/signout/{id}", name="app_member_lesson_signout")
     */
    public function signout(Lesson $lesson, EntityManagerInterface $em, RegistrationRepository $repository)
    {
        $registration = $repository->findOneBy(['lesson' => $lesson->getId()]);
        $em->remove($registration);
        $em->flush();
        $this->addFlash('success', 'Signed out of lesson');
        return $this->redirectToRoute('app_member_lessons');
    }
}