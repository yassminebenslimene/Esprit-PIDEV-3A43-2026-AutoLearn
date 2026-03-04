<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Workflow\WorkflowInterface;
use Psr\Log\LoggerInterface;

#[AsCommand(
    name: 'app:update-event-status',
    description: 'Met à jour automatiquement le statut des événements et applique les transitions du workflow',
)]
class UpdateEventStatusCommand extends Command
{
    public function __construct(
        private EvenementRepository $evenementRepository,
        private EntityManagerInterface $entityManager,
        private WorkflowInterface $evenementPublishingStateMachine,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Mise à jour automatique des statuts d\'événements');
        
        $now = new \DateTime();
        $eventsUpdated = 0;
        $eventsStarted = 0;
        $eventsCompleted = 0;
        
        // Récupérer tous les événements non annulés
        $events = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.isCanceled = false')
            ->getQuery()
            ->getResult();
        
        $io->info(sprintf('Traitement de %d événement(s)...', count($events)));
        
        foreach ($events as $event) {
            $currentStatus = $event->getWorkflowStatus();
            $updated = false;
            
            try {
                // Cas 1: Événement planifié qui doit démarrer
                if ($currentStatus === 'planifie' && $now >= $event->getDateDebut()) {
                    if ($this->evenementPublishingStateMachine->can($event, 'demarrer')) {
                        $this->evenementPublishingStateMachine->apply($event, 'demarrer');
                        $io->success(sprintf('✓ Événement "%s" démarré', $event->getTitre()));
                        $eventsStarted++;
                        $updated = true;
                    }
                }
                
                // Cas 2: Événement en cours qui doit se terminer
                if ($currentStatus === 'en_cours' && $now > $event->getDateFin()) {
                    if ($this->evenementPublishingStateMachine->can($event, 'terminer')) {
                        $this->evenementPublishingStateMachine->apply($event, 'terminer');
                        $io->success(sprintf('✓ Événement "%s" terminé', $event->getTitre()));
                        $eventsCompleted++;
                        $updated = true;
                    }
                }
                
                // Mettre à jour le statut enum également
                $event->updateStatus();
                
                if ($updated) {
                    $eventsUpdated++;
                }
                
            } catch (\Exception $e) {
                $io->error(sprintf('Erreur pour l\'événement "%s": %s', $event->getTitre(), $e->getMessage()));
                $this->logger->error('Erreur mise à jour événement', [
                    'evenement_id' => $event->getId(),
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        // Sauvegarder tous les changements
        $this->entityManager->flush();
        
        $io->newLine();
        $io->section('Résumé');
        $io->table(
            ['Statistique', 'Valeur'],
            [
                ['Événements traités', count($events)],
                ['Événements mis à jour', $eventsUpdated],
                ['Événements démarrés', $eventsStarted],
                ['Événements terminés', $eventsCompleted],
            ]
        );
        
        if ($eventsUpdated > 0) {
            $io->success(sprintf('Terminé! %d événement(s) mis à jour avec succès.', $eventsUpdated));
        } else {
            $io->info('Aucun événement à mettre à jour.');
        }
        
        return Command::SUCCESS;
    }
}
