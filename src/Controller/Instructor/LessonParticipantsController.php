<?php


namespace App\Controller\Instructor;

use App\Entity\Lesson;
use App\Entity\Member;
use App\Repository\MemberRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_INSTRUCTOR")
 */
class LessonParticipantsController extends AbstractController
{
    /**
     * @Route("/lesson/participants/{id}", name="app_instructor_participants")
     */
    public function participants(Lesson $lesson, MemberRepository $repository)
    {
        $participants = $repository->getMembersFrom($lesson);
        return $this->render('views/instructor/participant/index.html.twig', [
            'title' => 'Participants',
            'members' => $participants,
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/lesson/participants/{id}/add", name="app_instructor_participants_add_overview")
     */
    public function addParticipant(Lesson $lesson, MemberRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $paginator->paginate(
            $repository->getMembersNotIn($lesson, $request->query->get('search')),
            $request->query->getInt('page', 1),
            10
        );
        $participants = $repository->getMembersFrom($lesson);
        return $this->render('views/instructor/participant/index.html.twig', [
            'title' => 'Participants',
            'members' => $participants,
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/lesson/participants/{lesson}/add/{member}", name="app_instructor_participants_add")
     */
    public function addParticipantFunction(Lesson $lesson, Member $member, MemberRepository $repository)
    {
        $participants = $repository->getMembersFrom($lesson);
        return $this->render('views/instructor/participant/index.html.twig', [
            'title' => 'Participants',
            'members' => $participants,
            'lesson' => $lesson,
        ]);
    }
}