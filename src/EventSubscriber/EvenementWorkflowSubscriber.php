<?php

namespace App\EventSubscriber;

use App\Entity\Evenement;
use App\Service\EmailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Security\Core\Security;

/**
 * EventSubscriber pour écouter les transitions du workflow des événements
 * 
 * Enregistre l'historique complet:
 * - Qui a fait la transition
 * - Quand (timestamp)
 * - Quelle transition
 * - De quel état vers quel état
 */
class EvenementWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private EmailService $emailService,
        private ?Security $security = null
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Événements de transition
            'workflow.evenement_publishing.transition' => 'onTransition',
            'workflow.evenement_publishing.entered' => 'onEntered',
            'workflow.evenement_publishing.completed' => 'onCompleted',
            
            // Événements spécifiques par état
            'workflow.evenement_publishing.entered.en_cours' => 'onEnCours',
            'workflow.evenement_publishing.entered.termine' => 'onTermine',
            'workflow.evenement_publishing.entered.annule' => 'onAnnule',
            
            // Guards (conditions)
            'workflow.evenement_publishing.guard' => 'onGuard',
        ];
    }

    /**
     * Appelé lors de chaque transition
     * Enregistre l'historique complet
     */
    public function onTransition(Event $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        $transition = $event->getTransition();
        $from = $transition->getFroms();
        $to = $transition->getTos();
        
        // Récupérer l'utilisateur actuel (si connecté)
        $user = $this->security?->getUser();
        $username = $user ? $user->getUserIdentifier() : 'SYSTEM';
        
        // Logger l'historique complet
        $this->logger->info('Transition d\'événement', [
            'evenement_id' => $evenement->getId(),
            'evenement_titre' => $evenement->getTitre(),
            'transition' => $transition->getName(),
            'from' => $from,
            'to' => $to,
            'user' => $username,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            'workflow' => 'evenement_publishing'
        ]);
    }

    /**
     * Appelé quand on entre dans un nouvel état
     */
    public function onEntered(Event $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        $marking = $event->getMarking();
        
        $this->logger->info('Événement entré dans un nouvel état', [
            'evenement_id' => $evenement->getId(),
            'nouvel_etat' => $evenement->getWorkflowStatus(),
            'places' => $marking->getPlaces(),
        ]);
    }

    /**
     * Appelé quand la transition est complétée
     */
    public function onCompleted(Event $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        
        $this->logger->info('Transition complétée', [
            'evenement_id' => $evenement->getId(),
            'etat_final' => $evenement->getWorkflowStatus(),
        ]);
    }

    /**
     * Appelé quand un événement démarre
     * Envoyer des notifications, emails, etc.
     */
    public function onEnCours(Event $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        
        $this->logger->info('🚀 Événement démarré', [
            'evenement_id' => $evenement->getId(),
            'titre' => $evenement->getTitre(),
            'date_debut' => $evenement->getDateDebut()->format('Y-m-d H:i:s'),
        ]);
        
        // Envoyer email à tous les membres des équipes participantes
        $this->sendEmailsToParticipants($evenement, 'started');
    }

    /**
     * Appelé quand un événement termine
     * Générer certificats, envoyer emails de remerciement, etc.
     */
    public function onTermine(Event $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        
        $this->logger->info('✅ Événement terminé', [
            'evenement_id' => $evenement->getId(),
            'titre' => $evenement->getTitre(),
            'date_fin' => $evenement->getDateFin()->format('Y-m-d H:i:s'),
        ]);
        
        // Envoyer automatiquement les certificats de participation
        $this->sendCertificatesToParticipants($evenement);
    }

    /**
     * Appelé quand un événement est annulé
     * Envoyer notifications d'annulation, etc.
     */
    public function onAnnule(Event $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        
        $this->logger->warning('❌ Événement annulé', [
            'evenement_id' => $evenement->getId(),
            'titre' => $evenement->getTitre(),
        ]);
        
        // Envoyer email d'annulation à tous les membres des équipes participantes
        $this->sendEmailsToParticipants($evenement, 'cancelled');
    }
    
    /**
     * Envoie des emails à tous les membres des équipes participantes
     * 
     * @param Evenement $evenement L'événement concerné
     * @param string $type Type d'email: 'started' ou 'cancelled'
     */
    private function sendEmailsToParticipants(Evenement $evenement, string $type): void
    {
        $emailsSent = 0;
        $emailsFailed = 0;
        
        $this->logger->info('Début envoi emails', [
            'type' => $type,
            'evenement_id' => $evenement->getId(),
            'nb_participations' => $evenement->getParticipations()->count(),
        ]);
        
        // Récupérer toutes les participations acceptées
        foreach ($evenement->getParticipations() as $participation) {
            // Vérifier que la participation est acceptée (comparer avec l'enum directement)
            if ($participation->getStatut() !== \App\Enum\StatutParticipation::ACCEPTE) {
                $this->logger->debug('Participation ignorée (non acceptée)', [
                    'participation_id' => $participation->getId(),
                    'statut' => $participation->getStatut()->value,
                ]);
                continue;
            }
            
            $equipe = $participation->getEquipe();
            $teamName = $equipe->getNom();
            
            $this->logger->info('Traitement équipe', [
                'equipe_id' => $equipe->getId(),
                'equipe_nom' => $teamName,
                'nb_etudiants' => $equipe->getEtudiants()->count(),
            ]);
            
            // Envoyer un email à chaque étudiant de l'équipe
            foreach ($equipe->getEtudiants() as $etudiant) {
                try {
                    $studentName = $etudiant->getPrenom() . ' ' . $etudiant->getNom();
                    $email = $etudiant->getEmail();
                    
                    if ($type === 'started') {
                        $this->emailService->sendEventStarted(
                            $email,
                            $studentName,
                            $teamName,
                            $evenement->getTitre(),
                            $evenement->getDateDebut(),
                            $evenement->getLieu()
                        );
                    } elseif ($type === 'cancelled') {
                        $this->emailService->sendEventCancellation(
                            $email,
                            $studentName,
                            $teamName,
                            $evenement->getTitre(),
                            $evenement->getDateDebut(),
                            $evenement->getLieu()
                        );
                    }
                    
                    $emailsSent++;
                    
                    $this->logger->info('Email envoyé', [
                        'type' => $type,
                        'evenement_id' => $evenement->getId(),
                        'student_email' => $email,
                        'team' => $teamName,
                    ]);
                    
                } catch (\Exception $e) {
                    $emailsFailed++;
                    $this->logger->error('Erreur envoi email', [
                        'type' => $type,
                        'evenement_id' => $evenement->getId(),
                        'student_email' => $etudiant->getEmail(),
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        $this->logger->info('Envoi d\'emails terminé', [
            'type' => $type,
            'evenement_id' => $evenement->getId(),
            'emails_sent' => $emailsSent,
            'emails_failed' => $emailsFailed,
        ]);
    }
    
    /**
     * Envoie automatiquement les certificats de participation à tous les participants
     * 
     * @param Evenement $evenement L'événement terminé
     */
    private function sendCertificatesToParticipants(Evenement $evenement): void
    {
        $certificatesSent = 0;
        $certificatesFailed = 0;
        $quotaExceeded = false;
        
        $this->logger->info('🎓 Début envoi des certificats', [
            'evenement_id' => $evenement->getId(),
            'evenement_titre' => $evenement->getTitre(),
            'nb_participations' => $evenement->getParticipations()->count(),
        ]);
        
        // Récupérer toutes les participations acceptées
        foreach ($evenement->getParticipations() as $participation) {
            // Si le quota est dépassé, arrêter l'envoi
            if ($quotaExceeded) {
                $this->logger->warning('⚠️ Arrêt de l\'envoi: quota SendGrid dépassé', [
                    'certificates_sent' => $certificatesSent,
                    'certificates_pending' => $evenement->getParticipations()->count() - $certificatesSent - $certificatesFailed,
                ]);
                break;
            }
            
            // Vérifier que la participation est acceptée
            if ($participation->getStatut() !== \App\Enum\StatutParticipation::ACCEPTE) {
                $this->logger->debug('Participation ignorée pour certificat (non acceptée)', [
                    'participation_id' => $participation->getId(),
                    'statut' => $participation->getStatut()->value,
                ]);
                continue;
            }
            
            $equipe = $participation->getEquipe();
            
            // Envoyer un certificat à chaque étudiant de l'équipe
            foreach ($equipe->getEtudiants() as $etudiant) {
                // Si le quota est dépassé, arrêter l'envoi
                if ($quotaExceeded) {
                    break;
                }
                
                try {
                    $this->emailService->sendCertificate(
                        $etudiant->getEmail(),
                        $etudiant->getPrenom(),
                        $etudiant->getNom(),
                        $evenement->getTitre(),
                        $evenement->getType()->value,
                        $evenement->getDateDebut()
                    );
                    
                    $certificatesSent++;
                    
                    $this->logger->info('✓ Certificat envoyé', [
                        'evenement_id' => $evenement->getId(),
                        'student_email' => $etudiant->getEmail(),
                        'student_name' => $etudiant->getPrenom() . ' ' . $etudiant->getNom(),
                    ]);
                    
                    // Petit délai pour éviter le rate limiting (50ms)
                    usleep(50000);
                    
                } catch (\Exception $e) {
                    $certificatesFailed++;
                    
                    // Détecter si c'est une erreur de quota (code 403)
                    if (strpos($e->getMessage(), '403') !== false || 
                        strpos($e->getMessage(), 'exceeded') !== false ||
                        strpos($e->getMessage(), 'limit') !== false) {
                        $quotaExceeded = true;
                        $this->logger->error('❌ QUOTA SENDGRID DÉPASSÉ', [
                            'evenement_id' => $evenement->getId(),
                            'student_email' => $etudiant->getEmail(),
                            'error' => $e->getMessage(),
                            'solution' => 'Vérifiez votre plan SendGrid ou attendez le renouvellement du quota',
                        ]);
                    } else {
                        $this->logger->error('✗ Erreur envoi certificat', [
                            'evenement_id' => $evenement->getId(),
                            'student_email' => $etudiant->getEmail(),
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                }
            }
        }
        
        $this->logger->info('🎓 Envoi des certificats terminé', [
            'evenement_id' => $evenement->getId(),
            'certificates_sent' => $certificatesSent,
            'certificates_failed' => $certificatesFailed,
            'quota_exceeded' => $quotaExceeded,
        ]);
        
        // Si le quota est dépassé, logger un message d'avertissement
        if ($quotaExceeded) {
            $this->logger->warning('⚠️ ATTENTION: Certains certificats n\'ont pas pu être envoyés à cause du quota SendGrid', [
                'evenement_id' => $evenement->getId(),
                'certificates_sent' => $certificatesSent,
                'certificates_failed' => $certificatesFailed,
                'action_requise' => 'Relancez la commande php bin/console app:send-certificates manuellement après renouvellement du quota',
            ]);
        }
    }

    /**
     * Guards: Conditions pour autoriser ou bloquer une transition
     */
    public function onGuard(GuardEvent $event): void
    {
        /** @var Evenement $evenement */
        $evenement = $event->getSubject();
        $transition = $event->getTransition()->getName();
        
        // Exemple: Empêcher de démarrer un événement si la date n'est pas encore arrivée
        if ($transition === 'demarrer') {
            $now = new \DateTime();
            if ($evenement->getDateDebut() > $now) {
                $event->setBlocked(true, 'La date de début n\'est pas encore arrivée');
                $this->logger->warning('Transition bloquée: événement pas encore commencé', [
                    'evenement_id' => $evenement->getId(),
                    'date_debut' => $evenement->getDateDebut()->format('Y-m-d H:i:s'),
                ]);
            }
        }
        
        // Exemple: Empêcher de terminer un événement si la date de fin n'est pas passée
        if ($transition === 'terminer') {
            $now = new \DateTime();
            if ($evenement->getDateFin() >= $now) {
                $event->setBlocked(true, 'La date de fin n\'est pas encore passée');
                $this->logger->warning('Transition bloquée: événement pas encore terminé', [
                    'evenement_id' => $evenement->getId(),
                    'date_fin' => $evenement->getDateFin()->format('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
