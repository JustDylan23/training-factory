<?php

namespace App\Controller\admin;

use App\Entity\Instructor;
use App\Form\InstructorFormType;
use App\Form\ChangePasswordType;
use App\Repository\InstructorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class InstructorAdminController extends AbstractController
{
    /**
     * @Route("/instructors", name="app_admin_instructors")
     */
    public function index(InstructorRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilder($search);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('views/admin/instructor/index.html.twig', [
            'title' => 'Overview instructors',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/instructor/add", name="app_admin_instructor_add")
     */
    public function addInstructor(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(InstructorFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $instructor = $form->getData();

            $em->persist($instructor);
            $em->flush();

            $this->addFlash('success', 'Instructor added!');
            return $this->redirectToRoute('app_admin_instructors');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => 'Add instructor',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/instructor/edit/{id}", name="app_admin_instructor_edit")
     */
    public function editInstructor(Instructor $instructor, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $userForm = $this->createForm(InstructorFormType::class, $instructor);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em->persist($instructor);
            $em->flush();

            $this->addFlash('success', 'Applied changes!');
            return $this->redirectToRoute('app_admin_instructors');
        }

        $userPasswordForm = $this->createForm(ChangePasswordType::class, $instructor);
        $userPasswordForm->handleRequest($request);

        if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
            $instructor->setPassword($passwordEncoder->encodePassword($instructor, $instructor->getPassword()));
            $em->flush();
            $this->addFlash('success', 'Changed applied!');
            return $this->redirectToRoute('app_admin_instructors');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => 'Edit instructor',
            'userForm' => $userForm->createView(),
            'userPasswordForm' => $userPasswordForm->createView()
        ]);
    }

    /**
     * @Route("/instructor/remove/{id}", name="app_admin_instructor_remove", methods={"POST"})
     */
    public function removeInstructor(Instructor $instructor, EntityManagerInterface $em)
    {
        $em->remove($instructor);
        $em->flush();
        $this->addFlash('success', 'Removed successfully');
        return $this->redirectToRoute('app_admin_instructors');
    }
}