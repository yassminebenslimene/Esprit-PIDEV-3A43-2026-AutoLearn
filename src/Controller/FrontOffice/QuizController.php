<?php

namespace App\Controller\FrontOffice;

use App\Entity\Chapitre;
use App\Entity\Quiz;
use App\Repository\ChapitreRepository;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chapitre/{chapitreId}/quiz', name: 'app_frontoffice_quiz_')]
class QuizController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(int $chapitreId, ChapitreRepository $chapitreRepository, QuizRepository $quizRepository): Response
    {
        $chapitre = $chapitreRepository->find($chapitreId);
        
        if (!$chapitre) {
            throw $this->createNotFoundException('Chapitre non trouvé');
        }

        // Récupérer tous les quiz actifs pour ce chapitre
        $quizzes = $quizRepository->createQueryBuilder('q')
            ->where('q.chapitre = :chapitre')
            ->andWhere('q.etat = :etat')
            ->setParameter('chapitre', $chapitre)
            ->setParameter('etat', 'actif')
            ->getQuery()
            ->getResult();

        return $this->render('frontoffice/quiz/list.html.twig', [
            'chapitre' => $chapitre,
            'quizzes' => $quizzes,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $chapitreId, int $id, ChapitreRepository $chapitreRepository, QuizRepository $quizRepository): Response
    {
        $chapitre = $chapitreRepository->find($chapitreId);
        $quiz = $quizRepository->find($id);
        
        if (!$chapitre || !$quiz) {
            throw $this->createNotFoundException('Chapitre ou Quiz non trouvé');
        }

        // Vérifier que le quiz appartient bien au chapitre
        if ($quiz->getChapitre() !== $chapitre) {
            throw $this->createNotFoundException('Ce quiz n\'appartient pas à ce chapitre');
        }

        return $this->render('frontoffice/quiz/show.html.twig', [
            'chapitre' => $chapitre,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}/submit', name: 'submit', methods: ['POST'])]
    public function submit(int $chapitreId, int $id, Request $request, QuizRepository $quizRepository): Response
    {
        $quiz = $quizRepository->find($id);
        
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        $answers = $request->request->all('answers');
        $score = 0;
        $totalPoints = 0;
        $results = [];

        foreach ($quiz->getQuestions() as $question) {
            $totalPoints += $question->getPoint();
            $questionId = $question->getId();
            $selectedOptionId = $answers[$questionId] ?? null;

            $isCorrect = false;
            $correctOption = null;

            foreach ($question->getOptions() as $option) {
                if ($option->isEstCorrecte()) {
                    $correctOption = $option;
                    if ($selectedOptionId == $option->getId()) {
                        $isCorrect = true;
                        $score += $question->getPoint();
                    }
                }
            }

            $results[$questionId] = [
                'question' => $question,
                'selectedOptionId' => $selectedOptionId,
                'correctOption' => $correctOption,
                'isCorrect' => $isCorrect,
            ];
        }

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;

        return $this->render('frontoffice/quiz/result.html.twig', [
            'quiz' => $quiz,
            'score' => $score,
            'totalPoints' => $totalPoints,
            'percentage' => $percentage,
            'results' => $results,
            'chapitreId' => $chapitreId,
        ]);
    }
}
