<?php
// src/Controller/BackofficeController.php

namespace App\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackofficeController extends AbstractController
{
    #[Route('/backoffice', name: 'app_backoffice')]
    public function index(): Response
    {
        return $this->render('backoffice/index.html.twig');
    }

    #[Route('/backoffice/analytics', name: 'backoffice_analytics')]
    public function analytics(): Response
    {
        return $this->render('backoffice/analytics.html.twig');
    }

    #[Route('/backoffice/users', name: 'backoffice_users')]
    public function users(): Response
    {
        return $this->render('backoffice/users.html.twig');
    }

    #[Route('/backoffice/settings', name: 'backoffice_settings')]
    public function settings(): Response
    {
        return $this->render('backoffice/settings.html.twig');
    }

    #[Route('/backoffice/about-templatemo', name: 'backoffice_about_templatemo')]
    public function aboutTemplatemo(): Response
    {
        return $this->render('backoffice/about-templatemo.html.twig');
    }

    #[Route('/backoffice/login', name: 'backoffice_login')]
    public function login(): Response
    {
        return $this->render('backoffice/login.html.twig');
    }

    #[Route('/backoffice/register', name: 'backoffice_register')]
    public function register(): Response
    {
        return $this->render('backoffice/register.html.twig');
    }
    #[Route('/backoffice/exercices', name: 'backoffice_exercices')]
    public function listExercices(ExerciceRepository $repo): Response
    {
        $exercices = $repo->findAll();

        return $this->render('backoffice/exercice.html.twig', [
            'exercices' => $exercices,
        ]);
    }
    #[Route('/backoffice/exercice/add', name: 'backoffice_exercice_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $exercice = new Exercice();

        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($exercice);
            $em->flush();

            return $this->redirectToRoute('backoffice_exercices');
        }

        return $this->render('backoffice/exercice_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter Exercice'
        ]);
    }
    #[Route('/backoffice/exercice/edit/{id}', name: 'backoffice_exercice_edit')]
    public function edit(
        int $id,
        ExerciceRepository $repo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $exercice = $repo->find($id);

        if (!$exercice) {
            throw $this->createNotFoundException('Exercice non trouvé');
        }

        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('backoffice_exercices');
        }

        return $this->render('backoffice/exercice_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier Exercice'
        ]);
    }
    #[Route('/backoffice/exercice/delete/{id}', name: 'backoffice_exercice_delete')]
    public function delete(
        int $id,
        ExerciceRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $exercice = $repo->find($id);

        if ($exercice) {
            $em->remove($exercice);
            $em->flush();
        }

        return $this->redirectToRoute('backoffice_exercices');
    }
    #[Route('/backoffice/challenges', name: 'backoffice_challenges')]
    public function showchallenge(ChallengeRepository $repository): Response
    {
        $challenges = $repository->findAll();

        return $this->render('backoffice/challenge.html.twig', [
            'challenges' => $challenges
        ]);
    }
    #[Route('/backoffice/challenge/add', name: 'backoffice_challenge_add')]
    public function addchall(Request $request, EntityManagerInterface $em, Security $security): Response
{
    $challenge = new Challenge();
    $form = $this->createForm(ChallengeType::class, $challenge);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // 🔥 Ici on affecte automatiquement l'utilisateur connecté
        $challenge->setCreatedBy($security->getUser());

        $em->persist($challenge);
        $em->flush();

        return $this->redirectToRoute('backoffice_challenges');
    }

    return $this->render('backoffice/challenge_form.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/backoffice/challenge/edit/{id}', name: 'backoffice_challenge_edit')]
    public function editchal(
        $id,
        ChallengeRepository $repository,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $challenge = $repository->find($id);

        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }

        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('backoffice_challenges');
        }

        return $this->render('backoffice/challenge_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier le Challenge'
        ]);
    }
    #[Route('/backoffice/challenge/delete/{id}', name: 'backoffice_challenge_delete')]
    public function deletechal(
        $id,
        ChallengeRepository $repository,
        EntityManagerInterface $em
    ): Response {

        $challenge = $repository->find($id);

        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }

        $em->remove($challenge);
        $em->flush();

        return $this->redirectToRoute('backoffice_challenges');
    }
}