<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Equipe;
use App\Entity\Participation;
use App\Enum\StatutParticipation;
use App\Repository\EvenementRepository;
use App\Repository\EquipeRepository;
use App\Repository\ParticipationRepository;
use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/events')]
class FrontofficeEvenementController extends AbstractController
{
    #[Route('/', name: 'app_events', methods: ['GET'])]
    public function index(
        EvenementRepository $evenementRepository, 
        ParticipationRepository $participationRepository, 
        EntityManagerInterface $entityManager,
        WeatherService $weatherService
    ): Response {
        // Charger tous les événements avec leurs participations, équipes ET étudiants en une seule query (optimisé)
        $evenements = $evenementRepository->createQueryBuilder('e')
            ->leftJoin('e.participations', 'p')
            ->addSelect('p')
            ->leftJoin('p.equipe', 'eq')
            ->addSelect('eq')
            ->leftJoin('eq.etudiants', 'et')
            ->addSelect('et')
            ->getQuery()
            ->getResult();
        
        // Pour chaque événement, mettre à jour le statut et calculer les places disponibles
        $evenementsData = [];
        foreach ($evenements as $evenement) {
            // Mettre à jour le statut automatiquement
            $evenement->updateStatus();
            
            // Filtrer les participations acceptées (déjà chargées)
            $participationsAcceptees = $evenement->getParticipations()->filter(
                fn($p) => $p->getStatut() === StatutParticipation::ACCEPTE
            );
            
            $placesOccupees = count($participationsAcceptees);
            $placesDisponibles = $evenement->getNbMax() - $placesOccupees;
            
            // Récupérer les équipes participantes
            $equipes = [];
            foreach ($participationsAcceptees as $participation) {
                $equipes[] = $participation->getEquipe();
            }
            
            // Récupérer la météo pour l'événement (toujours pour Tunis, Tunisie)
            $weather = $weatherService->getWeatherForEvent('Tunis,TN', $evenement->getDateDebut());
            
            $evenementsData[] = [
                'evenement' => $evenement,
                'placesDisponibles' => $placesDisponibles,
                'placesOccupees' => $placesOccupees,
                'equipes' => $equipes,
                'weather' => $weather
            ];
        }
        
        $entityManager->flush();
        
        return $this->render('frontoffice/evenement/index.html.twig', [
            'evenementsData' => $evenementsData,
            'weatherService' => $weatherService
        ]);
    }
    
    /**
     * Route pour afficher le calendrier des événements
     * Accessible à tous les étudiants pour voir les dates des événements
     */
    #[Route('/calendar', name: 'app_events_calendar', methods: ['GET'])]
    public function calendar(): Response
    {
        return $this->render('frontoffice/evenement/calendar.html.twig');
    }
    
    #[Route('/{id}/participate', name: 'app_event_participate', methods: ['GET'])]
    public function participate(Evenement $evenement, EquipeRepository $equipeRepository, ParticipationRepository $participationRepository): Response
    {
        // Vérifier si les participations sont ouvertes (workflow)
        if (!$evenement->canAcceptParticipations()) {
            $message = match($evenement->getWorkflowStatus()) {
                'termine' => 'This event has ended. Registrations are now closed.',
                'annule' => 'This event has been cancelled. No registrations are accepted.',
                default => 'Registrations are not available for this event.',
            };
            
            $this->addFlash('error', $message);
            return $this->redirectToRoute('app_events');
        }
        
        // Récupérer les équipes participantes avec leurs étudiants en une seule query (optimisé)
        $participationsAcceptees = $participationRepository->createQueryBuilder('p')
            ->leftJoin('p.equipe', 'eq')
            ->addSelect('eq')
            ->leftJoin('eq.etudiants', 'et')
            ->addSelect('et')
            ->where('p.evenement = :evenement')
            ->andWhere('p.statut = :statut')
            ->setParameter('evenement', $evenement)
            ->setParameter('statut', 'Accepté')
            ->getQuery()
            ->getResult();
        
        $equipesDisponibles = [];
        foreach ($participationsAcceptees as $participation) {
            $equipe = $participation->getEquipe();
            if ($equipe->getEtudiants()->count() < 6) {
                $equipesDisponibles[] = $equipe;
            }
        }
        
        return $this->render('frontoffice/evenement/participate.html.twig', [
            'evenement' => $evenement,
            'equipesDisponibles' => $equipesDisponibles,
        ]);
    }
    
    #[Route('/{equipeId}/join/{eventId}', name: 'app_equipe_join', methods: ['POST'])]
    public function joinEquipe(
        int $equipeId, 
        int $eventId,
        EquipeRepository $equipeRepository,
        EvenementRepository $evenementRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $equipe = $equipeRepository->find($equipeId);
        $evenement = $evenementRepository->find($eventId);
        $user = $this->getUser();
        
        if (!$equipe || !$evenement) {
            $this->addFlash('error', 'Équipe ou événement introuvable.');
            return $this->redirectToRoute('app_events');
        }
        
        // Vérifier que l'équipe a moins de 6 membres
        if ($equipe->getEtudiants()->count() >= 6) {
            $this->addFlash('error', 'Cette équipe est complète (6 membres maximum).');
            return $this->redirectToRoute('app_event_participate', ['id' => $eventId]);
        }
        
        // Vérifier que l'utilisateur n'est pas déjà dans cette équipe
        foreach ($equipe->getEtudiants() as $etudiant) {
            if ($etudiant->getId() === $user->getId()) {
                $this->addFlash('error', 'Vous êtes déjà membre de cette équipe.');
                return $this->redirectToRoute('app_event_participate', ['id' => $eventId]);
            }
        }
        
        // Ajouter l'utilisateur à l'équipe
        $equipe->addEtudiant($user);
        $entityManager->flush();
        
        $this->addFlash('success', 'Vous avez rejoint l\'équipe "' . $equipe->getNom() . '" avec succès !');
        return $this->redirectToRoute('app_mes_equipes');
    }
}
