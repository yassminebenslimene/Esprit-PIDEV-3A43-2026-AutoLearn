# 🔍 PREUVES DES TÂCHES RÉALISÉES - LOCALISATION DANS LE CODE

**Date:** 23 Février 2026  
**Responsable:** Amira NEFZI

Ce document liste précisément où trouver les preuves de chaque tâche réalisée dans le code source.

---

## 📋 COMMENT UTILISER CE DOCUMENT

Pour chaque tâche réalisée (marquée ✓), vous trouverez:
1. **Fichier(s) concerné(s)** - Où chercher le code
2. **Lignes approximatives** - Section du fichier
3. **Ce qu'il faut chercher** - Éléments clés à vérifier
4. **Fonctionnement** - Explication rapide

---

## US-5.1: Créer un événement (Admin)

### ✓ T-5.1.1: Créer l'entité Evenement
- **Fichier:** `src/Entity/Evenement.php`
- **Chercher:** `class Evenement`
- **Attributs:** id, titre, description, dateDebut, dateFin, lieu, type, statut, nbMax, workflowStatus, isCanceled
- **Relations:** OneToMany avec Participation

### ✓ T-5.1.2: Créer les enums
- **Fichiers:** 
  - `src/Enum/TypeEvenement.php` → cases: CONFERENCE, ATELIER, HACKATHON, SEMINAIRE
  - `src/Enum/StatutEvenement.php` → cases: PLANIFIE, EN_COURS, TERMINE, ANNULE

### ✓ T-5.1.3: Générer migration
- **Dossier:** `migrations/`
- **Chercher:** Fichiers `VersionXXXXXXXXXXXXXX.php`
- **Contenu:** CREATE TABLE evenement, equipe, participation

### ✓ T-5.1.4: Créer formulaire
- **Fichier:** `src/Form/EvenementType.php`
- **Chercher:** `class EvenementType extends AbstractType`
- **Champs:** titre, description, dateDebut, dateFin, lieu, type, nbMax

### ✓ T-5.1.5: Créer contrôleur
- **Fichier:** `src/Controller/EvenementController.php`
- **Chercher:** `#[Route('/backoffice/evenement/new')]`
- **Méthode:** `new(Request $request)`

### ✓ T-5.1.6: Créer template
- **Fichier:** `templates/backoffice/evenement/new.html.twig`
- **Chercher:** `{{ form_start(form) }}`

### ✓ T-5.1.7: Ajouter validations
- **Fichier:** `src/Entity/Evenement.php`
- **Chercher:** `#[Assert\NotBlank]`, `#[Assert\Length]`, `#[Assert\GreaterThan]`

---

## US-5.2: Modifier un événement

### ✓ T-5.2.1: Action edit
- **Fichier:** `src/Controller/EvenementController.php`
- **Chercher:** `#[Route('/backoffice/evenement/{id}/edit')]`
- **Méthode:** `edit(Request $request, Evenement $evenement)`

### ✓ T-5.2.2: Template modification
- **Fichier:** `templates/backoffice/evenement/edit.html.twig`
- **Chercher:** Formulaire d'édition

### ✓ T-5.2.3: Vérifications sécurité
- **Fichier:** `src/Controller/EvenementController.php`
- **Chercher:** `$this->denyAccessUnlessGranted('ROLE_ADMIN')`

---

## US-5.3: Supprimer un événement

### ✓ T-5.3.1: Action delete
- **Fichier:** `src/Controller/EvenementController.php`
- **Chercher:** `#[Route('/backoffice/evenement/{id}/delete')]`

### ✓ T-5.3.2: Confirmation JavaScript
- **Fichier:** `templates/backoffice/evenement/index.html.twig`
- **Chercher:** `onclick="return confirm('Êtes-vous sûr?')"`

### ✓ T-5.3.3: Suppression en cascade
- **Fichier:** `src/Entity/Evenement.php`
- **Chercher:** `cascade: ['remove']` dans la relation OneToMany

---

## US-5.4: Workflow automatique

### ✓ T-5.4.1: Installer symfony/workflow
- **Fichier:** `composer.json`
- **Chercher:** `"symfony/workflow"`

### ✓ T-5.4.2: Configurer workflow
- **Fichier:** `config/packages/workflow.yaml`
- **Chercher:** `evenement_publishing:`, places, transitions

### ✓ T-5.4.3: Ajouter workflowStatus
- **Fichier:** `src/Entity/Evenement.php`
- **Chercher:** `private ?string $workflowStatus`

### ✓ T-5.4.4: Commande mise à jour
- **Fichier:** `src/Command/UpdateEvenementWorkflowCommand.php`
- **Chercher:** `class UpdateEvenementWorkflowCommand extends Command`
- **Logique:** Compare dates avec aujourd'hui, applique transitions

