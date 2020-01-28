<?php


namespace App\Controller\Instructor;

use App\Entity\Lesson;
use App\Entity\Member;
use App\Entity\Registration;
use App\Repository\MemberRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_INSTRUCTOR")
 */
class LessonParticipantsController extends AbstractController
{
    /**
     * @Route("/lesson/participants/{id}", name="app_instructor_participants")
     */
    public function participants(Lesson $lesson, MemberRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        dd($this->getUser());
        $pagination = $paginator->paginate(
            $repository->getWithSearchQueryBuilder(null),
            $request->query->getInt('page', 1),
        );

        return $this->render('views/instructor/participant/index.html.twig', [
            'title' => 'Participants',
            'pagination' => $pagination,
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/lesson/participants/{id}/add", name="app_instructor_participants_add_overview")
     */
    public function addParticipant(Lesson $lesson, MemberRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $repository->getQueryBuilderMembersNotInLessonWithSearch($lesson, $request->query->get('search')),
            $request->query->getInt('page', 1),
        );
        return $this->render('views/instructor/participant/add.html.twig', [
            'title' => 'Add participants',
            'pagination' => $pagination,
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/lesson/participants/{lesson}/add/{member}", name="app_instructor_participants_add")
     */
    public function addParticipantFunction(Lesson $lesson, Member $member, EntityManagerInterface $em)
    {
        $registration = new Registration();
        $registration->setLesson($lesson);
        $registration->setMember($member);

        $em->persist($registration);
        $em->flush();

        $this->addFlash('success', "Signed $member up for lesson of type $lesson");

        return $this->redirectToRoute('app_instructor_participants', ['id' => $lesson->getId()]);
    }
}