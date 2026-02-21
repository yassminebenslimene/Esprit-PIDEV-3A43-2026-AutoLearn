<?php

namespace App\Controller\Backoffice;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Service\QuizManagementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/quiz')]
final class QuizController extends AbstractController
{
    #[Route(name: 'app_quiz_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('backoffice/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/api/{id}/questions', name: 'api_quiz_questions', methods: ['GET'])]
    public function getQuestions(Quiz $quiz): Response
    {
        $questions = $quiz->getQuestions();
        $data = [];
        
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'texte' => $question->getTexteQuestion(),
                'type' => 'Standard',
                'points' => $question->getPoint(),
            ];
        }
        
        return $this->json($data);
    }

    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, QuizManagementService $quizService): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation métier supplémentaire
            $validation = $quizService->validateQuizBusinessRules($quiz);
            
            if (!$validation['valid']) {
                foreach ($validation['errors'] as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->render('backoffice/quiz/new.html.twig', [
                    'quiz' => $quiz,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($quiz);
            $entityManager->flush();

            $this->addFlash('success', '✅ Quiz créé avec succès et lié au chapitre "' . $quiz->getChapitre()->getTitre() . '"');
            return $this->redirectToRoute('backoffice_quiz_management', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('backoffice/quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager, QuizManagementService $quizService): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation métier supplémentaire
            $validation = $quizService->validateQuizBusinessRules($quiz);
            
            if (!$validation['valid']) {
                foreach ($validation['errors'] as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->render('backoffice/quiz/edit.html.twig', [
                    'quiz' => $quiz,
                    'form' => $form,
                ]);
            }

            $entityManager->flush();

            $this->addFlash('success', '✅ Quiz modifié avec succès et lié au chapitre "' . $quiz->getChapitre()->getTitre() . '"');
            return $this->redirectToRoute('backoffice_quiz_management', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_quiz_management', [], Response::HTTP_SEE_OTHER);
    }
}
