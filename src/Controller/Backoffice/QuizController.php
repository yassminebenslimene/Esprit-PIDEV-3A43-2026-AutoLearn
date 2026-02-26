<?php
// Déclaration du fichier PHP

// Définition du namespace pour organiser les classes du backoffice
namespace App\Controller\Backoffice;

// Import de l'entité Quiz pour manipuler les quiz
use App\Entity\Quiz;
// Import de l'entité Chapitre pour lier les quiz aux chapitres
use App\Entity\GestionDeCours\Chapitre;
// Import du formulaire QuizType pour créer/éditer les quiz
use App\Form\QuizType;
// Import du repository pour accéder aux quiz en base de données
use App\Repository\QuizRepository;
// Import du repository pour accéder aux chapitres en base de données
use App\Repository\Cours\ChapitreRepository;
// Import du service de gestion métier des quiz
use App\Service\QuizManagementService;
// Import du service de génération automatique de quiz par IA
use App\Service\GrokQuizGeneratorService;
// Import de l'EntityManager pour persister les données en base
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

// Définit le préfixe de route '/quiz' pour toutes les méthodes de ce contrôleur
#[Route('/quiz')]
// Classe finale (ne peut pas être étendue) pour le contrôleur de gestion des quiz
final class QuizController extends AbstractController
{
    // Route pour afficher la liste de tous les quiz (méthode GET uniquement)
    #[Route(name: 'app_quiz_index', methods: ['GET'])]
    // Méthode pour afficher l'index des quiz
    public function index(QuizRepository $quizRepository): Response
    {
        // Retourne la vue Twig avec tous les quiz récupérés depuis la base de données
        return $this->render('backoffice/quiz/index.html.twig', [
            // Récupère tous les quiz via le repository et les passe à la vue
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    // Route API pour récupérer les questions d'un quiz en JSON (méthode GET)
    #[Route('/api/{id}/questions', name: 'api_quiz_questions', methods: ['GET'])]
    // Méthode pour retourner les questions d'un quiz au format JSON
    public function getQuestions(Quiz $quiz): Response
    {
        // Récupère la collection de toutes les questions du quiz
        $questions = $quiz->getQuestions();
        // Initialise un tableau vide pour stocker les données formatées
        $data = [];
        
        // Parcourt chaque question du quiz
        foreach ($questions as $question) {
            // Ajoute les informations de la question dans le tableau de données
            $data[] = [
                // Identifiant unique de la question
                'id' => $question->getId(),
                // Texte de la question
                'texte' => $question->getTexteQuestion(),
                // Type de question (toujours 'Standard' pour l'instant)
                'type' => 'Standard',
                // Nombre de points attribués à cette question
                'points' => $question->getPoint(),
            ];
        }
        
        // Retourne les données au format JSON pour l'API
        return $this->json($data);
    }

    // Route pour créer un nouveau quiz (accepte GET pour afficher le formulaire et POST pour le soumettre)
    #[Route('/new', name: 'app_quiz_new', methods: ['GET', 'POST'])]
    // Méthode pour créer un nouveau quiz
    public function new(Request $request, EntityManagerInterface $entityManager, QuizManagementService $quizService): Response
    {
        // Crée une nouvelle instance vide de Quiz
        $quiz = new Quiz();
        // Crée le formulaire basé sur QuizType et lie l'objet $quiz
        $form = $this->createForm(QuizType::class, $quiz);
        // Traite la requête HTTP et remplit le formulaire avec les données soumises
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis ET si toutes les validations sont passées
        if ($form->isSubmitted() && $form->isValid()) {
            // Applique les règles métier supplémentaires via le service
            $validation = $quizService->validateQuizBusinessRules($quiz);
            
            // Si la validation métier échoue
            if (!$validation['valid']) {
                // Parcourt chaque erreur de validation
                foreach ($validation['errors'] as $error) {
                    // Ajoute un message flash d'erreur pour l'utilisateur
                    $this->addFlash('error', $error);
                }
                // Réaffiche le formulaire avec les erreurs
                return $this->render('backoffice/quiz/new.html.twig', [
                    // Passe le quiz au template
                    'quiz' => $quiz,
                    // Passe le formulaire au template
                    'form' => $form,
                ]);
            }

            // Prépare l'objet quiz pour être sauvegardé en base de données
            $entityManager->persist($quiz);
            // Exécute réellement l'insertion en base de données
            $entityManager->flush();

            // Ajoute un message de succès avec le nom du chapitre lié
            $this->addFlash('success', '✅ Quiz créé avec succès et lié au chapitre "' . $quiz->getChapitre()->getTitre() . '"');
            // Redirige vers la page de gestion des quiz avec code HTTP 303
            return $this->redirectToRoute('backoffice_quiz_management', [], Response::HTTP_SEE_OTHER);
        }

        // Si le formulaire n'est pas soumis ou invalide, affiche le formulaire vide
        return $this->render('backoffice/quiz/new.html.twig', [
            // Passe le quiz vide au template
            'quiz' => $quiz,
            // Passe le formulaire au template
            'form' => $form,
        ]);
    }

    // Route pour afficher les détails d'un quiz spécifique (méthode GET uniquement)
    #[Route('/{id}', name: 'app_quiz_show', methods: ['GET'])]
    // Méthode pour afficher un quiz (ParamConverter charge automatiquement le quiz depuis l'ID)
    public function show(Quiz $quiz): Response
    {
        // Retourne la vue de détail du quiz
        return $this->render('backoffice/quiz/show.html.twig', [
            // Passe l'objet quiz complet au template
            'quiz' => $quiz,
        ]);
    }

    // Route pour éditer un quiz existant (GET pour afficher, POST pour soumettre)
    #[Route('/{id}/edit', name: 'app_quiz_edit', methods: ['GET', 'POST'])]
    // Méthode pour éditer un quiz existant (ParamConverter charge le quiz depuis l'ID)
    public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager, QuizManagementService $quizService): Response
    {
        // Crée le formulaire pré-rempli avec les données du quiz existant
        $form = $this->createForm(QuizType::class, $quiz);
        // Traite la requête et met à jour l'objet quiz avec les nouvelles données
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis (POST)
        if ($form->isSubmitted()) {
            // Vérifie si le formulaire est valide selon les contraintes de validation
            if (!$form->isValid()) {
                // Ajoute un message d'erreur général
                $this->addFlash('error', '❌ Le formulaire contient des erreurs. Veuillez vérifier les champs.');
                
                // Parcourt toutes les erreurs du formulaire (y compris les erreurs imbriquées)
                foreach ($form->getErrors(true) as $error) {
                    // Affiche chaque message d'erreur spécifique
                    $this->addFlash('error', $error->getMessage());
                }
            } else {
                // Le formulaire est valide, on applique les règles métier supplémentaires
                $validation = $quizService->validateQuizBusinessRules($quiz);
                
                // Si les règles métier ne sont pas respectées
                if (!$validation['valid']) {
                    // Affiche chaque erreur métier
                    foreach ($validation['errors'] as $error) {
                        $this->addFlash('error', $error);
                    }
                    // Réaffiche le formulaire avec les erreurs
                    return $this->render('backoffice/quiz/edit.html.twig', [
                        'quiz' => $quiz,
                        'form' => $form,
                    ]);
                }

                // Sauvegarde les modifications en base de données (pas besoin de persist car l'objet existe déjà)
                $entityManager->flush();

                // Affiche un message de succès avec le nom du chapitre
                $this->addFlash('success', '✅ Quiz modifié avec succès et lié au chapitre "' . $quiz->getChapitre()->getTitre() . '"');
                // Redirige vers la page de gestion des quiz
                return $this->redirectToRoute('backoffice_quiz_management', [], Response::HTTP_SEE_OTHER);
            }
        }

        // Affiche le formulaire d'édition (GET ou POST invalide)
        return $this->render('backoffice/quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    // Route pour supprimer un quiz (POST uniquement pour la sécurité)
    #[Route('/{id}', name: 'app_quiz_delete', methods: ['POST'])]
    // Méthode pour supprimer un quiz
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que le token CSRF est valide pour éviter les attaques CSRF
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->getPayload()->getString('_token'))) {
            // Marque le quiz pour suppression (supprime aussi les questions grâce à orphanRemoval)
            $entityManager->remove($quiz);
            // Exécute la suppression en base de données
            $entityManager->flush();
        }

        // Redirige vers la page de gestion des quiz
        return $this->redirectToRoute('backoffice_quiz_management', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Sélection du chapitre pour génération automatique de quiz
     */
    // Route pour sélectionner un chapitre avant de générer un quiz par IA
    #[Route('/generate/select-chapitre', name: 'app_quiz_generate_select_chapitre', methods: ['GET'])]
    // Restreint l'accès aux administrateurs uniquement
    #[IsGranted('ROLE_ADMIN')]
    // Méthode pour afficher la liste des chapitres disponibles
    public function selectChapitre(ChapitreRepository $chapitreRepository): Response
    {
        // Récupère tous les chapitres depuis la base de données
        $chapitres = $chapitreRepository->findAll();

        // Affiche la vue de sélection de chapitre
        return $this->render('backoffice/quiz/select_chapitre.html.twig', [
            // Passe la liste des chapitres au template
            'chapitres' => $chapitres,
        ]);
    }

    /**
     * Génération automatique d'un quiz via IA Groq
     */
    // Route pour générer un quiz automatiquement à partir d'un chapitre
    #[Route('/generate/chapitre/{id}', name: 'app_quiz_generate_from_chapitre', methods: ['GET', 'POST'])]
    // Restreint l'accès aux administrateurs uniquement
    #[IsGranted('ROLE_ADMIN')]
    // Méthode pour générer un quiz via l'IA Groq
    public function generateFromChapitre(
        Request $request,
        Chapitre $chapitre,
        GrokQuizGeneratorService $grokService
    ): Response {
        // Vérifie si la requête est de type POST (soumission du formulaire)
        if ($request->isMethod('POST')) {
            // Récupère le token CSRF depuis la requête
            $token = $request->request->get('_token');
            // Vérifie que le token CSRF est valide pour éviter les attaques
            if (!$this->isCsrfTokenValid('generate_quiz_' . $chapitre->getId(), $token)) {
                // Affiche un message d'erreur si le token est invalide
                $this->addFlash('error', '❌ Token CSRF invalide. Veuillez réessayer.');
                // Réaffiche le formulaire de génération
                return $this->render('backoffice/quiz/generate.html.twig', [
                    'chapitre' => $chapitre,
                ]);
            }

            // Bloc try-catch pour gérer les erreurs de génération
            try {
                // Récupère le nombre de questions demandé (défaut: 5)
                $nombreQuestions = (int) $request->request->get('nombre_questions', 5);
                // Récupère le niveau de difficulté (défaut: moyen)
                $difficulte = $request->request->get('difficulte', 'moyen');
                
                // Prépare le tableau d'options pour la génération du quiz
                $options = [
                    // Titre du quiz (défaut: "Quiz - [Nom du chapitre]")
                    'titre' => $request->request->get('titre', 'Quiz - ' . $chapitre->getTitre()),
                    // Description du quiz
                    'description' => $request->request->get('description', 'Quiz généré automatiquement par IA'),
                    // Niveau de difficulté des questions
                    'difficulte' => $difficulte,
                    // Pourcentage minimum pour réussir le quiz
                    'seuil_reussite' => (int) $request->request->get('seuil_reussite', 60),
                    // Nombre maximum de tentatives (null = illimité)
                    'max_tentatives' => $request->request->get('max_tentatives') ? (int) $request->request->get('max_tentatives') : null,
                    // Durée maximale en minutes (null = illimité)
                    'duree_max' => $request->request->get('duree_max') ? (int) $request->request->get('duree_max') : null,
                    // État du quiz (toujours en brouillon pour permettre la révision)
                    'etat' => 'brouillon',
                ];

                // Appelle le service IA pour générer le quiz complet
                $quiz = $grokService->genererQuizPourChapitre($chapitre, $nombreQuestions, $options);

                // Affiche un message de succès avec le nombre de questions créées
                $this->addFlash('success', sprintf(
                    '🤖 Quiz généré avec succès ! %d questions créées. Le quiz est en mode brouillon pour révision.',
                    $quiz->getQuestions()->count()
                ));

                // Redirige vers la page d'édition du quiz nouvellement créé
                return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);

            } catch (\Exception $e) {
                // Capture toute exception et affiche le message d'erreur
                $this->addFlash('error', '❌ Erreur lors de la génération: ' . $e->getMessage());
                // Réaffiche le formulaire de génération
                return $this->render('backoffice/quiz/generate.html.twig', [
                    'chapitre' => $chapitre,
                ]);
            }
        }

        // Si la requête est GET, affiche le formulaire de génération
        return $this->render('backoffice/quiz/generate.html.twig', [
            'chapitre' => $chapitre,
        ]);
    }

    /**
     * Régénération des questions d'un quiz existant
     */
    // Route pour régénérer les questions d'un quiz existant (POST uniquement)
    #[Route('/{id}/regenerate', name: 'app_quiz_regenerate', methods: ['POST'])]
    // Restreint l'accès aux administrateurs uniquement
    #[IsGranted('ROLE_ADMIN')]
    // Méthode pour régénérer toutes les questions d'un quiz via l'IA
    public function regenerate(
        Request $request,
        Quiz $quiz,
        GrokQuizGeneratorService $grokService
    ): Response {
        // Vérifie la validité du token CSRF pour sécuriser l'action
        if (!$this->isCsrfTokenValid('regenerate'.$quiz->getId(), $request->request->get('_token'))) {
            // Affiche une erreur si le token est invalide
            $this->addFlash('error', '❌ Token CSRF invalide');
            // Redirige vers la page d'édition du quiz
            return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);
        }

        // Bloc try-catch pour gérer les erreurs de régénération
        try {
            // Récupère le nombre de questions à générer (défaut: 5)
            $nombreQuestions = (int) $request->request->get('nombre_questions', 5);
            // Récupère le niveau de difficulté souhaité (défaut: moyen)
            $difficulte = $request->request->get('difficulte', 'moyen');
            
            // Prépare les options pour la régénération
            $options = [
                // Niveau de difficulté des nouvelles questions
                'difficulte' => $difficulte,
            ];

            // Appelle le service pour supprimer les anciennes questions et en créer de nouvelles
            $grokService->regenererQuestions($quiz, $nombreQuestions, $options);

            // Affiche un message de succès avec le nombre de questions créées
            $this->addFlash('success', sprintf(
                '🔄 Questions régénérées avec succès ! %d nouvelles questions créées.',
                $quiz->getQuestions()->count()
            ));

        } catch (\Exception $e) {
            // Capture toute exception et affiche le message d'erreur
            $this->addFlash('error', '❌ Erreur lors de la régénération: ' . $e->getMessage());
        }

        // Redirige vers la page d'édition du quiz
        return $this->redirectToRoute('app_quiz_edit', ['id' => $quiz->getId()]);
    }
}
