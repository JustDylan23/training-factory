<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
     * @Route("/signup", name="app_security_signup")
     */
    public function signup(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardAuthenticatorHandler)
    {
        $form = $this->createForm(RegisterFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setRoles(["ROLE_MEMBER"]);
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account created!');

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request
            );
            return $this->redirectToRoute('app_security_login');
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
    public function account(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(RegisterFormType::class, $this->getUser());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

            $em->flush();

            $this->addFlash('success', 'Changed applied!');

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('views/security/account.html.twig', [
            'title' => $this->getUser()->getEmail(),
            'accountForm' => $form->createView()
        ]);
    }

    /**
     * Function logic is handled elsewhere
     *
     * @Route("/logout", name="app_security_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/migrate")
     * @throws \Exception
     */
    public function migrate(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => 'true',
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        return new Response($content);
    }

    /**
     * @Route("/fixtures")
     * @throws \Exception
     */
    public function fixtures(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => 'true',
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        return new Response($content);
    }
}