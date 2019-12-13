<?php


namespace App\Controller\admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserAdminController extends AbstractController
{
    /**
     * @Route("/instructor", name="app_admin_member")
     */
    public function index()
    {
        return $this->render('views/admin/user.html.twig', [
            'title' => 'Gebruikers'
        ]);
    }
}