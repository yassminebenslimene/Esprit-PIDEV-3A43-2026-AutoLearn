<?php
// Déclaration du fichier PHP

// Définition du namespace pour le contrôleur front-office
namespace App\Controller\FrontOffice;

// Import de l'entité Chapitre pour accéder aux chapitres
use App\Entity\GestionDeCours\Chapitre;
// Import de l'entité Etudiant pour identifier l'utilisateur connecté
use App\Entity\Etudiant;
// Import du repository des chapitres
use App\Repository\Cours\ChapitreRepository;
// Import du repository des quiz
use App\Repository\QuizRepository;
// Import du service de gestion métier des quiz
use App\Service\QuizManagementService;
// Import du contrôleur de base Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Import de la classe Response pour les réponses HTTP
use Symfony\Component\HttpFoundation\Response;
// Import de l'interface Session pour gérer les données de session
use Symfony\Component\HttpFoundation\Session\SessionInterface;
// Import de l'attribut Route pour définir les routes
use Symfony\Component\Routing\Annotation\Route;

// Définit le préfixe de route et le nom de base pour toutes les méthodes
#[Route('/chapitre/{chapitreId}/quiz', name: 'app_frontoffice_quiz_')]
// Classe contrôleur pour afficher les quiz côté étudiant
class QuizController extends AbstractController
{
    // Route pour lister tous les quiz d'un chapitre (GET uniquement)
    #[Route('', name: 'list', methods: ['GET'])]
    // Méthode pour afficher la liste des quiz actifs d'un chapitre
    public function list(
        int $chapitreId, 
        ChapitreRepository $chapitreRepository, 
        QuizRepository $quizRepository,
        QuizManagementService $quizService,
        SessionInterface $session
    ): Response {
        // Recherche le chapitre par son ID dans la base de données
        $chapitre = $chapitreRepository->find($chapitreId);
        
        // Si le chapitre n'existe pas, lance une exception 404
        if (!$chapitre) {
            throw $this->createNotFoundException('Chapitre non trouvé');
        }

        // Crée une requête personnalisée avec le QueryBuilder de Doctrine
        $quizzes = $quizRepository->createQueryBuilder('q')
            // Filtre les quiz appartenant au chapitre spécifié
            ->where('q.chapitre = :chapitre')
            // Filtre uniquement les quiz avec l'état 'actif'
            ->andWhere('q.etat = :etat')
            // Définit la valeur du paramètre :chapitre
            ->setParameter('chapitre', $chapitre)
            // Définit la valeur du paramètre :etat
            ->setParameter('etat', 'actif')
            // Récupère la requête SQL générée
            ->getQuery()
            // Exécute la requête et retourne les résultats
            ->getResult();

        // Initialise un tableau vide pour stocker les statistiques de chaque quiz
        $quizStatistiques = [];
        // Récupère l'utilisateur actuellement connecté
        $user = $this->getUser();
        
        // Vérifie si l'utilisateur est un étudiant
        if ($user instanceof Etudiant) {
            // Parcourt chaque quiz actif
            foreach ($quizzes as $quiz) {
                // Récupère les statistiques de l'étudiant pour ce quiz (tentatives, résultats, etc.)
                $quizStatistiques[$quiz->getId()] = $quizService->getStatistiquesEtudiant($user, $quiz, $session);
            }
        }

        // Retourne la vue avec la liste des quiz et leurs statistiques
        return $this->render('frontoffice/quiz/list.html.twig', [
            // Passe le chapitre au template
            'chapitre' => $chapitre,
            // Passe la liste des quiz actifs au template
            'quizzes' => $quizzes,
            // Passe les statistiques de l'étudiant au template
            'quizStatistiques' => $quizStatistiques,
        ]);
    }
}
