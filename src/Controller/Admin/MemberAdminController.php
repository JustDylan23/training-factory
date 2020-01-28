<?php


namespace App\Controller\Admin;


use App\Entity\Member;
use App\Entity\User;
use App\Form\MemberFormType;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class MemberAdminController extends AbstractController
{
    /**
     * @Route("/members", name="app_admin_members")
     */
    public function index(Request $request, MemberRepository $repository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilder($search);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        return $this->render('views/admin/member/index.html.twig', [
            'title' => 'Overview members',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/member/add", name="app_admin_member_add")
     */
    public function addMember(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(MemberFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Member $member */
            $member = $form->getData();

            $em->persist($member);
            $em->flush();

            $this->addFlash('success', "Added member $member!");

            return $this->redirectToRoute('app_admin_members');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => 'Add member',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/member/edit/{id}", name="app_admin_member_edit")
     */
    public function editMember(Member $member, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(MemberFormType::class, $member, ['isAdmin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', "Applied changes to member $member!");
            return $this->redirectToRoute('app_admin_members');
        }

        return $this->render('views/admin/member/form.html.twig', [
            'title' => 'Edit member',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/member/remove/{id}", name="app_admin_member_remove")
     */
    public function removeMember(Member $member, EntityManagerInterface $em)
    {
        $em->remove($member);
        $em->flush();
        $this->addFlash('success', "Removed member $member!");
        return $this->redirectToRoute('app_admin_members');
    }

    /**
     * @Route("/toggle-user/{id}", name="app_admin_member_toggle_user")
     */
    public function disableUser(User $user, EntityManagerInterface $em)
    {
        $isDisabled = !$user->isDisabled();
        $user->setDisabled($isDisabled);
        $em->flush();
        if ($isDisabled) {
            $this->addFlash('warning', "Disabled user $user");
        } else {
            $this->addFlash('success', "Enabled user $user");
        }
        return $this->redirectToRoute('app_admin_members');
    }
}