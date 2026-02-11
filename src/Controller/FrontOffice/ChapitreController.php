<?php

namespace App\Controller\FrontOffice;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Repository\ChapitreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chapitres', name: 'app_frontoffice_chapitre_')]
class ChapitreController extends AbstractController
{
    // Liste de tous les chapitres (si besoin)
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ChapitreRepository $chapitreRepository): Response
    {
        return $this->render('frontoffice/chapitre/index.html.twig', [
            'chapitres' => $chapitreRepository->findAll(),
        ]);
    }

    // Liste des chapitres d'un cours spécifique
    #[Route('/cours/{coursId}', name: 'by_cours', methods: ['GET'])]
    public function byCours(int $coursId, ChapitreRepository $chapitreRepository): Response
    {
        $chapitres = $chapitreRepository->findBy(
            ['cours' => $coursId],
            ['ordre' => 'ASC']
        );

        return $this->render('frontoffice/chapitre/index.html.twig', [
            'chapitres' => $chapitres,
            'coursId' => $coursId, // optionnel, pour le titre
        ]);
    }

    // Détail d'un chapitre
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Chapitre $chapitre): Response
    {
        return $this->render('frontoffice/chapitre/show.html.twig', [
            'chapitre' => $chapitre,
        ]);
    }
}