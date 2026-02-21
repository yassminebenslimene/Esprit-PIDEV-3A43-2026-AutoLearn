<?php

namespace App\EventSubscriber;

use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use App\Repository\EvenementRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * EventSubscriber pour charger les événements dans le calendrier
 * 
 * Ce subscriber écoute l'événement SET_DATA du CalendarBundle
 * et charge tous les événements de la base de données pour les afficher
 */
class CalendarSubscriber implements EventSubscriberInterface
{
    private EvenementRepository $evenementRepository;
    private UrlGeneratorInterface $router;

    public function __construct(
        EvenementRepository $evenementRepository,
        UrlGeneratorInterface $router
    ) {
        $this->evenementRepository = $evenementRepository;
        $this->router = $router;
    }

    /**
     * Déclare quel événement on écoute
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * Méthode appelée quand le calendrier charge les données
     * 
     * @param CalendarEvent $calendar L'événement du calendrier
     */
    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        // Récupérer les dates de début et fin demandées par le calendrier
        $start = $calendar->getStart();
        $end = $calendar->getEnd();

        // Récupérer tous les événements de la base de données
        $evenements = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.dateDebut BETWEEN :start and :end')
            ->orWhere('e.dateFin BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();

        // Pour chaque événement de la BDD, créer un Event pour le calendrier
        foreach ($evenements as $evenement) {
            // Mettre à jour le statut
            $evenement->updateStatus();
            
            // Déterminer la couleur selon le type
            $color = $this->getColorByType($evenement->getType()->value);
            
            // Modifier la couleur si événement annulé ou passé
            if ($evenement->getStatus()->value === 'Annulé') {
                $color = '#95a5a6'; // Gris
            } elseif ($evenement->getStatus()->value === 'Passé') {
                $color = '#7fb77e'; // Vert pâle
            }
            
            // Créer l'événement pour le calendrier
            $calendarEvent = new Event(
                $evenement->getTitre(),
                $evenement->getDateDebut(),
                $evenement->getDateFin()
            );
            
            // Personnaliser l'événement
            $calendarEvent->setOptions([
                'backgroundColor' => $color,
                'borderColor' => $color,
            ]);
            
            // Ajouter des données personnalisées (accessibles au clic)
            $calendarEvent->addOption('type', $evenement->getType()->value);
            $calendarEvent->addOption('lieu', $evenement->getLieu());
            $calendarEvent->addOption('status', $evenement->getStatus()->value);
            $calendarEvent->addOption('nbMax', $evenement->getNbMax());
            $calendarEvent->addOption('nbParticipations', count($evenement->getParticipations()));
            $calendarEvent->addOption('description', substr($evenement->getDescription(), 0, 100) . '...');
            
            // URL vers la page de détails
            $calendarEvent->addOption(
                'url',
                $this->router->generate('app_event_show', ['id' => $evenement->getId()])
            );
            
            // Ajouter l'événement au calendrier
            $calendar->addEvent($calendarEvent);
        }
    }

    /**
     * Retourne la couleur selon le type d'événement
     */
    private function getColorByType(string $type): string
    {
        return match($type) {
            'Workshop' => '#667eea',      // Violet
            'Conference' => '#f093fb',    // Rose
            'Hackathon' => '#4facfe',     // Bleu
            'Seminar' => '#43e97b',       // Vert
            'Meetup' => '#f5576c',        // Rouge
            'Training' => '#38f9d7',      // Cyan
            default => '#667eea',         // Violet par défaut
        };
    }
}
