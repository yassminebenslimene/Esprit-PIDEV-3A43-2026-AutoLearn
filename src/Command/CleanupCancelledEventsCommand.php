<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cleanup-cancelled-events',
    description: 'Supprime automatiquement les événements annulés depuis plus de 2 jours'
)]
class CleanupCancelledEventsCommand extends Command
{
    public function __construct(
        private EvenementRepository $evenementRepository,
        private ParticipationRepository $participationRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $now = new \DateTime();
        
        $io->title('Nettoyage des événements annulés');
        $io->text('Date/Heure actuelle: ' . $now->format('Y-m-d H:i:s'));
        
        // Calculer la date limite (2 jours avant maintenant)
        $limitDate = (clone $now)->modify('-2 days');
        $io->text('Suppression des événements annulés avant: ' . $limitDate->format('Y-m-d H:i:s'));
        $io->newLine();
        
        // Récupérer tous les événements annulés
        $evenements = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.isCanceled = :canceled')
            ->andWhere('e.workflowStatus = :status')
            ->setParameter('canceled', true)
            ->setParameter('status', 'annule')
            ->getQuery()
            ->getResult();
        
        $io->text(sprintf('Nombre d\'événements annulés trouvés: %d', count($evenements)));
        
        $deletedCount = 0;
        
        foreach ($evenements as $evenement) {
            // Vérifier si l'événement a été annulé il y a plus de 2 jours
            // On utilise la date de fin comme référence (ou date de début si pas encore commencé)
            $eventDate = $evenement->getDateFin();
            
            // Si l'événement a été annulé il y a plus de 2 jours
            if ($eventDate < $limitDate) {
                $titre = $evenement->getTitre();
                $id = $evenement->getId();
                
                // Suppression en cascade (même logique que dans EvenementController)
                // 1. Récupérer toutes les équipes
                $equipes = $evenement->getEquipes()->toArray();
                
                // 2. Supprimer toutes les participations
                foreach ($equipes as $equipe) {
                    $participations = $this->participationRepository->findBy(['equipe' => $equipe]);
                    foreach ($participations as $participation) {
                        $this->entityManager->remove($participation);
                    }
                }
                $this->entityManager->flush();
                
                // 3. Supprimer toutes les équipes
                foreach ($equipes as $equipe) {
                    $this->entityManager->remove($equipe);
                }
                $this->entityManager->flush();
                
                // 4. Supprimer l'événement
                $this->entityManager->remove($evenement);
                $this->entityManager->flush();
                
                $io->success(sprintf('✓ Événement supprimé: "%s" (ID: %d) - Annulé le: %s', 
                    $titre, 
                    $id, 
                    $eventDate->format('Y-m-d')
                ));
                
                $deletedCount++;
            }
        }
        
        $io->newLine();
        $io->section('Résumé');
        $io->table(
            ['Métrique', 'Valeur'],
            [
                ['Événements annulés trouvés', count($evenements)],
                ['Événements supprimés', $deletedCount],
                ['Événements conservés', count($evenements) - $deletedCount],
            ]
        );
        
        if ($deletedCount > 0) {
            $io->success(sprintf('%d événement(s) annulé(s) supprimé(s) avec succès!', $deletedCount));
        } else {
            $io->info('Aucun événement annulé à supprimer.');
        }
        
        return Command::SUCCESS;
    }
}
