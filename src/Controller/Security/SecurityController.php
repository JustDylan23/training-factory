<?php

namespace App\Controller\Security;

use App\Entity\Member;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\MemberFormType;
use App\Form\Model\ChangePassword;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('views/security/login.html.twig', [
            'title' => 'Login',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="app_security_signup")
     */
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $authenticatorHandler,
        LoginFormAuthenticator $authenticator
    )
    {
        $form = $this->createForm(MemberFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Member $user */
            $user = $form->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account created!');
            $this->addFlash('message', "Welcome|Hi there {$this->getUser()->getUsername()}.\nWelcome to Training Centre The Hague");

            return $authenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        return $this->render('views/security/signup.html.twig', [
            'title' => 'Sign up',
            'loginForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/account", name="app_security_account")
     * @IsGranted("ROLE_USER")
     */
    public function account(
        Request $request,
        EntityManagerInterface $em,
        LoginFormAuthenticator $authenticator,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $authenticatorHandler
    )
    {
        /** @var User $user */
        $user = $this->getUser();

        $userForm = $this->createForm($user->getAccountFormType(), $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $em->flush();
            $this->addFlash('success', "Changed applied to account of $user!");
            return $authenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        $changePasswordForm = $this->createForm(ChangePasswordFormType::class);
        $changePasswordForm->handleRequest($request);

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            /** @var ChangePassword $changePassword */
            $changePassword = $changePasswordForm->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $changePassword->getNewPassword()));
            $em->flush();
            $this->addFlash('success', 'Changed password!');
            return $authenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => $this->getUser()->getEmail(),
            'userForm' => $userForm->createView(),
            'userPasswordForm' => $changePasswordForm->createView()
        ]);
    }

    /**
     * Function will never get called, it only
     * exists for its route so Symfony knows
     * how to logout.
     *
     * @Route("/logout", name="app_security_logout")
     */
    public function logout()
    {
    }
}