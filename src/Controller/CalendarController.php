<?php
// src/Controller/CalendarController.php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('/fc-load-events', name: 'fc_load_events', methods: ['POST'])]
    public function loadEvents(Request $request, ChallengeRepository $challengeRepository): JsonResponse
    {
        // Récupérer les dates envoyées par FullCalendar
        $start = $request->get('start');
        $end = $request->get('end');
        
        $startDate = $start ? new \DateTime($start) : new \DateTime();
        $endDate = $end ? new \DateTime($end) : new \DateTime('+1 month');
        
        // Récupérer les challenges entre ces dates
        $challenges = $challengeRepository->createQueryBuilder('c')
            ->where('c.date_debut BETWEEN :start and :end')
            ->orWhere('c.date_fin BETWEEN :start and :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();
        
        $events = [];
        foreach ($challenges as $challenge) {
            // Définir la couleur selon le niveau
            $color = '#7fb77e'; // Vert pour Facile
            if ($challenge->getNiveau() === 'Intermédiaire') {
                $color = '#f3b562'; // Orange
            } elseif ($challenge->getNiveau() === 'Avancé') {
                $color = '#f17b7b'; // Rouge
            }
            
            $events[] = [
                'title' => $challenge->getTitre(),
                'start' => $challenge->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $challenge->getDateFin()->format('Y-m-d H:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'url' => $this->generateUrl('frontchallenge', ['id' => $challenge->getId()]),
            ];
        }
        
        return $this->json($events);
    }
}