<?php

namespace App\Controller\admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InstructorAdminController extends AbstractController
{
    /**
     * @Route("/leden", name="app_admin_instructor")
     */
    public function index()
    {
        return $this->render('views/admin/instructor.html.twig', [
            'title' => 'Instructeurs'
        ]);
    }
}