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

#[AsCommand(
    name: 'app:update-evenement-workflow',
    description: 'Met à jour automatiquement les statuts des événements via le Workflow Component'
)]
class UpdateEvenementWorkflowCommand extends Command
{
    public function __construct(
        private EvenementRepository $evenementRepository,
        private WorkflowInterface $evenementPublishingStateMachine,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $now = new \DateTime();
        
        $io->title('Mise à jour automatique des statuts d\'événements');
        $io->text('Date/Heure actuelle: ' . $now->format('Y-m-d H:i:s'));
        
        // Récupérer tous les événements non annulés
        $evenements = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.isCanceled = :canceled')
            ->setParameter('canceled', false)
            ->getQuery()
            ->getResult();
        
        $io->text(sprintf('Nombre d\'événements à vérifier: %d', count($evenements)));
        $io->newLine();
        
        $demarres = 0;
        $termines = 0;
        
        foreach ($evenements as $evenement) {
            $workflowStatus = $evenement->getWorkflowStatus();
            $titre = $evenement->getTitre();
            
            // Transition: planifie → en_cours
            if ($workflowStatus === 'planifie' 
                && $evenement->getDateDebut() <= $now
                && $this->evenementPublishingStateMachine->can($evenement, 'demarrer')) {
                
                $this->evenementPublishingStateMachine->apply($evenement, 'demarrer');
                $io->success(sprintf('✓ Événement "%s" (ID: %d) démarré', $titre, $evenement->getId()));
                $demarres++;
            }
            
            // Transition: en_cours → termine
            elseif ($workflowStatus === 'en_cours' 
                && $evenement->getDateFin() < $now
                && $this->evenementPublishingStateMachine->can($evenement, 'terminer')) {
                
                $this->evenementPublishingStateMachine->apply($evenement, 'terminer');
                $io->success(sprintf('✓ Événement "%s" (ID: %d) terminé', $titre, $evenement->getId()));
                $termines++;
            }
        }
        
        // Sauvegarder tous les changements
        $this->entityManager->flush();
        
        $io->newLine();
        $io->section('Résumé');
        $io->table(
            ['Action', 'Nombre'],
            [
                ['Événements démarrés', $demarres],
                ['Événements terminés', $termines],
                ['Total des transitions', $demarres + $termines],
            ]
        );
        
        if ($demarres + $termines > 0) {
            $io->success('Mise à jour terminée avec succès!');
        } else {
            $io->info('Aucune mise à jour nécessaire.');
        }
        
        return Command::SUCCESS;
    }
}
