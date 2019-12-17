<?php

namespace App\Controller;

use App\Repository\TrainingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(TrainingRepository $trainingRepository)
    {
        $results = $trainingRepository->getFeaturedTrainings();

        return $this->render('views/public/index.html.twig', [
            'title' => 'Training Center The Hague',
            'trainings' => $results
        ]);
    }

    /**
     * @Route("/lessons", name="app_public_trainings")
     */
    public function trainings(TrainingRepository $trainingRepository)
    {
        $results = $trainingRepository->findAll();

        return $this->render('views/public/trainings.html.twig', [
            'title' => 'Lessons',
            'trainings' => $results
        ]);
    }


    /**
     * @Route("/rules", name="app_public_rules")
     * @return Response
     */
    public function rules()
    {
        return $this->render('views/public/rules.html.twig', [
            'title' => 'Behaviour rules',
        ]);
    }

    /**
     * @Route("/contact", name="app_public_contact")
     */
    public function contact()
    {
        return $this->render('views/public/contact.html.twig', [
            'title' => 'Contact us',
        ]);
    }
}