<?php


namespace App\Controller\Member;

use App\Entity\Lesson;
use App\Entity\Registration;
use App\Repository\LessonRepository;
use App\Repository\RegistrationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class LessonController extends AbstractController
{
    /**
     * @Route("/signup", name="app_member_signup")
     */
    public function availableLessons(Request $request, LessonRepository $repository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $startDate = $request->query->get('startDate');
        if ($startDate) {
            $startDate = DateTime::createFromFormat('Y-m-d\\TH:i', $startDate);
            if (!$startDate) $startDate = null;
        }
        $queryBuilder = $repository->getWithSearchQueryBuilderAndNotSignedUp($search, $startDate, $this->getUser());
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
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
        $this->addFlash('success', "Signed up for lesson of type $lesson");
        return $this->redirectToRoute('app_member_signup');
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
        );

        return $this->render('views/member/lesson.html.twig', [
            'title' => 'My lessons',
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/member/lesson/signout/{id}", name="app_member_lesson_signout")
     */
    public function signout(Lesson $lesson, EntityManagerInterface $em, RegistrationRepository $repository)
    {
        $registration = $repository->findOneBy(['lesson' => $lesson->getId(), 'member' => $this->getUser()->getId()]);
        $em->remove($registration);
        $em->flush();
        $this->addFlash('success', "Signed out of lesson of type $lesson!");
        return $this->redirectToRoute('app_member_lessons');
    }
}