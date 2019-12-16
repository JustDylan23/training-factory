<?php


namespace App\Controller\admin;


use App\Entity\Training;
use App\Form\TrainingFormType;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class TrainingAdminController extends AbstractController
{
    /**
     * @Route("/training", name="app_admin_training")
     */
    public function trainings(TrainingRepository $trainingRepository)
    {
        return $this->render('views/admin/training/training_index.html.twig', [
            'title' => 'Trainingen bewerken',
            'trainings' => $trainingRepository->findAll()
        ]);
    }

    /**
     * @Route("/training/add/", name="app_admin_training_add")
     */
    public function addTraining(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TrainingFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Training $training */
            $training = $form->getData();



            $em->persist($training);
            $em->flush();

            $this->addFlash('success', 'Training form added!');

            return $this->redirectToRoute('app_admin_training');
        }

        return $this->render('views/admin/training/training_add.html.twig', [
            'title' => 'Trainingen bewerken',
            'trainingForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/training/edit/{id}", name="app_admin_training_edit")
     */
    public function editTraining(Training $training, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TrainingFormType::class, $training);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($training);
            $em->flush();

            $this->addFlash('success', 'Changed applied!');

            return $this->redirectToRoute('app_admin_training');
        }

        return $this->render('views/admin/training/training_edit.html.twig', [
            'title' => 'Trainingen bewerken',
            'trainingForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/training/remove/{id}", name="app_admin_training_remove", methods={"POST"})
     */
    public function removeTraining(Training $training, EntityManagerInterface $em)
    {
        try {
            $em->remove($training);
            $em->flush();
            return $this->json(['success' => true]);
        } catch (Exception $e) {
            return $this->json(['success' => false]);
        }
    }
}