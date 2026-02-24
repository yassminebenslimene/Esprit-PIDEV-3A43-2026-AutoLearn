<?php

namespace App\Controller\Backoffice;

use App\Entity\Quiz;
use App\Entity\GestionDeCours\Chapitre;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Repository\Cours\ChapitreRepository;
use App\Service\QuizManagementService;
use App\Service\GrokQuizGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        if ($form->isSubmitted()) {
            // Debug: vérifier si le formulaire est valide
            if (!$form->isValid()) {
                $this->addFlash('error', '❌ Le formulaire contient des erreurs. Veuillez vérifier les champs.');
                
                // Afficher les erreurs du formulaire
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            } else {
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

    /**
     * Sélection du chapitre pour génération automatique de quiz
     */
    #[Route('/generate/select-chapitre', name: 'app_quiz_generate_select_chapitre', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function selectChapitre(ChapitreRepository $chapitreRepository): Response
    {
        $chapitres = $chapitreRepository->findAll();

        return $this->render('backoffice/quiz/select_chapitre.html.twig', [
            'chapitres' => $chapitres,
        ]);
    }

    /**
     * Génération automatique d'un quiz via IA Groq
     */
    #[Route('/generate/chapitre/{id}', name: 'app_quiz_generate_from_chapitre', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function generateFromChapitre(
        Request $request,
        Chapitre $chapitre,
        GrokQuizGeneratorService $grokService
    ): Response {
        if ($request->isMethod('POST')) {
            // Vérification du token CSRF
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('generate_quiz_' . $chapitre->getId(), $token)) {
                $this->addFlash('error', '❌ Token CSRF invalide. Veuillez réessayer.');
                return $this->render('backoffice/quiz/generate.html.twig', [
                    'chapitre' => $chapitre,
                ]);
            }

            try {
                $nombreQuestions = (int) $request->request->get('nombre_questions', 5);
                $difficulte = $request->request->get('difficulte', 'moyen');
                
                $options = [
                    'titre' => $request->request->get('titre', 'Quiz - ' . $chapitre->getTitre()),
                    'description' => $request->request->get('description', 'Quiz généré automatiquement par IA'),
                    'difficulte' => $difficulte,
                    'seuil_reussite' => (int) $request->request->get('seuil_reussite', 60),
                    'max_tentatives' => $request->request->get('max_tentatives') ? (int) $request->request->get('max_tentatives') : null,
                    'duree_max' => $request->request->get('duree_max') ? (int) $request->request->get('duree_max') : null,
                    'etat' => 'brouillon', // Toujours en brouillon pour révision
                ];

                $quiz = $grokService->genererQuizPourChapitre($chapitre, $nombreQuestions, $options);

                $this->addFlash('success', sprintf(
                    '🤖 Quiz généré avec succès ! %d questions créées. Le quiz est en mode brouillon pour révision.',
                    $quiz->getQuestions()->count()
                ));

                return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);

            } catch (\Exception $e) {
                $this->addFlash('error', '❌ Erreur lors de la génération: ' . $e->getMessage());
                return $this->render('backoffice/quiz/generate.html.twig', [
                    'chapitre' => $chapitre,
                ]);
            }
        }

        return $this->render('backoffice/quiz/generate.html.twig', [
            'chapitre' => $chapitre,
        ]);
    }

    /**
     * Régénération des questions d'un quiz existant
     */
    #[Route('/{id}/regenerate', name: 'app_quiz_regenerate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function regenerate(
        Request $request,
        Quiz $quiz,
        GrokQuizGeneratorService $grokService
    ): Response {
        if (!$this->isCsrfTokenValid('regenerate'.$quiz->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', '❌ Token CSRF invalide');
            return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);
        }

        try {
            $nombreQuestions = (int) $request->request->get('nombre_questions', 5);
            $difficulte = $request->request->get('difficulte', 'moyen');
            
            $options = [
                'difficulte' => $difficulte,
            ];

            $grokService->regenererQuestions($quiz, $nombreQuestions, $options);

            $this->addFlash('success', sprintf(
                '🔄 Questions régénérées avec succès ! %d nouvelles questions créées.',
                $quiz->getQuestions()->count()
            ));

        } catch (\Exception $e) {
            $this->addFlash('error', '❌ Erreur lors de la régénération: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);
    }
}