### ✓ T-5.4.5: EventSubscriber
- **Fichier:** `src/EventSubscriber/EvenementWorkflowSubscriber.php`
- **Chercher:** `class EvenementWorkflowSubscriber implements EventSubscriberInterface`
- **Méthodes:** onEnter, onLeave, onTransition

---

## US-5.5: Annuler un événement

### ✓ T-5.5.1: Action annuler
- **Fichier:** `src/Controller/EvenementController.php`
- **Chercher:** `#[Route('/backoffice/evenement/{id}/annuler')]`

### ✓ T-5.5.2: Bouton Annuler
- **Fichier:** `templates/backoffice/evenement/index.html.twig`
- **Chercher:** `<a href="{{ path('app_evenement_annuler') }}"`

### ✓ T-5.5.3: Modal confirmation
- **Fichier:** `templates/backoffice/evenement/index.html.twig`
- **Chercher:** `<div class="modal">`

---

## US-5.9: Créer une équipe

### ✓ T-5.9.1: Créer entité Equipe
- **Fichier:** `src/Entity/Equipe.php`
- **Chercher:** `class Equipe`
- **Attributs:** id, nom, createdAt, membres (ManyToMany)

### ✓ T-5.9.2: Relation ManyToMany
- **Fichiers:** 
  - `src/Entity/Equipe.php` → `#[ORM\ManyToMany(targetEntity: Etudiant::class)]`
  - `src/Entity/Etudiant.php` → `#[ORM\ManyToMany(targetEntity: Equipe::class, mappedBy: 'membres')]`

### ✓ T-5.9.4: Formulaire équipe
- **Fichier:** `src/Form/EquipeType.php`
- **Chercher:** `class EquipeType extends AbstractType`

### ✓ T-5.9.5: Contrôleur Equipe
- **Fichier:** `src/Controller/EquipeController.php`
- **Chercher:** `#[Route('/frontoffice/equipe/new')]`

### ✓ T-5.9.6: Template création
- **Fichier:** `templates/frontoffice/equipe/new.html.twig`

### ✓ T-5.9.7: Validation 4-6 membres
- **Fichier:** `src/Entity/Equipe.php`
- **Chercher:** `#[Assert\Count(min: 4, max: 6)]`

---

## US-5.11: Inscrire équipe à événement

### ✓ T-5.11.1: Créer entité Participation
- **Fichier:** `src/Entity/Participation.php`
- **Chercher:** `class Participation`
- **Attributs:** id, equipe, evenement, statut, dateInscription, feedbacks

### ✓ T-5.11.2: Enum StatutParticipation
- **Fichier:** `src/Enum/StatutParticipation.php`
- **Chercher:** cases: EN_ATTENTE, ACCEPTE, REFUSE

### ✓ T-5.11.4: Contrôleur Participation
- **Fichier:** `src/Controller/ParticipationController.php`
- **Chercher:** `#[Route('/frontoffice/participation/new')]`

### ✓ T-5.11.5: Template participation
- **Fichier:** `templates/frontoffice/evenement/participate.html.twig`

---

## US-5.12: Validation automatique

### ✓ T-5.12.1: Méthode validateParticipation
- **Fichier:** `src/Entity/Participation.php` OU `src/Controller/ParticipationController.php`
- **Chercher:** Logique de validation des contraintes

### ✓ T-5.12.2: Règle événement non annulé
- **Chercher:** `if ($evenement->isCanceled())`

### ✓ T-5.12.3: Règle capacité maximale
- **Chercher:** `if (count($participations) >= $evenement->getNbMax())`

### ✓ T-5.12.4: Règle pas de doublon
- **Chercher:** Vérification qu'un étudiant n'est pas dans 2 équipes

---

## US-5.17: Email de confirmation

### ✓ T-5.17.1: Installer endroid/qr-code
- **Fichier:** `composer.json`
- **Chercher:** `"endroid/qr-code"`

### ✓ T-5.17.2: Service EmailService
- **Fichier:** `src/Service/EmailService.php`
- **Chercher:** `class EmailService`
- **Méthodes:** sendParticipationConfirmation, sendEventCancellation, etc.

### ✓ T-5.17.3: Service BadgeService
- **Fichier:** `src/Service/BadgeService.php`
- **Chercher:** `class BadgeService`
- **Méthode:** generateBadge (génère QR code)

### ✓ T-5.17.4: Template email
- **Fichier:** `templates/emails/participation_confirmation.html.twig`
- **Chercher:** Structure HTML de l'email avec QR code

