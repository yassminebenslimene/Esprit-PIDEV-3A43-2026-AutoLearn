<?php

namespace App\Controller;

use App\Service\ChapterExplainerService;
use App\Repository\Cours\ChapitreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/chapter-explainer')]
class ChapterExplainerController extends AbstractController
{
    public function __construct(
        private ChapterExplainerService $explainerService,
        private ChapitreRepository $chapitreRepository
    ) {
    }

    /**
     * Page d'explication d'un chapitre
     */
    #[Route('/{id}', name: 'app_chapter_explainer', methods: ['GET'])]
    public function index(int $id): Response
    {
        $chapitre = $this->chapitreRepository->find($id);

        if (!$chapitre) {
            throw $this->createNotFoundException('Chapitre non trouvé');
        }

        return $this->render('frontoffice/chapter_explainer/index.html.twig', [
            'chapitre' => $chapitre,
        ]);
    }

    /**
     * API pour générer l'explication
     */
    #[Route('/api/explain/{id}', name: 'app_chapter_explainer_api', methods: ['POST'])]
    public function explain(int $id, Request $request): JsonResponse
    {
        $chapitre = $this->chapitreRepository->find($id);

        if (!$chapitre) {
            return $this->json(['error' => 'Chapitre non trouvé'], 404);
        }

        $level = $request->request->get('level', 'beginner');
        
        if (!in_array($level, ['beginner', 'advanced'])) {
            return $this->json(['error' => 'Niveau invalide'], 400);
        }

        // Récupérer le contenu du chapitre
        $content = $chapitre->getContenu() ?? $chapitre->getTitre();

        // Générer l'explication
        $result = $this->explainerService->explainChapter($content, $level);

        return $this->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
