<?php


namespace App\Controller\Instructor;

use App\Entity\Lesson;
use App\Entity\Member;
use App\Entity\Registration;
use App\Repository\LessonRepository;
use App\Repository\MemberRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     *
     * ! Don't use parameter conversion on the id, for some reason it breaks and after many and many
     * ! hours of debugging I haven't been able to find out why this is so just go with it even if
     * ! this is imperfect
     */
    public function participants($id, LessonRepository $lessonRepository, MemberRepository $memberRepository, Request $request, PaginatorInterface $paginator)
    {
        $lesson = $lessonRepository->findOneBy(['id' => $id]);
        if (!$lesson) {
            throw $this->createNotFoundException();
        }
        $search = $request->query->get('search');
        $pagination = $paginator->paginate(
            $memberRepository->getQueryBuilderMembersInLesson($lesson, $search),
            $request->query->getInt('page', 1)
        );

        return $this->render('views/instructor/participant/index.html.twig', [
            'title' => 'Participants',
            'pagination' => $pagination,
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/lesson/participants/{id}/select", name="app_instructor_participants_select")
     */
    public function selectParticipant($id, LessonRepository $lessonRepository, MemberRepository $memberRepository, Request $request, PaginatorInterface $paginator)
    {
        $lesson = $lessonRepository->findOneBy(['id' => $id]);
        if (!$lesson) {
            throw $this->createNotFoundException();
        }
        $pagination = $paginator->paginate(
            $memberRepository->getQueryBuilderMembersNotInLesson($lesson, $request->query->get('search')),
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
    public function addParticipant($lesson, LessonRepository $lessonRepository, Member $member, EntityManagerInterface $em)
    {
        $lesson = $lessonRepository->findOneBy(['id' => $lesson]);
        if (!$lesson) {
            throw $this->createNotFoundException();
        }
        $registration = new Registration();
        $registration->setLesson($lesson);
        $registration->setMember($member);

        $em->persist($registration);
        $em->flush();

        $this->addFlash('success', "Signed $member up for lesson of type $lesson");

        return $this->redirectToRoute('app_instructor_participants', ['id' => $lesson->getId()]);
    }

    /**
     * @Route("/lesson/participants/{lesson}/remove/{member}", name="app_instructor_participants_remove")
     */
    public function remove($lesson, LessonRepository $lessonRepository, RegistrationRepository $registrationRepository, Member $member, EntityManagerInterface $em)
    {
        $lesson = $lessonRepository->findOneBy(['id' => $lesson]);
        if (!$lesson) {
            throw $this->createNotFoundException();
        }
        $registration = $registrationRepository->findOneBy(['lesson' => $lesson->getId(), 'member' =>  $member->getId()]);
        $em->remove($registration);
        $em->flush();

        $this->addFlash('success', "Removed $member from lesson of type $lesson");
        return $this->redirectToRoute('app_instructor_participants', ['id' => $lesson->getId()]);
    }
}