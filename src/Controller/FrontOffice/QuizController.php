<?php

namespace App\Controller\FrontOffice;

use App\Entity\GestionDeCours\Chapitre;
use App\Repository\Cours\ChapitreRepository;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
