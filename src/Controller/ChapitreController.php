<?php

namespace App\Controller;

use App\Entity\GestionDeCours\Chapitre;
use App\Form\GestionCours\ChapitreType;
use App\Repository\Cours\ChapitreRepository;
use App\Service\PdfGeneratorService;
use App\Service\CourseProgressService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chapitre')]
class ChapitreController extends AbstractController
{
    #[Route('/', name: 'app_chapitre_index', methods: ['GET'])]
    public function index(ChapitreRepository $chapitreRepository): Response
    {
        return $this->render('backoffice/chapitre/index.html.twig', [
            'chapitres' => $chapitreRepository->findAll(),
        ]);
    }

    #[Route('/front', name: 'app_chapitre_index_front', methods: ['GET'])]
    public function indexFront(
        ChapitreRepository $chapitreRepository,
        CourseProgressService $progressService
    ): Response
    {
        $chapitres = $chapitreRepository->findAll();
        $user = $this->getUser();
        $progressStats = null;
        
        // Si l'utilisateur est connecté et qu'il y a des chapitres
        if ($user && !empty($chapitres)) {
            // Prendre le cours du premier chapitre pour afficher la progression globale
            $cours = $chapitres[0]->getCours();
            if ($cours) {
                $progressStats = $progressService->getCourseProgressStats($user, $cours);
            }
        }
        
        return $this->render('frontoffice/chapitre/index.html.twig', [
            'chapitres' => $chapitres,
            'progress_stats' => $progressStats,
        ]);
    }

    #[Route('/new', name: 'app_chapitre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chapitre = new Chapitre();
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapitre);
            $entityManager->flush();

            return $this->redirectToRoute('app_chapitre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/chapitre/new.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chapitre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chapitre $chapitre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chapitre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/chapitre/edit.html.twig', [
            'chapitre' => $chapitre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_chapitre_delete', methods: ['POST'])]
    public function delete(Request $request, Chapitre $chapitre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chapitre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapitre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chapitre_index', [], Response::HTTP_SEE_OTHER);
    }

    // ==================== BACKOFFICE ====================
    #[Route('/{id}', name: 'app_chapitre_show', methods: ['GET'])]
    public function show(Chapitre $chapitre): Response
    {
        return $this->render('backoffice/chapitre/show.html.twig', [
            'chapitre' => $chapitre,
        ]);
    }

    // ==================== FRONTOFFICE ====================
    #[Route('/front/{id}', name: 'app_chapitre_show_front', methods: ['GET'])]
    public function showFront(
        Chapitre $chapitre,
        CourseProgressService $progressService
    ): Response
    {
        $user = $this->getUser();
        $progressStats = null;
        
        // Si l'utilisateur est connecté, calculer la progression du cours
        if ($user && $chapitre->getCours()) {
            $progressStats = $progressService->getCourseProgressStats($user, $chapitre->getCours());
        }
        
        return $this->render('frontoffice/chapitre/show.html.twig', [
            'chapitre' => $chapitre,
            'progress_stats' => $progressStats,
        ]);
    }

    // ==================== PDF GENERATION ====================
    
    /**
     * Afficher le chapitre en PDF dans le navigateur
     */
    #[Route('/front/{id}/pdf', name: 'app_chapitre_pdf_preview', methods: ['GET'])]
    public function pdfPreview(Chapitre $chapitre, PdfGeneratorService $pdfGenerator): Response
    {
        // Générer le PDF
        $dompdf = $pdfGenerator->generateChapterPdf($chapitre);
        
        // Afficher dans le navigateur (inline)
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="chapitre-' . $chapitre->getOrdre() . '-' . $this->slugify($chapitre->getTitre()) . '.pdf"'
            ]
        );
    }

    /**
     * Télécharger le chapitre en PDF
     */
    #[Route('/front/{id}/pdf/download', name: 'app_chapitre_pdf_download', methods: ['GET'])]
    public function pdfDownload(Chapitre $chapitre, PdfGeneratorService $pdfGenerator): Response
    {
        // Générer le PDF
        $dompdf = $pdfGenerator->generateChapterPdf($chapitre);
        
        // Forcer le téléchargement (attachment)
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="chapitre-' . $chapitre->getOrdre() . '-' . $this->slugify($chapitre->getTitre()) . '.pdf"'
            ]
        );
    }

    /**
     * Utilitaire pour créer un slug depuis un titre
     */
    private function slugify(string $text): string
    {
        // Remplacer les caractères non alphanumériques par des tirets
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Translittérer
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        
        // Supprimer les caractères indésirables
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Supprimer les tirets en début et fin
        $text = trim($text, '-');
        
        // Supprimer les tirets multiples
        $text = preg_replace('~-+~', '-', $text);
        
        // Mettre en minuscules
        $text = strtolower($text);
        
        return empty($text) ? 'chapitre' : $text;
    }
}