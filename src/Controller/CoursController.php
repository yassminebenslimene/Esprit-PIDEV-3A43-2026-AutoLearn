<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Chapitre;
use App\Entity\Quiz;
use App\Form\CoursType;
use App\Form\ChapitreType;
use App\Form\QuizType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cours')]
class CoursController extends AbstractController
{
    #[Route('', name: 'app_cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('backoffice/cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    // ==================== GESTION DES CHAPITRES ====================
    
    #[Route('/{id}/chapitres', name: 'app_cours_chapitres', methods: ['GET'])]
    public function chapitres(Cours $cours): Response
    {
        // Récupérer les chapitres triés par ordre
        $chapitres = $cours->getChapitres()->toArray();
        usort($chapitres, function($a, $b) {
            return $a->getOrdre() <=> $b->getOrdre();
        });

        return $this->render('backoffice/cours/chapitres.html.twig', [
            'cours' => $cours,
            'chapitres' => $chapitres,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('backoffice/cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/chapitres/new', name: 'app_cours_chapitre_new', methods: ['GET', 'POST'])]
    public function newChapitre(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        $chapitre = new Chapitre();
        $chapitre->setCours($cours);
        
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapitre);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_chapitres', ['id' => $cours->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/cours/chapitre_new.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'form' => $form,
        ]);
    }

    #[Route('/{coursId}/chapitres/{id}', name: 'app_cours_chapitre_show', methods: ['GET'])]
    public function showChapitre(Cours $cours, Chapitre $chapitre): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        return $this->render('backoffice/cours/chapitre_show.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
        ]);
    }

    #[Route('/{coursId}/chapitres/{id}/edit', name: 'app_cours_chapitre_edit', methods: ['GET', 'POST'])]
    public function editChapitre(Request $request, Cours $cours, Chapitre $chapitre, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_chapitres', ['id' => $cours->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/cours/chapitre_edit.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'form' => $form,
        ]);
    }

    #[Route('/{coursId}/chapitres/{id}/delete', name: 'app_cours_chapitre_delete', methods: ['POST'])]
    public function deleteChapitre(Request $request, Cours $cours, Chapitre $chapitre, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        if ($this->isCsrfTokenValid('delete'.$chapitre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chapitre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_chapitres', ['id' => $cours->getId()], Response::HTTP_SEE_OTHER);
    }

    // ==================== GESTION DES QUIZ ====================
    
    #[Route('/{coursId}/chapitres/{chapitreId}/quizzes', name: 'app_cours_chapitre_quizzes', methods: ['GET'])]
    public function quizzes(
        #[MapEntity(id: 'coursId')] Cours $cours,
        #[MapEntity(id: 'chapitreId')] Chapitre $chapitre
    ): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        return $this->render('backoffice/cours/quizzes.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'quizzes' => $chapitre->getQuizzes(),
        ]);
    }

    #[Route('/{coursId}/chapitres/{chapitreId}/quizzes/new', name: 'app_cours_chapitre_quiz_new', methods: ['GET', 'POST'])]
    public function newQuiz(
        Request $request,
        #[MapEntity(id: 'coursId')] Cours $cours,
        #[MapEntity(id: 'chapitreId')] Chapitre $chapitre,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        $quiz = new Quiz();
        $quiz->setChapitre($chapitre);
        
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($quiz);
                $entityManager->flush();

                $this->addFlash('success', 'Le quiz a été créé avec succès.');

                return $this->redirectToRoute('app_cours_chapitre_quizzes', [
                    'coursId' => $cours->getId(),
                    'chapitreId' => $chapitre->getId()
                ], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez vérifier les champs.');
            }
        }

        return $this->render('backoffice/cours/quiz_new.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{coursId}/chapitres/{chapitreId}/quizzes/{id}', name: 'app_cours_chapitre_quiz_show', methods: ['GET'])]
    public function showQuiz(
        #[MapEntity(id: 'coursId')] Cours $cours,
        #[MapEntity(id: 'chapitreId')] Chapitre $chapitre,
        Quiz $quiz
    ): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        // Vérifier que le quiz appartient bien au chapitre
        if ($quiz->getChapitre() !== $chapitre) {
            throw $this->createNotFoundException('Ce quiz n\'appartient pas à ce chapitre.');
        }

        return $this->render('backoffice/cours/quiz_show.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{coursId}/chapitres/{chapitreId}/quizzes/{id}/edit', name: 'app_cours_chapitre_quiz_edit', methods: ['GET', 'POST'])]
    public function editQuiz(
        Request $request,
        #[MapEntity(id: 'coursId')] Cours $cours,
        #[MapEntity(id: 'chapitreId')] Chapitre $chapitre,
        Quiz $quiz,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        // Vérifier que le quiz appartient bien au chapitre
        if ($quiz->getChapitre() !== $chapitre) {
            throw $this->createNotFoundException('Ce quiz n\'appartient pas à ce chapitre.');
        }

        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_chapitre_quizzes', [
                'coursId' => $cours->getId(),
                'chapitreId' => $chapitre->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/cours/quiz_edit.html.twig', [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{coursId}/chapitres/{chapitreId}/quizzes/{id}/delete', name: 'app_cours_chapitre_quiz_delete', methods: ['POST'])]
    public function deleteQuiz(
        Request $request,
        #[MapEntity(id: 'coursId')] Cours $cours,
        #[MapEntity(id: 'chapitreId')] Chapitre $chapitre,
        Quiz $quiz,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Vérifier que le chapitre appartient bien au cours
        if ($chapitre->getCours() !== $cours) {
            throw $this->createNotFoundException('Ce chapitre n\'appartient pas à ce cours.');
        }

        // Vérifier que le quiz appartient bien au chapitre
        if ($quiz->getChapitre() !== $chapitre) {
            throw $this->createNotFoundException('Ce quiz n\'appartient pas à ce chapitre.');
        }

        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_chapitre_quizzes', [
            'coursId' => $cours->getId(),
            'chapitreId' => $chapitre->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}