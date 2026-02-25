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
    name: 'app:send-certificates',
    description: 'Envoie des certificats de participation après les événements terminés',
)]
class SendCertificatesCommand extends Command
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
        
        // Récupérer tous les événements terminés (date de fin passée)
        $now = new \DateTime();
        
        $events = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.dateFin < :now')
            ->andWhere('e.isCanceled = false')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
        
        if (empty($events)) {
            $io->success('Aucun événement terminé. Aucun certificat envoyé.');
            return Command::SUCCESS;
        }
        
        $certificateCount = 0;
        
        foreach ($events as $event) {
            $io->info('Traitement de l\'événement: ' . $event->getTitre());
            
            // Récupérer toutes les participations acceptées
            foreach ($event->getParticipations() as $participation) {
                if ($participation->getStatut()->value !== 'Accepté') {
                    continue;
                }
                
                // Envoyer un certificat à chaque membre de l'équipe
                foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
                    try {
                        $this->emailService->sendCertificate(
                            $etudiant->getEmail(),
                            $etudiant->getPrenom(),
                            $etudiant->getNom(),
                            $event->getTitre(),
                            $event->getType()->value,
                            $event->getDateDebut()
                        );
                        $certificateCount++;
                        $io->text('  ✓ Certificat envoyé à: ' . $etudiant->getEmail());
                    } catch (\Exception $e) {
                        $io->error('  ✗ Erreur pour ' . $etudiant->getEmail() . ': ' . $e->getMessage());
                    }
                }
            }
        }
        
        $io->success(sprintf('Terminé! %d certificats envoyés pour %d événement(s).', $certificateCount, count($events)));
        
        return Command::SUCCESS;
    }
}
