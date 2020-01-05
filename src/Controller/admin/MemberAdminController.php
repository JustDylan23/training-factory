<?php


namespace App\Controller\admin;


use App\Entity\Member;
use App\Form\MemberFormType;
use App\Form\UserPasswordFormType;
use App\Repository\MemberRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
            10
        );

        return $this->render('views/admin/member/member_index.html.twig', [
            'title' => 'Overview members',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/member/add", name="app_admin_member_add")
     */
    public function addMember(Request $request, EntityManagerInterface $em, UploaderHelper $uploaderHelper)
    {
        $form = $this->createForm(MemberFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Member $member */
            $member = $form->getData();

            $em->persist($member);
            $em->flush();

            $this->addFlash('success', 'Member added!');

            return $this->redirectToRoute('app_admin_members');
        }

        return $this->render('views/admin/member/member_form.html.twig', [
            'title' => 'Add member',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/member/edit/{id}", name="app_admin_member_edit")
     */
    public function editMember(Member $member, Request $request, EntityManagerInterface $em, LoginFormAuthenticator $authenticator, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $authenticatorHandler)
    {
        $userForm = $this->createForm(MemberFormType::class, $member);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em->persist($member);
            $em->flush();

            $this->addFlash('success', 'Applied changes!');
            return $this->redirectToRoute('app_admin_members');
        }

        $userPasswordForm = $this->createForm(UserPasswordFormType::class, $member);
        $userPasswordForm->handleRequest($request);

        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            $member->setPassword($passwordEncoder->encodePassword($member, $member->getPassword()));
            $em->flush();
            $this->addFlash('success', 'Changed applied!');
            return $this->redirectToRoute('app_admin_members');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => 'Edit member',
            'userForm' => $userForm->createView(),
            'userPasswordForm' => $userPasswordForm->createView()
        ]);
    }

    /**
     * @Route("/member/remove/{id}", name="app_admin_member_remove", methods={"POST"})
     */
    public function removeMember(Member $member, EntityManagerInterface $em)
    {
        try {
            $em->remove($member);
            $em->flush();
            return $this->json(['success' => true]);
        } catch (Exception $e) {
            return $this->json(['success' => false]);
        }
    }
}