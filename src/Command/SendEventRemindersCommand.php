<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use App\Service\EmailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-event-reminders',
    description: 'Envoie des emails de rappel 3 jours avant chaque événement',
)]
class SendEventRemindersCommand extends Command
{
    private EvenementRepository $evenementRepository;
    private EmailService $emailService;

    public function __construct(
        EvenementRepository $evenementRepository,
        EmailService $emailService
    ) {
        parent::__construct();
        $this->evenementRepository = $evenementRepository;
        $this->emailService = $emailService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Calculer la date dans 3 jours
        $targetDate = new \DateTime('+3 days');
        $targetDate->setTime(0, 0, 0);
        
        $nextDay = clone $targetDate;
        $nextDay->modify('+1 day');
        
        // Récupérer tous les événements qui auront lieu dans 3 jours
        $events = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.dateDebut >= :targetDate')
            ->andWhere('e.dateDebut < :nextDay')
            ->andWhere('e.isCanceled = false')
            ->setParameter('targetDate', $targetDate)
            ->setParameter('nextDay', $nextDay)
            ->getQuery()
            ->getResult();
        
        if (empty($events)) {
            $io->success('Aucun événement dans 3 jours. Aucun email envoyé.');
            return Command::SUCCESS;
        }
        
        $emailCount = 0;
        
        foreach ($events as $event) {
            $io->info('Traitement de l\'événement: ' . $event->getTitre());
            
            // Récupérer toutes les participations acceptées
            foreach ($event->getParticipations() as $participation) {
                if ($participation->getStatut()->value !== 'Accepté') {
                    continue;
                }
                
                // Envoyer un email à chaque membre de l'équipe
                foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
                    try {
                        $this->emailService->sendEventReminder(
                            $etudiant->getEmail(),
                            $etudiant->getPrenom() . ' ' . $etudiant->getNom(),
                            $event->getTitre(),
                            $event->getDateDebut(),
                            $event->getLieu()
                        );
                        $emailCount++;
                        $io->text('  ✓ Email envoyé à: ' . $etudiant->getEmail());
                    } catch (\Exception $e) {
                        $io->error('  ✗ Erreur pour ' . $etudiant->getEmail() . ': ' . $e->getMessage());
                    }
                }
            }
        }
        
        $io->success(sprintf('Terminé! %d emails de rappel envoyés pour %d événement(s).', $emailCount, count($events)));
        
        return Command::SUCCESS;
    }
}
