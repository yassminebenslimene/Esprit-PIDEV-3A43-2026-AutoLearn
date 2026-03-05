<?php

namespace App\Controller;

use App\Repository\Cours\CoursRepository;
use App\Repository\Cours\ChapitreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Utils\LikeEscaper;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $query = $request->query->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            // Recherche dans les cours
            $coursResults = $entityManager->getRepository('App\Entity\GestionDeCours\Cours')
                ->createQueryBuilder('c')
                ->where('c.titre LIKE :query OR c.description LIKE :query')
                ->setParameter('query', LikeEscaper::escapeAndWrap($query))
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            foreach ($coursResults as $cours) {
                $results[] = [
                    'type' => 'cours',
                    'title' => $cours->getTitre(),
                    'description' => $cours->getDescription(),
                    'url' => $this->generateUrl('app_frontoffice_chapitre_by_cours', ['coursId' => $cours->getId()]),
                    'icon' => 'fa-book'
                ];
            }

            // Recherche dans les chapitres
            $chapitreResults = $entityManager->getRepository('App\Entity\GestionDeCours\Chapitre')
                ->createQueryBuilder('ch')
                ->where('ch.titre LIKE :query OR ch.contenu LIKE :query')
                ->setParameter('query', LikeEscaper::escapeAndWrap($query))
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            foreach ($chapitreResults as $chapitre) {
                $results[] = [
                    'type' => 'chapitre',
                    'title' => $chapitre->getTitre(),
                    'description' => substr($chapitre->getContenu(), 0, 150) . '...',
                    'url' => $this->generateUrl('app_chapitre_show_front', ['id' => $chapitre->getId()]),
                    'icon' => 'fa-file-text',
                    'cours' => $chapitre->getCours() ? $chapitre->getCours()->getTitre() : null
                ];
            }

            // Recherche dans les quiz
            $quizResults = $entityManager->getRepository('App\Entity\Quiz')
                ->createQueryBuilder('q')
                ->where('q.titre LIKE :query OR q.description LIKE :query')
                ->setParameter('query', LikeEscaper::escapeAndWrap($query))
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            foreach ($quizResults as $quiz) {
                $chapitre = $quiz->getChapitre();
                $results[] = [
                    'type' => 'quiz',
                    'title' => $quiz->getTitre(),
                    'description' => $quiz->getDescription(),
                    'url' => $chapitre ? $this->generateUrl('app_frontoffice_quiz_list', ['chapitreId' => $chapitre->getId()]) : '#',
                    'icon' => 'fa-question-circle'
                ];
            }

            // Recherche dans les événements (si l'entité existe)
            try {
                $eventResults = $entityManager->getRepository('App\Entity\Evenement')
                    ->createQueryBuilder('e')
                    ->where('e.titre LIKE :query OR e.description LIKE :query')
                    ->setParameter('query', LikeEscaper::escapeAndWrap($query))
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();

                foreach ($eventResults as $event) {
                    $results[] = [
                        'type' => 'event',
                        'title' => $event->getTitre(),
                        'description' => $event->getDescription(),
                        'url' => $this->generateUrl('app_events'),
                        'icon' => 'fa-calendar'
                    ];
                }
            } catch (\Exception $e) {
                // L'entité Evenement n'existe pas encore
            }

            // Recherche dans les challenges (si l'entité existe)
            try {
                $challengeResults = $entityManager->getRepository('App\Entity\Challenge')
                    ->createQueryBuilder('ch')
                    ->where('ch.titre LIKE :query OR ch.description LIKE :query')
                    ->setParameter('query', LikeEscaper::escapeAndWrap($query))
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();

                foreach ($challengeResults as $challenge) {
                    $results[] = [
                        'type' => 'challenge',
                        'title' => $challenge->getTitre(),
                        'description' => $challenge->getDescription(),
                        'url' => $this->generateUrl('frontchallenge'),
                        'icon' => 'fa-trophy'
                    ];
                }
            } catch (\Exception $e) {
                // L'entité Challenge n'existe pas encore
            }
        }

        return $this->render('frontoffice/search/results.html.twig', [
            'query' => $query,
            'results' => $results,
            'total' => count($results)
        ]);
    }
}
