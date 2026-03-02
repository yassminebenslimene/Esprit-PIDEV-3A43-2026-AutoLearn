# 📋 GUIDE DÉTAILLÉ - TOUS LES FICHIERS DU MODULE ÉVÉNEMENT

## 🎯 TABLE DES MATIÈRES

1. [Entités (Models)](#entités)
2. [Contrôleurs (Controllers)](#contrôleurs)
3. [Services](#services)
4. [EventSubscribers](#eventsubscribers)
5. [Commandes Console](#commandes)
6. [Formulaires](#formulaires)
7. [Templates](#templates)
8. [Configuration](#configuration)
9. [Enums](#enums)

---

## 📦 1. ENTITÉS (Models)

### 📄 `src/Entity/Evenement.php`

**Rôle** : Représente un événement dans la base de données

**Table SQL** : `evenement`

**Propriétés principales** :
```php
private ?int $id;                      // Clé primaire auto-incrémentée
private string $titre;                 // Titre de l'événement (min 3, max 255 caractères)
private string $lieu;                  // Lieu de l'événement
private string $description;           // Description détaillée (min 10, max 2000 caractères)
private TypeEvenement $type;           // ENUM: Conference, Atelier, Hackathon, Seminaire, Competition
private \DateTimeInterface $dateDebut; // Date et heure de début
private \DateTimeInterface $dateFin;   // Date et heure de fin
private StatutEvenement $status;       // ENUM: Planifié, En cours, Passé, Annulé
private bool $isCanceled;              // Événement annulé ? (true/false)
private string $workflowStatus;        // État workflow: planifie, en_cours, termine, annule
private int $nbMax;                    // Nombre maximum d'équipes (min 1, max 100)
```

**Méthodes importantes** :
- `updateStatus()` : Met à jour automatiquement le statut en fonction des dates
- `areParticipationsOpen()` : Vérifie si les participations sont ouvertes
- `syncStatusFromWorkflow()` : Synchronise le statut enum avec le workflow

**Validations** :
- Titre obligatoire (3-255 caractères)
- Date début doit être dans le futur
- Date fin doit être après date début
- Nombre max d'équipes entre 1 et 100

**Lignes de code** : 1-200

---

### 📄 `src/Entity/Participation.php`

**Rôle** : Représente la participation d'une équipe à un événement

**Table SQL** : `participation`

**Propriétés** :
```php
private ?int $id;                           // Clé primaire
private Equipe $equipe;                     // Relation ManyToOne avec Equipe
private Evenement $evenement;               // Relation ManyToOne avec Evenement
private StatutParticipation $statut;        // ENUM: EN_ATTENTE, ACCEPTE, REFUSE
private ?array $feedbacks;                  // JSON: Feedbacks des étudiants
```

**Méthode clé : `validateParticipation()`**

Cette méthode vérifie automatiquement 3 contraintes :

1. **Événement annulé ?**
   ```php
   if ($evenement->getIsCanceled()) {
       $this->setStatut(StatutParticipation::REFUSE);
       return ['accepted' => false, 'message' => 'Événement annulé'];
   }
   ```

2. **Capacité maximale atteinte ?**
   ```php
   $acceptedCount = 0;
   foreach ($evenement->getParticipations() as $p) {
       if ($p->getStatut() === StatutParticipation::ACCEPTE) {
           $acceptedCount++;
       }
   }
   if ($acceptedCount >= $evenement->getNbMax()) {
       $this->setStatut(StatutParticipation::REFUSE);
       return ['accepted' => false, 'message' => 'Capacité maximale atteinte'];
   }
   ```

3. **Étudiant déjà inscrit ?**
   ```php
   foreach ($evenement->getParticipations() as $p) {
       foreach ($p->getEquipe()->getEtudiants() as $etudiant) {
           foreach ($equipe->getEtudiants() as $membre) {
               if ($etudiant->getId() === $membre->getId()) {
                   $this->setStatut(StatutParticipation::REFUSE);
                   return ['accepted' => false, 'message' => 'Étudiant déjà inscrit'];
               }
           }
       }
   }
   ```

**Gestion des feedbacks** :
- `addFeedback()` : Ajoute un feedback avec rating, sentiment, emoji, commentaire
- `getFeedbackByEtudiant()` : Récupère le feedback d'un étudiant
- `getAverageFeedbackScore()` : Calcule la moyenne des feedbacks
- `getSentimentDistribution()` : Distribution des sentiments (très satisfait, satisfait, etc.)

**Lignes de code** : 1-250

---

### 📄 `src/Entity/Equipe.php`

**Rôle** : Représente une équipe participant à un événement

**Table SQL** : `equipe`

**Propriétés** :
```php
private ?int $id;                      // Clé primaire
private string $nom;                   // Nom de l'équipe
private Evenement $evenement;          // Relation ManyToOne avec Evenement
private Collection $etudiants;         // Relation ManyToMany avec Etudiant (4-6 étudiants)
```

**Contrainte importante** :
```php
#[Assert\Count(
    min: 4, max: 6, 
    minMessage: "Une équipe doit avoir au moins {{ limit }} étudiants",
    maxMessage: "Une équipe ne peut pas avoir plus de {{ limit }} étudiants"
)]
```

**Lignes de code** : 1-80

---

## 🎮 2. CONTRÔLEURS (Controllers)

### 📄 `src/Controller/EvenementController.php`

**Rôle** : Gestion des événements dans le backoffice (admin)

**Route de base** : `/backoffice/evenement`

**Actions disponibles** :

| Méthode | Route | HTTP | Description |
|---------|-------|------|-------------|
| `index()` | `/` | GET | Liste tous les événements |
| `new()` | `/new` | GET/POST | Créer un nouvel événement |
| `show()` | `/{id}` | GET | Afficher un événement |
| `edit()` | `/{id}/edit` | GET/POST | Modifier un événement |
| `delete()` | `/{id}/delete` | GET | Supprimer un événement |
| `annuler()` | `/{id}/annuler` | POST | Annuler un événement via workflow |

**Injection de dépendances** :
```php
public function __construct(
    private WorkflowInterface $evenementPublishingStateMachine
) {}
```

**Exemple : Méthode `annuler()`**
```php
#[Route('/{id}/annuler', name: 'backoffice_evenement_annuler', methods: ['POST'])]
public function annuler(Evenement $evenement, EntityManagerInterface $entityManager): Response
{
    // 1. Vérifier si la transition est possible
    if (!$this->evenementPublishingStateMachine->can($evenement, 'annuler')) {
        $this->addFlash('error', 'Impossible d\'annuler cet événement');
        return $this->redirectToRoute('backoffice_evenements');
    }
    
    // 2. Appliquer la transition via le workflow
    $this->evenementPublishingStateMachine->apply($evenement, 'annuler');
    
    // 3. Marquer comme annulé
    $evenement->setIsCanceled(true);
    
    // 4. Sauvegarder
    $entityManager->flush();
    
    $this->addFlash('success', 'Événement annulé avec succès');
    return $this->redirectToRoute('backoffice_evenements');
}
```

**Routes AI** :
- `/ai/generate-analysis` : Génère un rapport d'analyse AI
- `/ai/generate-recommendations` : Génère des recommandations AI
- `/ai/generate-improvements` : Génère des suggestions d'amélioration

**Lignes de code** : 1-250

---


### 📄 `src/Controller/FrontofficeEvenementController.php`

**Rôle** : Gestion des événements côté utilisateur (frontoffice)

**Route de base** : `/frontoffice/evenement`

**Actions** :
- `index()` : Liste des événements disponibles
- `show()` : Détails d'un événement
- `participate()` : Formulaire de participation
- `calendar()` : Vue calendrier des événements

**Lignes de code** : 1-150

---

### 📄 `src/Controller/ParticipationController.php`

**Rôle** : Gestion des participations dans le backoffice

**Route de base** : `/backoffice/participation`

**Actions** :
- `index()` : Liste toutes les participations
- `show()` : Détails d'une participation
- `edit()` : Modifier le statut d'une participation
- `delete()` : Supprimer une participation

**Lignes de code** : 1-120

---

### 📄 `src/Controller/FrontofficeParticipationController.php`

**Rôle** : Gestion des participations côté utilisateur

**Route de base** : `/frontoffice/participation`

**Actions importantes** :

1. **`new()` - Créer une participation**
   ```php
   public function new(Request $request, EntityManagerInterface $entityManager): Response
   {
       $participation = new Participation();
       $form = $this->createForm(ParticipationFrontType::class, $participation);
       $form->handleRequest($request);
       
       if ($form->isSubmitted() && $form->isValid()) {
           // Validation automatique
           $result = $participation->validateParticipation();
           
           if ($result['accepted']) {
               $entityManager->persist($participation);
               $entityManager->flush();
               
               // Envoyer email de confirmation
               $this->emailService->sendParticipationConfirmation(...);
               
               $this->addFlash('success', $result['message']);
           } else {
               $this->addFlash('error', $result['message']);
           }
       }
       
       return $this->render('frontoffice/participation/new.html.twig', ['form' => $form]);
   }
   ```

2. **`mesParticipations()` - Mes participations**
   - Affiche toutes les participations de l'utilisateur connecté
   - Filtre par statut (accepté, en attente, refusé)

**Lignes de code** : 1-180

---

### 📄 `src/Controller/FeedbackController.php`

**Rôle** : Gestion des feedbacks des participants

**Route de base** : `/frontoffice/feedback`

**Actions** :
- `form()` : Formulaire de feedback avec rating Kahoot-style
- `submit()` : Soumettre un feedback avec sentiment, emoji, commentaire

**Lignes de code** : 1-100

---

## 🛠️ 3. SERVICES

### 📄 `src/Service/EmailService.php`

**Rôle** : Service centralisé pour l'envoi d'emails via SendGrid

**Configuration** :
```php
private string $fromEmail = 'autolearnplateforme@gmail.com';
private string $fromName = 'Autolearn Platform';
```

**Méthodes disponibles** :

#### 1. `sendParticipationConfirmation()`
**Quand** : Après qu'une participation est acceptée

**Contenu de l'email** :
- Email HTML avec design professionnel
- QR code généré via API externe (https://api.qrserver.com)
- Badge PDF (généré par BadgeService)
- Fichier .ics pour ajouter l'événement au calendrier

**Code** :
```php
public function sendParticipationConfirmation(
    string $toEmail,
    string $studentFirstName,
    string $studentLastName,
    string $teamName,
    string $eventName,
    \DateTimeInterface $eventDate,
    string $eventLocation,
    int $participationId
): void {
    // 1. Générer le QR code
    $qrContent = "EVENT PARTICIPATION\nPARTICIPANT: $studentName\nTEAM: $teamName...";
    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrContent);
    $qrCodeData = @file_get_contents($qrCodeUrl);
    
    // 2. Générer le badge PDF
    $badgePdf = $this->badgeService->generateBadge(...);
    
    // 3. Générer le fichier .ics
    $icsContent = $this->generateIcsFile($eventName, $eventDate, $eventLocation);
    
    // 4. Créer l'email
    $email = (new Email())
        ->from(new Address($this->fromEmail, $this->fromName))
        ->to($toEmail)
        ->subject('Participation Confirmed - ' . $eventName)
        ->html($html)
        ->addPart(new DataPart($icsContent, 'event.ics', 'text/calendar'))
        ->addPart(new DataPart($badgePdf, 'event-badge.pdf', 'application/pdf'))
        ->addPart(new DataPart($qrCodeData, 'qrcode.png', 'image/png'));
    
    // 5. Envoyer
    $this->mailer->send($email);
}
```

**Lignes de code** : 60-140

---

#### 2. `sendEventCancellation()`
**Quand** : Quand un événement est annulé via le workflow

**Contenu** :
- Email HTML informant de l'annulation
- Détails de l'événement annulé

**Lignes de code** : 145-165

---

#### 3. `sendEventStarted()`
**Quand** : Quand un événement démarre (transition workflow: planifie → en_cours)

**Contenu** :
- Email HTML "🚀 Event Started"
- Rappel des détails de l'événement

**Lignes de code** : 170-190

---

#### 4. `sendCertificate()`
**Quand** : Quand un événement se termine (transition workflow: en_cours → termine)

**Contenu** :
- Email HTML de félicitations
- Certificat PDF en pièce jointe (généré par CertificateService)

**Code** :
```php
public function sendCertificate(
    string $toEmail,
    string $studentFirstName,
    string $studentLastName,
    string $eventName,
    string $eventType,
    \DateTimeInterface $eventDate
): void {
    // 1. Générer le certificat PDF
    $pdfContent = $this->certificateService->generateCertificate(
        $studentFirstName,
        $studentLastName,
        $eventName,
        $eventType,
        $eventDate
    );
    
    // 2. Créer l'email
    $email = (new Email())
        ->from(new Address($this->fromEmail, $this->fromName))
        ->to($toEmail)
        ->subject('Your Certificate - ' . $eventName)
        ->html($html)
        ->addPart(new DataPart($pdfContent, 'certificate.pdf', 'application/pdf'));
    
    // 3. Envoyer
    $this->mailer->send($email);
}
```

**Lignes de code** : 195-240

---

#### 5. `sendEventReminder()`
**Quand** : 3 jours avant l'événement (via commande cron)

**Contenu** :
- Email de rappel
- Détails de l'événement

**Lignes de code** : 245-265

---

#### 6. `generateIcsFile()`
**Rôle** : Génère un fichier .ics pour ajouter l'événement au calendrier

**Format** : iCalendar (RFC 5545)

**Code** :
```php
private function generateIcsFile(
    string $eventName,
    \DateTimeInterface $eventDate,
    string $location
): string {
    $dtStart = $eventDate->format('Ymd\THis');
    $dtEnd = (clone $eventDate)->modify('+2 hours')->format('Ymd\THis');
    
    return <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Autolearn Platform//Event//EN
BEGIN:VEVENT
UID:{$now}@autolearn.com
DTSTART:{$dtStart}
DTEND:{$dtEnd}
SUMMARY:{$eventName}
LOCATION:{$location}
END:VEVENT
END:VCALENDAR
ICS;
}
```

**Lignes de code** : 270-295

---

**Configuration SendGrid** :
- Fichier : `.env.local`
- Variable : `MAILER_DSN=sendgrid+api://API_KEY@default`
- Sender vérifié : `autolearnplateforme@gmail.com`

**Total lignes** : 1-295

---

### 📄 `src/Service/CertificateService.php`

**Rôle** : Génère des certificats PDF professionnels

**Bibliothèque utilisée** : Dompdf

**Méthode principale** :
```php
public function generateCertificate(
    string $studentFirstName,
    string $studentLastName,
    string $eventName,
    string $eventType,
    \DateTimeInterface $eventDate
): string {
    // 1. Configurer Dompdf
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    
    // 2. Générer le HTML du certificat
    $html = $this->getCertificateTemplate(...);
    
    // 3. Convertir en PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    // 4. Retourner le PDF en string
    return $dompdf->output();
}
```

**Design du certificat** :
- Format A4 paysage
- Gradient violet/bleu (thème Autolearn)
- Bordure décorative
- Nom de l'étudiant en grand
- Détails de l'événement
- Signatures (Event Coordinator, Platform Director)

**Lignes de code** : 1-180

---

### 📄 `src/Service/BadgeService.php`

**Rôle** : Génère des badges PDF pour les participants

**Format** : 10cm x 14cm (taille badge standard)

**Méthode principale** :
```php
public function generateBadge(
    string $studentFirstName,
    string $studentLastName,
    string $teamName,
    string $eventName,
    \DateTimeInterface $eventDate
): string {
    $dompdf = new Dompdf($options);
    $html = $this->getBadgeTemplate(...);
    $dompdf->loadHtml($html);
    $dompdf->setPaper([0, 0, 283.46, 396.85], 'portrait'); // 10cm x 14cm
    $dompdf->render();
    return $dompdf->output();
}
```

**Design du badge** :
- Gradient violet/bleu en fond
- Logo AUTOLEARN en haut
- Nom de l'étudiant en grand
- Nom de l'équipe
- Nom de l'événement
- Date de l'événement

**Lignes de code** : 1-150

---

### 📄 `src/Service/FeedbackAnalyticsService.php`

**Rôle** : Analyse les feedbacks et génère des statistiques

**Méthodes** :

1. **`analyzeByEventType()`** : Statistiques par type d'événement
   ```php
   return [
       'Conference' => [
           'total_feedbacks' => 45,
           'average_rating' => 4.2,
           'sentiment_distribution' => [...]
       ],
       'Hackathon' => [...]
   ];
   ```

2. **`getTopRatedEvents()`** : Top 10 des événements les mieux notés

3. **`getSentimentTrends()`** : Évolution des sentiments dans le temps

**Lignes de code** : 1-200

---

### 📄 `src/Service/AIReportService.php`

**Rôle** : Génère des rapports AI via Hugging Face (Mistral-7B)

**Configuration** :
- API Key : `HUGGINGFACE_API_KEY` dans `.env.local`
- Modèle : `mistralai/Mistral-7B-Instruct-v0.2`

**Méthodes** :

1. **`generateAnalysisReport()`** : Analyse complète des feedbacks
2. **`generateEventRecommendations()`** : Recommandations pour améliorer les événements
3. **`generateImprovementSuggestions()`** : Suggestions d'amélioration

**Lignes de code** : 1-250

---

## 🎧 4. EVENTSUBSCRIBERS

### 📄 `src/EventSubscriber/EvenementWorkflowSubscriber.php`

**Rôle** : Écoute les transitions du workflow et exécute des actions automatiques

**C'EST ICI QUE L'ENVOI AUTOMATIQUE DES EMAILS EST CONFIGURÉ** ✅

**Événements écoutés** :
```php
public static function getSubscribedEvents(): array
{
    return [
        'workflow.evenement_publishing.transition' => 'onTransition',
        'workflow.evenement_publishing.entered.en_cours' => 'onEnCours',
        'workflow.evenement_publishing.entered.termine' => 'onTermine',
        'workflow.evenement_publishing.entered.annule' => 'onAnnule',
        'workflow.evenement_publishing.guard' => 'onGuard',
    ];
}
```

---

#### Méthode 1 : `onTransition()`
**Quand** : À chaque transition du workflow

**Rôle** : Logger l'historique complet

**Code** :
```php
public function onTransition(Event $event): void
{
    $evenement = $event->getSubject();
    $transition = $event->getTransition();
    $user = $this->security?->getUser();
    $username = $user ? $user->getUserIdentifier() : 'SYSTEM';
    
    $this->logger->info('Transition d\'événement', [
        'evenement_id' => $evenement->getId(),
        'evenement_titre' => $evenement->getTitre(),
        'transition' => $transition->getName(),
        'from' => $transition->getFroms(),
        'to' => $transition->getTos(),
        'user' => $username,
        'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
    ]);
}
```

**Lignes de code** : 40-65

---

#### Méthode 2 : `onEnCours()`
**Quand** : Quand un événement démarre (transition: planifie → en_cours)

**Rôle** : Envoyer un email "Event Started" à tous les participants

**Code** :
```php
public function onEnCours(Event $event): void
{
    $evenement = $event->getSubject();
    
    $this->logger->info('🚀 Événement démarré', [
        'evenement_id' => $evenement->getId(),
        'titre' => $evenement->getTitre(),
    ]);
    
    // Envoyer email à tous les membres des équipes participantes
    $this->sendEmailsToParticipants($evenement, 'started');
}
```

**Lignes de code** : 100-115

---

#### Méthode 3 : `onTermine()`
**Quand** : Quand un événement se termine (transition: en_cours → termine)

**Rôle** : Envoyer automatiquement les certificats de participation

**Code** :
```php
public function onTermine(Event $event): void
{
    $evenement = $event->getSubject();
    
    $this->logger->info('✅ Événement terminé', [
        'evenement_id' => $evenement->getId(),
        'titre' => $evenement->getTitre(),
    ]);
    
    // Envoyer automatiquement les certificats de participation
    $this->sendCertificatesToParticipants($evenement);
}
```

**Lignes de code** : 120-135

---

#### Méthode 4 : `onAnnule()`
**Quand** : Quand un événement est annulé (transition: planifie/en_cours → annule)

**Rôle** : Envoyer un email d'annulation à tous les participants

**Code** :
```php
public function onAnnule(Event $event): void
{
    $evenement = $event->getSubject();
    
    $this->logger->warning('❌ Événement annulé', [
        'evenement_id' => $evenement->getId(),
        'titre' => $evenement->getTitre(),
    ]);
    
    // Envoyer email d'annulation à tous les membres des équipes participantes
    $this->sendEmailsToParticipants($evenement, 'cancelled');
}
```

**Lignes de code** : 140-155

---

#### Méthode 5 : `sendEmailsToParticipants()`
**Rôle** : Envoie des emails à tous les participants (started ou cancelled)

**Code détaillé** :
```php
private function sendEmailsToParticipants(Evenement $evenement, string $type): void
{
    $emailsSent = 0;
    $emailsFailed = 0;
    
    // Récupérer toutes les participations acceptées
    foreach ($evenement->getParticipations() as $participation) {
        // Vérifier que la participation est acceptée
        if ($participation->getStatut() !== StatutParticipation::ACCEPTE) {
            continue;
        }
        
        $equipe = $participation->getEquipe();
        $teamName = $equipe->getNom();
        
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
                $this->logger->info('Email envoyé', ['student_email' => $email]);
                
            } catch (\Exception $e) {
                $emailsFailed++;
                $this->logger->error('Erreur envoi email', ['error' => $e->getMessage()]);
            }
        }
    }
    
    $this->logger->info('Envoi d\'emails terminé', [
        'emails_sent' => $emailsSent,
        'emails_failed' => $emailsFailed,
    ]);
}
```

**Lignes de code** : 160-220

---


#### Méthode 6 : `sendCertificatesToParticipants()`
**Rôle** : Envoie automatiquement les certificats quand un événement se termine

**GESTION DU QUOTA SENDGRID** ✅

**Code détaillé** :
```php
private function sendCertificatesToParticipants(Evenement $evenement): void
{
    $certificatesSent = 0;
    $certificatesFailed = 0;
    $quotaExceeded = false;  // ← Variable pour détecter le quota dépassé
    
    $this->logger->info('🎓 Début envoi des certificats', [
        'evenement_id' => $evenement->getId(),
        'nb_participations' => $evenement->getParticipations()->count(),
    ]);
    
    // Récupérer toutes les participations acceptées
    foreach ($evenement->getParticipations() as $participation) {
        // Si le quota est dépassé, arrêter l'envoi
        if ($quotaExceeded) {
            $this->logger->warning('⚠️ Arrêt de l\'envoi: quota SendGrid dépassé');
            break;
        }
        
        // Vérifier que la participation est acceptée
        if ($participation->getStatut() !== StatutParticipation::ACCEPTE) {
            continue;
        }
        
        $equipe = $participation->getEquipe();
        
        // Envoyer un certificat à chaque étudiant de l'équipe
        foreach ($equipe->getEtudiants() as $etudiant) {
            if ($quotaExceeded) break;
            
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
                    'student_email' => $etudiant->getEmail(),
                ]);
                
                // Petit délai pour éviter le rate limiting (50ms)
                usleep(50000);
                
            } catch (\Exception $e) {
                $certificatesFailed++;
                
                // ⚠️ DÉTECTION DU QUOTA DÉPASSÉ ⚠️
                if (strpos($e->getMessage(), '403') !== false || 
                    strpos($e->getMessage(), 'exceeded') !== false ||
                    strpos($e->getMessage(), 'limit') !== false) {
                    
                    $quotaExceeded = true;  // ← Marquer le quota comme dépassé
                    
                    $this->logger->error('❌ QUOTA SENDGRID DÉPASSÉ', [
                        'evenement_id' => $evenement->getId(),
                        'student_email' => $etudiant->getEmail(),
                        'error' => $e->getMessage(),
                        'solution' => 'Vérifiez votre plan SendGrid ou attendez le renouvellement du quota',
                    ]);
                } else {
                    $this->logger->error('✗ Erreur envoi certificat', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
    
    $this->logger->info('🎓 Envoi des certificats terminé', [
        'certificates_sent' => $certificatesSent,
        'certificates_failed' => $certificatesFailed,
        'quota_exceeded' => $quotaExceeded,
    ]);
    
    // Si le quota est dépassé, logger un message d'avertissement
    if ($quotaExceeded) {
        $this->logger->warning('⚠️ ATTENTION: Certains certificats n\'ont pas pu être envoyés', [
            'action_requise' => 'Relancez la commande php bin/console app:send-certificates',
        ]);
    }
}
```

**Explication de la gestion du quota** :

1. **Variable `$quotaExceeded`** : Initialisée à `false`, devient `true` si le quota est dépassé

2. **Détection de l'erreur 403** :
   ```php
   if (strpos($e->getMessage(), '403') !== false || 
       strpos($e->getMessage(), 'exceeded') !== false ||
       strpos($e->getMessage(), 'limit') !== false)
   ```
   - Cherche "403" dans le message d'erreur
   - Cherche "exceeded" (dépassé)
   - Cherche "limit" (limite)

3. **Arrêt immédiat** :
   ```php
   if ($quotaExceeded) {
       $this->logger->warning('⚠️ Arrêt de l\'envoi: quota SendGrid dépassé');
       break;
   }
   ```
   - Dès que le quota est détecté comme dépassé, on arrête l'envoi
   - Évite de spammer les logs avec des centaines d'erreurs identiques

4. **Logging détaillé** :
   - Log chaque certificat envoyé avec succès
   - Log chaque erreur avec le message complet
   - Log un résumé final avec le nombre de certificats envoyés/échoués

**Lignes de code** : 225-330

---

#### Méthode 7 : `onGuard()`
**Rôle** : Conditions pour autoriser ou bloquer une transition

**Code** :
```php
public function onGuard(GuardEvent $event): void
{
    $evenement = $event->getSubject();
    $transition = $event->getTransition()->getName();
    
    // Empêcher de démarrer un événement si la date n'est pas encore arrivée
    if ($transition === 'demarrer') {
        $now = new \DateTime();
        if ($evenement->getDateDebut() > $now) {
            $event->setBlocked(true, 'La date de début n\'est pas encore arrivée');
            $this->logger->warning('Transition bloquée: événement pas encore commencé');
        }
    }
    
    // Empêcher de terminer un événement si la date de fin n'est pas passée
    if ($transition === 'terminer') {
        $now = new \DateTime();
        if ($evenement->getDateFin() >= $now) {
            $event->setBlocked(true, 'La date de fin n\'est pas encore passée');
            $this->logger->warning('Transition bloquée: événement pas encore terminé');
        }
    }
}
```

**Lignes de code** : 335-360

---

**Total lignes** : 1-360

**Injection de dépendances** :
```php
public function __construct(
    private LoggerInterface $logger,
    private EmailService $emailService,
    private ?Security $security = null
) {}
```

---

### 📄 `src/EventSubscriber/CalendarSubscriber.php`

**Rôle** : Fournit les événements au CalendarBundle pour l'affichage calendrier

**Événement écouté** :
```php
public static function getSubscribedEvents(): array
{
    return [
        CalendarEvents::SET_DATA => 'onCalendarSetData',
    ];
}
```

**Méthode** :
```php
public function onCalendarSetData(CalendarEvent $calendar): void
{
    $start = $calendar->getStart();
    $end = $calendar->getEnd();
    
    // Récupérer les événements dans la période
    $evenements = $this->evenementRepository->createQueryBuilder('e')
        ->where('e.dateDebut BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery()
        ->getResult();
    
    // Ajouter chaque événement au calendrier
    foreach ($evenements as $evenement) {
        $calendarEvent = new Event(
            $evenement->getTitre(),
            $evenement->getDateDebut(),
            $evenement->getDateFin()
        );
        
        // Couleur selon le statut
        $color = match($evenement->getWorkflowStatus()) {
            'planifie' => '#667eea',
            'en_cours' => '#28a745',
            'termine' => '#6c757d',
            'annule' => '#dc3545',
        };
        $calendarEvent->setOptions(['backgroundColor' => $color]);
        
        $calendar->addEvent($calendarEvent);
    }
}
```

**Lignes de code** : 1-80

---

## ⚙️ 5. COMMANDES CONSOLE

### 📄 `src/Command/UpdateEventStatusCommand.php`

**Rôle** : Met à jour automatiquement le statut des événements (cron job)

**Commande** : `php bin/console app:update-event-status`

**Fréquence recommandée** : Toutes les heures

**Fonctionnement** :

1. **Récupère tous les événements non annulés**
   ```php
   $events = $this->evenementRepository->createQueryBuilder('e')
       ->where('e.isCanceled = false')
       ->getQuery()
       ->getResult();
   ```

2. **Pour chaque événement, vérifie les dates**
   ```php
   // Cas 1: Événement planifié qui doit démarrer
   if ($currentStatus === 'planifie' && $now >= $event->getDateDebut()) {
       if ($this->evenementPublishingStateMachine->can($event, 'demarrer')) {
           $this->evenementPublishingStateMachine->apply($event, 'demarrer');
           // ← Ceci déclenche automatiquement l'envoi des emails via EvenementWorkflowSubscriber
           $eventsStarted++;
       }
   }
   
   // Cas 2: Événement en cours qui doit se terminer
   if ($currentStatus === 'en_cours' && $now > $event->getDateFin()) {
       if ($this->evenementPublishingStateMachine->can($event, 'terminer')) {
           $this->evenementPublishingStateMachine->apply($event, 'terminer');
           // ← Ceci déclenche automatiquement l'envoi des certificats via EvenementWorkflowSubscriber
           $eventsCompleted++;
       }
   }
   ```

3. **Affiche un résumé**
   ```
   ┌─────────────────────────┬────────┐
   │ Statistique             │ Valeur │
   ├─────────────────────────┼────────┤
   │ Événements traités      │ 25     │
   │ Événements mis à jour   │ 3      │
   │ Événements démarrés     │ 1      │
   │ Événements terminés     │ 2      │
   └─────────────────────────┴────────┘
   ```

**Configuration cron** :
```bash
# Exécuter toutes les heures
0 * * * * cd /path/to/autolearn && php bin/console app:update-event-status
```

**Lignes de code** : 1-120

---

### 📄 `src/Command/SendCertificatesCommand.php`

**Rôle** : Envoie manuellement les certificats pour les événements terminés

**Commande** : `php bin/console app:send-certificates`

**Utilisation** : Quand le quota SendGrid est dépassé et qu'on veut renvoyer les certificats manquants

**Fonctionnement** :
```php
protected function execute(InputInterface $input, OutputInterface $output): int
{
    // Récupérer tous les événements terminés
    $events = $this->evenementRepository->findBy(['workflowStatus' => 'termine']);
    
    foreach ($events as $event) {
        // Envoyer les certificats à tous les participants
        foreach ($event->getParticipations() as $participation) {
            if ($participation->getStatut() === StatutParticipation::ACCEPTE) {
                foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
                    try {
                        $this->emailService->sendCertificate(...);
                        $io->success('✓ Certificat envoyé à ' . $etudiant->getEmail());
                    } catch (\Exception $e) {
                        $io->error('✗ Erreur pour ' . $etudiant->getEmail());
                    }
                }
            }
        }
    }
    
    return Command::SUCCESS;
}
```

**Lignes de code** : 1-100

---

### 📄 `src/Command/SendEventRemindersCommand.php`

**Rôle** : Envoie des rappels 3 jours avant les événements

**Commande** : `php bin/console app:send-event-reminders`

**Fréquence recommandée** : Une fois par jour

**Fonctionnement** :
```php
// Récupérer les événements dans 3 jours
$targetDate = (new \DateTime())->modify('+3 days');

$events = $this->evenementRepository->createQueryBuilder('e')
    ->where('DATE(e.dateDebut) = :targetDate')
    ->andWhere('e.workflowStatus = :status')
    ->setParameter('targetDate', $targetDate->format('Y-m-d'))
    ->setParameter('status', 'planifie')
    ->getQuery()
    ->getResult();

foreach ($events as $event) {
    // Envoyer un rappel à tous les participants
    foreach ($event->getParticipations() as $participation) {
        if ($participation->getStatut() === StatutParticipation::ACCEPTE) {
            foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
                $this->emailService->sendEventReminder(...);
            }
        }
    }
}
```

**Configuration cron** :
```bash
# Exécuter tous les jours à 9h du matin
0 9 * * * cd /path/to/autolearn && php bin/console app:send-event-reminders
```

**Lignes de code** : 1-90

---

### 📄 `src/Command/CleanupCancelledEventsCommand.php`

**Rôle** : Nettoie les événements annulés anciens (> 6 mois)

**Commande** : `php bin/console app:cleanup-cancelled-events`

**Fréquence recommandée** : Une fois par mois

**Lignes de code** : 1-80

---

## 📝 6. FORMULAIRES

### 📄 `src/Form/EvenementType.php`

**Rôle** : Formulaire de création/modification d'événement (backoffice)

**Champs** :
```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('titre', TextType::class, [
            'label' => 'Titre de l\'événement',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Hackathon 2026']
        ])
        ->add('lieu', TextType::class, [
            'label' => 'Lieu',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: ESPRIT']
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'attr' => ['class' => 'form-control', 'rows' => 5]
        ])
        ->add('type', EnumType::class, [
            'class' => TypeEvenement::class,
            'label' => 'Type d\'événement',
            'attr' => ['class' => 'form-select']
        ])
        ->add('dateDebut', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Date de début',
            'attr' => ['class' => 'form-control']
        ])
        ->add('dateFin', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Date de fin',
            'attr' => ['class' => 'form-control']
        ])
        ->add('nbMax', IntegerType::class, [
            'label' => 'Nombre maximum d\'équipes',
            'attr' => ['class' => 'form-control', 'min' => 1, 'max' => 100]
        ]);
}
```

**Lignes de code** : 1-60

---

### 📄 `src/Form/ParticipationFrontType.php`

**Rôle** : Formulaire de participation (frontoffice)

**Champs** :
```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('evenement', EntityType::class, [
            'class' => Evenement::class,
            'choice_label' => 'titre',
            'query_builder' => function (EvenementRepository $er) {
                return $er->createQueryBuilder('e')
                    ->where('e.workflowStatus IN (:statuses)')
                    ->andWhere('e.isCanceled = false')
                    ->setParameter('statuses', ['planifie', 'en_cours'])
                    ->orderBy('e.dateDebut', 'ASC');
            },
            'label' => 'Événement',
            'attr' => ['class' => 'form-select']
        ])
        ->add('equipe', EntityType::class, [
            'class' => Equipe::class,
            'choice_label' => 'nom',
            'label' => 'Équipe',
            'attr' => ['class' => 'form-select']
        ]);
}
```

**Lignes de code** : 1-50

---

## 🎨 7. TEMPLATES

### 📄 `templates/backoffice/evenement/index.html.twig`

**Rôle** : Liste des événements dans le backoffice

**Fonctionnalités** :
- Tableau avec tous les événements
- Filtres par statut
- Boutons d'action (Voir, Modifier, Supprimer, Annuler)
- Section AI avec statistiques et rapports

**Lignes de code** : 1-200

---

### 📄 `templates/frontoffice/evenement/index.html.twig`

**Rôle** : Liste des événements côté utilisateur

**Fonctionnalités** :
- Cards avec design moderne
- Filtres par type et statut
- Bouton "Participer"
- Affichage du nombre de places restantes

**Lignes de code** : 1-150

---

### 📄 `templates/frontoffice/evenement/calendar.html.twig`

**Rôle** : Vue calendrier des événements

**Bibliothèque** : FullCalendar.js

**Configuration** :
```javascript
var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'fr',
    events: '{{ path('calendar_feed') }}',  // ← Route qui appelle CalendarSubscriber
    eventClick: function(info) {
        window.location.href = '/frontoffice/evenement/' + info.event.id;
    }
});
```

**Lignes de code** : 1-100

---

### 📄 `templates/emails/participation_confirmation.html.twig`

**Rôle** : Template HTML de l'email de confirmation de participation

**Design** :
- Gradient violet/bleu
- Logo Autolearn
- Détails de la participation
- QR code intégré
- Boutons d'action

**Lignes de code** : 1-150

---

### 📄 `templates/emails/event_cancelled.html.twig`

**Rôle** : Template HTML de l'email d'annulation

**Lignes de code** : 1-100

---

### 📄 `templates/emails/event_started.html.twig`

**Rôle** : Template HTML de l'email "Event Started"

**Lignes de code** : 1-100

---

### 📄 `templates/frontoffice/feedback/form.html.twig`

**Rôle** : Formulaire de feedback style Kahoot

**Fonctionnalités** :
- Rating par étoiles (1-5)
- Sélection de sentiment avec emojis
- Rating par catégories (organisation, contenu, lieu, animation)
- Commentaire libre

**Lignes de code** : 1-200

---

## ⚙️ 8. CONFIGURATION

### 📄 `config/packages/workflow.yaml`

**Rôle** : Configuration du Workflow Bundle

**Contenu complet** :
```yaml
framework:
    workflows:
        evenement_publishing:
            type: 'state_machine'           # Machine à états (un seul état à la fois)
            audit_trail:
                enabled: true               # Historique des transitions activé
            marking_store:
                type: 'method'
                property: 'workflowStatus'  # Propriété de l'entité qui stocke l'état
            supports:
                - App\Entity\Evenement      # Entité concernée
            initial_marking: planifie       # État initial par défaut
            places:                         # États possibles
                - planifie
                - en_cours
                - termine
                - annule
            transitions:                    # Transitions possibles
                demarrer:
                    from: planifie
                    to: en_cours
                    metadata:
                        title: "Démarrer l'événement"
                        description: "L'événement commence maintenant"
                        color: 'success'
                        icon: 'play'
                terminer:
                    from: en_cours
                    to: termine
                    metadata:
                        title: "Terminer l'événement"
                        description: "L'événement est maintenant terminé"
                        color: 'info'
                        icon: 'check'
                annuler:
                    from: [planifie, en_cours]
                    to: annule
                    metadata:
                        title: "Annuler l'événement"
                        description: "L'événement est annulé"
                        color: 'danger'
                        icon: 'times'
```

**Explication** :
- `type: 'state_machine'` : Un événement ne peut être que dans un seul état à la fois
- `audit_trail: enabled: true` : Symfony enregistre automatiquement l'historique des transitions
- `marking_store: property: 'workflowStatus'` : L'état est stocké dans la propriété `workflowStatus` de l'entité
- `initial_marking: planifie` : Quand un événement est créé, il est automatiquement en état "planifie"

---

### 📄 `config/packages/mailer.yaml`

**Rôle** : Configuration du Mailer (SendGrid)

**Contenu** :
```yaml
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
```

**Variable d'environnement** (`.env.local`) :
```env
MAILER_DSN=sendgrid+api://SG.API_KEY_HERE@default
```

---

### 📄 `config/packages/calendar.yaml`

**Rôle** : Configuration du CalendarBundle

**Contenu** :
```yaml
calendar: ~
```

La configuration se fait principalement dans le `CalendarSubscriber`.

---

### 📄 `config/packages/doctrine.yaml`

**Rôle** : Configuration de Doctrine ORM

**Ajout pour EntityAudit** :
```yaml
doctrine:
    dbal:
        schema_filter: ~^(?!user_audit|revisions)~
```

Cette ligne protège les tables `user_audit` et `revisions` contre la suppression par Doctrine.

---

### 📄 `config/packages/monolog.yaml`

**Rôle** : Configuration des logs

**Contenu** :
```yaml
monolog:
    channels:
        - deprecation
    handlers:
        main:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
```

**Fichier de logs** : `var/log/dev.log`

---

## 🔢 9. ENUMS

### 📄 `src/Enum/TypeEvenement.php`

**Rôle** : Types d'événements possibles

**Valeurs** :
```php
enum TypeEvenement: string
{
    case CONFERENCE = 'Conference';
    case ATELIER = 'Atelier';
    case HACKATHON = 'Hackathon';
    case SEMINAIRE = 'Seminaire';
    case COMPETITION = 'Competition';
}
```

---

### 📄 `src/Enum/StatutEvenement.php`

**Rôle** : Statuts d'événements

**Valeurs** :
```php
enum StatutEvenement: string
{
    case PLANIFIE = 'Planifié';
    case EN_COURS = 'En cours';
    case PASSE = 'Passé';
    case ANNULE = 'Annulé';
}
```

---

### 📄 `src/Enum/StatutParticipation.php`

**Rôle** : Statuts de participations

**Valeurs** :
```php
enum StatutParticipation: string
{
    case EN_ATTENTE = 'En attente';
    case ACCEPTE = 'Accepté';
    case REFUSE = 'Refusé';
}
```

---

## 🔄 FLUX COMPLET : DE LA CRÉATION À L'ENVOI AUTOMATIQUE DES EMAILS

### Scénario : Création d'un événement jusqu'à l'envoi des certificats

```
1. ADMIN crée un événement dans le backoffice
   ↓
   Fichier: EvenementController.php → new()
   ↓
   Formulaire: EvenementType.php
   ↓
   Entité: Evenement.php (workflowStatus = 'planifie')
   ↓
   Base de données: INSERT INTO evenement

2. ÉTUDIANT participe à l'événement
   ↓
   Fichier: FrontofficeParticipationController.php → new()
   ↓
   Formulaire: ParticipationFrontType.php
   ↓
   Validation: Participation.php → validateParticipation()
   ↓
   Si accepté: EmailService.php → sendParticipationConfirmation()
   ↓
   Email envoyé avec QR code + Badge PDF + fichier .ics

3. CRON JOB vérifie les dates (toutes les heures)
   ↓
   Commande: UpdateEventStatusCommand.php
   ↓
   Si dateDebut est passée:
      Workflow: apply($evenement, 'demarrer')
      ↓
      EventSubscriber: EvenementWorkflowSubscriber.php → onEnCours()
      ↓
      EmailService.php → sendEventStarted()
      ↓
      Email "Event Started" envoyé à tous les participants

4. CRON JOB vérifie les dates (toutes les heures)
   ↓
   Commande: UpdateEventStatusCommand.php
   ↓
   Si dateFin est passée:
      Workflow: apply($evenement, 'terminer')
      ↓
      EventSubscriber: EvenementWorkflowSubscriber.php → onTermine()
      ↓
      Méthode: sendCertificatesToParticipants()
      ↓
      Pour chaque participant:
         EmailService.php → sendCertificate()
         ↓
         CertificateService.php → generateCertificate()
         ↓
         Email avec certificat PDF envoyé
         ↓
         Si erreur 403 (quota dépassé):
            $quotaExceeded = true
            break; (arrêt de l'envoi)
```

---

## 📊 RÉSUMÉ DES FICHIERS PAR CATÉGORIE

| Catégorie | Nombre de fichiers | Lignes totales (approx) |
|-----------|-------------------|------------------------|
| Entités | 4 | 600 |
| Contrôleurs | 6 | 900 |
| Services | 5 | 1200 |
| EventSubscribers | 2 | 440 |
| Commandes | 4 | 390 |
| Formulaires | 3 | 170 |
| Templates | 15+ | 2000+ |
| Configuration | 6 | 200 |
| Enums | 3 | 60 |
| **TOTAL** | **48+** | **5960+** |

---

## 🎯 POINTS CLÉS POUR LA VALIDATION

1. **EventSubscriber** : C'est là que l'envoi automatique des emails est configuré
2. **Workflow** : Gère les transitions d'états et déclenche automatiquement les actions
3. **Gestion du quota** : Détection automatique du code 403 et arrêt de l'envoi
4. **Validation automatique** : Les participations sont validées automatiquement selon 3 contraintes
5. **Commandes cron** : Automatisation complète du cycle de vie des événements

---

FIN DU GUIDE DÉTAILLÉ
