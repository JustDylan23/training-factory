<?php


namespace App\Controller\Admin;


use App\Entity\Training;
use App\Form\TrainingFormType;
use App\Repository\TrainingRepository;
use App\Services\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/training-courses", name="app_admin_trainings")
     */
    public function index(Request $request, TrainingRepository $repository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');
        $queryBuilder = $repository->getWithSearchQueryBuilder($search);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
        );

        return $this->render('views/admin/training/index.html.twig', [
            'title' => 'Overview training courses',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/training-course/add", name="app_admin_training_add")
     */
    public function addTraining(Request $request, EntityManagerInterface $em, UploaderHelper $uploaderHelper)
    {
        $form = $this->createForm(TrainingFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Training $training */
            $training = $form->getData();

            $uploadedfile = $form['imageFile']->getData();

            if ($uploadedfile) {
                $newFileName = $uploaderHelper->uploadArticleImage($uploadedfile);
                $training->setImg($newFileName);
            }

            $em->persist($training);
            $em->flush();

            $this->addFlash('success', "Added training course $training!");

            return $this->redirectToRoute('app_admin_trainings');
        }

        return $this->render('views/admin/training/form.html.twig', [
            'title' => 'Add training course',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/training-course/edit/{id}", name="app_admin_training_edit")
     */
    public function editTraining(Training $training, Request $request, EntityManagerInterface $em, UploaderHelper $uploaderHelper)
    {
        $form = $this->createForm(TrainingFormType::class, $training);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedfile = $form['imageFile']->getData();

            if ($uploadedfile) {
                $newFileName = $uploaderHelper->uploadArticleImage($uploadedfile);
                $training->setImg($newFileName);
            }

            $em->persist($training);
            $em->flush();

            $this->addFlash('success', "Applied changes to training course {$training->getName()}!");

            return $this->redirectToRoute('app_admin_trainings');
        }

        return $this->render('views/admin/training/form.html.twig', [
            'title' => 'Edit training course',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/training-course/remove/{id}", name="app_admin_training_remove")
     */
    public function removeTraining(Training $training, EntityManagerInterface $em)
    {
        $em->remove($training);
        $em->flush();
        $this->addFlash('success', "Removed training course $training");
        return $this->redirectToRoute('app_admin_trainings');
    }
}