### ✓ T-5.17.5: Configuration SendGrid
- **Fichier:** `.env.local`
- **Chercher:** `MAILER_DSN=sendgrid://`

---

## US-5.18: Email d'annulation

### ✓ T-5.18.1: Template email annulation
- **Fichier:** `templates/emails/event_cancelled.html.twig`

### ✓ T-5.18.2: Méthode sendEventCancellation
- **Fichier:** `src/Service/EmailService.php`
- **Chercher:** `public function sendEventCancellation`

### ✓ T-5.18.3: Appel dans WorkflowSubscriber
- **Fichier:** `src/EventSubscriber/EvenementWorkflowSubscriber.php`
- **Chercher:** Appel à `emailService->sendEventCancellation()` lors de la transition vers "annulé"

---

## US-5.20: Email de rappel

### ✓ T-5.20.1: Commande SendEventRemindersCommand
- **Fichier:** `src/Command/SendEventRemindersCommand.php`
- **Chercher:** `class SendEventRemindersCommand extends Command`
- **Logique:** Trouve événements dans 3 jours, envoie emails

### ✓ T-5.20.2: Template email rappel
- **Fichier:** `templates/emails/event_reminder.html.twig`

### ⚠️ T-5.20.3: Configurer tâche cron
- **NON RÉALISÉ** - À faire sur le serveur
- **Commande:** `0 9 * * * php bin/console app:send-event-reminders`

---

## US-5.21: Certificats PDF

### ✓ T-5.21.1: Service CertificateService
- **Fichier:** `src/Service/CertificateService.php`
- **Chercher:** `class CertificateService`
- **Méthode:** generateCertificate (génère PDF)

### ✓ T-5.21.2: Commande SendCertificatesCommand
- **Fichier:** `src/Command/SendCertificatesCommand.php`
- **Chercher:** `class SendCertificatesCommand extends Command`
- **Logique:** Trouve événements terminés, génère et envoie certificats

### ⚠️ T-5.21.3: Champ certificate_sent
- **NON RÉALISÉ** - À ajouter dans `src/Entity/Participation.php`

### ✓ T-5.21.4: Template email certificat
- **Fichier:** `templates/emails/certificate.html.twig`

---

## US-5.22: Calendrier visuel

### ✓ T-5.22.1: Installer calendar-bundle
- **Fichier:** `composer.json`
- **Chercher:** `"tattali/calendar-bundle"`

### ✓ T-5.22.2: Configurer bundle
- **Fichier:** `config/packages/calendar.yaml`

### ✓ T-5.22.3: CalendarSubscriber
- **Fichier:** `src/EventSubscriber/CalendarSubscriber.php`
- **Chercher:** `class CalendarSubscriber implements EventSubscriberInterface`
- **Méthode:** onCalendarSetData (charge les événements)

### ✓ T-5.22.4: Template calendrier
- **Fichier:** `templates/frontoffice/evenement/calendar.html.twig`
- **Chercher:** FullCalendar JavaScript

### ✓ T-5.22.5: Route /calendar
- **Fichier:** `src/Controller/FrontofficeController.php`
- **Chercher:** `#[Route('/calendar')]`

---

## US-5.23: Météo

### ✓ T-5.23.1: Service WeatherService
- **Fichier:** `src/Service/WeatherService.php`
- **Chercher:** `class WeatherService`
- **Méthode:** getWeatherForEvent (appelle OpenWeatherMap API)

### ✓ T-5.23.2: Configuration API
- **Fichier:** `.env.local`
- **Chercher:** `OPENWEATHER_API_KEY=`

### ✓ T-5.23.3: Affichage météo
- **Fichier:** `templates/frontoffice/evenement/show.html.twig`
- **Chercher:** Section météo avec icônes

---

## US-5.24: Donner feedback

### ✓ T-5.24.1: Champ feedbacks JSON
- **Fichier:** `src/Entity/Participation.php`
- **Chercher:** `private ?array $feedbacks = null;`
- **Type:** JSON en base de données

### ✓ T-5.24.2: Contrôleur FeedbackController
- **Fichier:** `src/Controller/FeedbackController.php`
- **Chercher:** `class FeedbackController extends AbstractController`

### ✓ T-5.24.3: Template formulaire feedback
- **Fichier:** `templates/frontoffice/feedback/form.html.twig`
- **Chercher:** Formulaire avec étoiles, catégories, commentaire

### ✓ T-5.24.4: Bouton "Donner feedback"
- **Fichier:** `templates/frontoffice/participation/mes_participations.html.twig`
- **Chercher:** Lien vers formulaire feedback

---

## US-5.26: Statistiques de feedbacks

