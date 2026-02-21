<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-evenements-status',
    description: 'Met à jour les statuts de tous les événements selon leurs dates',
)]
class UpdateEvenementsStatusCommand extends Command
{
    public function __construct(
        private EvenementRepository $evenementRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Mise à jour des statuts des événements');
        
        // Récupérer tous les événements
        $evenements = $this->evenementRepository->findAll();
        
        $io->info(sprintf('Nombre d\'événements trouvés: %d', count($evenements)));
        
        $updated = 0;
        $now = new \DateTime();
        
        foreach ($evenements as $evenement) {
            $oldStatus = $evenement->getStatus()->value;
            
            // Appeler updateStatus() pour recalculer le statut
            $evenement->updateStatus();
            
            $newStatus = $evenement->getStatus()->value;
            
            if ($oldStatus !== $newStatus) {
                $io->success(sprintf(
                    '%s: %s → %s (Date fin: %s)',
                    $evenement->getTitre(),
                    $oldStatus,
                    $newStatus,
                    $evenement->getDateFin()->format('d/m/Y H:i')
                ));
                $updated++;
            }
        }
        
        // Sauvegarder les changements
        $this->entityManager->flush();
        
        $io->section('Résultat');
        $io->success(sprintf('%d événement(s) mis à jour', $updated));
        $io->info(sprintf('%d événement(s) inchangés', count($evenements) - $updated));
        
        return Command::SUCCESS;
    }
}
