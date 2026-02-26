<?php
// src/EventListener/CalendarListener.php

namespace App\EventListener;

use App\Repository\ChallengeRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarListener implements EventSubscriberInterface
{
    public function __construct(
        private ChallengeRepository $challengeRepository,
        private UrlGeneratorInterface $router
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        // Récupérer les dates de début et fin du calendrier
        $start = $calendar->getStart();
        $end = $calendar->getEnd();

        // Récupérer les challenges entre ces dates
        $challenges = $this->challengeRepository->createQueryBuilder('c')
            ->where('c.date_debut BETWEEN :start and :end')
            ->orWhere('c.date_fin BETWEEN :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        foreach ($challenges as $challenge) {
            // Définir la couleur selon le niveau
            $bgColor = match($challenge->getNiveau()) {
                'Facile' => '#7fb77e', // Vert
                'Intermédiaire' => '#f3b562', // Orange
                'Avancé' => '#f17b7b', // Rouge
                default => '#7fb77e'
            };

            // Créer l'événement pour le calendrier
            $event = new Event(
                $challenge->getTitre(), // Titre affiché
                $challenge->getDateDebut(), // Date de début
                $challenge->getDateFin() // Date de fin
            );

            // Personnaliser l'apparence
            $event->setOptions([
                'backgroundColor' => $bgColor,
                'borderColor' => $bgColor,
                'textColor' => '#ffffff',
                'url' => $this->router->generate('frontchallenge', [
                    'id' => $challenge->getId()
                ])
            ]);

            // Ajouter l'événement au calendrier
            $calendar->addEvent($event);
        }
    }
}