### ✓ T-5.26.1: FeedbackAnalyticsService
- **Fichier:** `src/Service/FeedbackAnalyticsService.php`
- **Chercher:** `class FeedbackAnalyticsService`
- **Méthodes:** calculateAverageRatings, getTopRatedEvents, etc.

### ⚠️ T-5.26.2: Contrôleur FeedbackStatsController
- **NON RÉALISÉ** - À créer

### ⚠️ T-5.26.3: Template statistiques Chart.js
- **NON RÉALISÉ** - À créer

---

## US-5.27: Rapport AI

### ✓ T-5.27.1: Configuration Hugging Face
- **Fichier:** `.env.local`
- **Chercher:** `HUGGINGFACE_API_KEY=`

### ✓ T-5.27.2: AIReportService
- **Fichier:** `src/Service/AIReportService.php`
- **Chercher:** `class AIReportService`
- **Méthodes:** analyzeFeedbacks, generateReport, generateRecommendations

### ✓ T-5.27.3: Contrôleur AIDashboardController
- **Fichier:** `src/Controller/AIDashboardController.php`
- **Chercher:** `class AIDashboardController extends AbstractController`

### ✓ T-5.27.4: Template dashboard AI
- **Fichier:** `templates/backoffice/ai/dashboard.html.twig`
- **Chercher:** Affichage des rapports AI

---

## US-5.31: Logging transitions

### ✓ T-5.31.1: Configurer Monolog
- **Fichier:** `config/packages/monolog.yaml`
- **Chercher:** Configuration des channels et handlers

### ✓ T-5.31.2: Logging dans WorkflowSubscriber
- **Fichier:** `src/EventSubscriber/EvenementWorkflowSubscriber.php`
- **Chercher:** `$this->logger->info()` dans les méthodes

### ✓ T-5.31.3: Page consultation logs
- **Fichier:** `templates/backoffice/logs/index.html.twig`

---

## US-5.34: Suppression participations refusées

### ✓ T-5.34.1: CleanupCancelledEventsCommand
- **Fichier:** `src/Command/CleanupCancelledEventsCommand.php`
- **Chercher:** `class CleanupCancelledEventsCommand extends Command`
- **Logique:** Supprime participations refusées anciennes

### ✓ T-5.34.2: Configurer cron
- **Documentation:** À faire sur le serveur

---

## US-5.35: Fichier .ics

### ✓ T-5.35.1: Méthode generateIcsFile
- **Fichier:** `src/Service/EmailService.php`
- **Chercher:** `public function generateIcsFile`
- **Format:** iCalendar standard

### ✓ T-5.35.2: Attacher .ics à email
- **Fichier:** `src/Service/EmailService.php`
- **Chercher:** `->attachFromPath()` dans sendParticipationConfirmation

---

## US-5.36: Suppression cascade équipes

### ✓ T-5.36.1: Cascade remove
- **Fichier:** `src/Entity/Evenement.php`
- **Chercher:** `cascade: ['remove']` dans relation avec Participation

### ✓ T-5.36.2: Tester suppression
- **Tests manuels:** Supprimer un événement et vérifier que les participations sont supprimées

---

## 🧪 TÂCHES DE TEST (36 tâches)

Chaque User Story a une tâche TEST ajoutée:
- **Format:** `T-X.X.TEST`
- **Description:** "Tests pour US-X.X"
- **Type:** Tests manuels et automatiques
- **Temps:** 1h par US
- **Fichier Sprint Backlog:** `SPRINT_BACKLOG_COMPLET_FINAL.html`

---

## 📝 NOTES POUR LA DÉMONSTRATION

### Fonctionnalités à démontrer en priorité:
1. **Workflow automatique** - Montrer les transitions d'états
2. **Emails automatiques** - Montrer un email de confirmation avec QR code
3. **Calendrier** - Montrer la vue calendrier interactive
4. **AI Dashboard** - Montrer l'analyse de feedbacks par l'IA
5. **Validation automatique** - Montrer le refus/acceptation automatique

### Commandes à exécuter:
```bash
# Mettre à jour les statuts des événements
php bin/console app:update-evenement-workflow

# Envoyer les rappels
php bin/console app:send-event-reminders

# Envoyer les certificats
php bin/console app:send-certificates

# Nettoyer les participations
php bin/console app:cleanup-cancelled-events
```

### Fichiers de configuration importants:
- `.env.local` - Clés API (SendGrid, Hugging Face, OpenWeather)
- `config/packages/workflow.yaml` - Configuration du workflow
- `config/packages/calendar.yaml` - Configuration du calendrier

---

✅ **Ce document permet de localiser précisément chaque tâche réalisée dans le code source!**
