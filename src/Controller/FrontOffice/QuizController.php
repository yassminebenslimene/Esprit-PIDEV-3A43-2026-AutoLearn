<?php

namespace App\Controller\FrontOffice;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\Etudiant;
use App\Repository\Cours\ChapitreRepository;
use App\Repository\QuizRepository;
use App\Service\QuizManagementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chapitre/{chapitreId}/quiz', name: 'app_frontoffice_quiz_')]
class QuizController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        int $chapitreId, 
        ChapitreRepository $chapitreRepository, 
        QuizRepository $quizRepository,
        QuizManagementService $quizService,
        SessionInterface $session
    ): Response {
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

        // Ajouter les statistiques de tentatives pour chaque quiz si l'utilisateur est connecté
        $quizStatistiques = [];
        $user = $this->getUser();
        
        if ($user instanceof Etudiant) {
            foreach ($quizzes as $quiz) {
                $quizStatistiques[$quiz->getId()] = $quizService->getStatistiquesEtudiant($user, $quiz, $session);
            }
        }

        return $this->render('frontoffice/quiz/list.html.twig', [
            'chapitre' => $chapitre,
            'quizzes' => $quizzes,
            'quizStatistiques' => $quizStatistiques,
        ]);
    }
}
