# 📋 Guide Trello - AI, Calendar Bundle & Workflow Bundle

## 🎯 STRUCTURE DES LISTES TRELLO

```
1. 📝 BACKLOG
2. 📅 À FAIRE (Sprint actuel)
3. 🔄 EN COURS
4. ✅ À VÉRIFIER
5. ✔️ TERMINÉ
6. 🐛 BUGS
```

---

## 🎨 LABELS À CRÉER

```
🤖 AI/ML          - Tout ce qui concerne l'intelligence artificielle
📅 Calendar       - Intégration du calendrier
🔄 Workflow       - Workflow Bundle et transitions d'état
🔧 Configuration  - Fichiers de config (.env, .yaml)
🎨 Frontend       - Templates, JavaScript, CSS
🔵 Backend        - Services, Contrôleurs, Entités
📧 Email          - Envoi d'emails
🧪 Tests          - Tests unitaires/fonctionnels
📚 Documentation  - Guides, README

🔴 CRITIQUE
🟠 HAUTE
🟡 MOYENNE
🟢 BASSE
```

---

## 🤖 MODULE 1 : FEEDBACK & AI

### 📝 BACKLOG - User Stories AI

```
📌 [US-AI-01] Système de feedback après événement
Description : Le système permet aux étudiants de donner leur feedback après un événement terminé
Story Points : 8 SP
Priorité : HAUTE
Labels : AI/ML, Backend, Frontend

📌 [US-AI-02] Analyse des feedbacks par l'IA
Description : Le système utilise Mistral-7B pour analyser les feedbacks et générer des rapports
Story Points : 13 SP
Priorité : CRITIQUE
Labels : AI/ML, Backend

📌 [US-AI-03] Recommandations d'événements par l'IA
Description : Le système génère des recommandations d'événements basées sur l'historique
Story Points : 8 SP
Priorité : HAUTE
Labels : AI/ML, Backend

📌 [US-AI-04] Suggestions d'amélioration par l'IA
Description : Le système propose des améliorations basées sur les feedbacks négatifs
Story Points : 5 SP
Priorité : MOYENNE
Labels : AI/ML, Backend

📌 [US-AI-05] Dashboard admin avec rapports AI
Description : Interface backoffice pour visualiser les rapports générés par l'IA
Story Points : 8 SP
Priorité : HAUTE
Labels : AI/ML, Frontend, Backend
```

### 📅 À FAIRE - Tâches Techniques AI

#### 🔧 Configuration Hugging Face

```
[TASK-AI-01] Créer un compte Hugging Face
Description : S'inscrire sur https://huggingface.co/
Assigné à : [Nom]
Temps estimé : 10 min
Checklist :
☐ Aller sur https://huggingface.co/
☐ Cliquer sur "Sign Up"
☐ Remplir le formulaire
☐ Confirmer l'email
```

```
[TASK-AI-02] Générer un token API Hugging Face
Description : Créer un token avec permission "Make calls to Inference Providers"
Assigné à : [Nom]
Temps estimé : 5 min
Checklist :
☐ Aller sur https://huggingface.co/settings/tokens
☐ Cliquer sur "New token"
☐ Name : "autolearn-mistral"
☐ Permission : "Make calls to Inference Providers"
☐ Copier le token (commence par hf_...)
```

```
[TASK-AI-03] Configurer les variables d'environnement
Description : Ajouter le token Hugging Face dans .env.local
Assigné à : [Nom]
Temps estimé : 5 min
Fichiers à modifier : .env.local
Checklist :
☐ Ouvrir .env.local
☐ Ajouter HUGGINGFACE_API_KEY=hf_...
☐ Ajouter HUGGINGFACE_MODEL=mistralai/Mistral-7B-Instruct-v0.3
☐ Sauvegarder
```

#### 🔵 Backend - Services AI

```
[TASK-AI-04] Créer FeedbackAnalyticsService
Description : Service pour analyser les feedbacks et calculer les statistiques
Assigné à : [Nom Backend]
Temps estimé : 3h
Fichier : src/Service/FeedbackAnalyticsService.php
Checklist :
☐ Créer la classe FeedbackAnalyticsService
☐ Méthode analyzeEventFeedbacks() - Analyse par événement
☐ Méthode analyzeByEventType() - Analyse par type
☐ Méthode calculateRatings() - Calcul des moyennes
☐ Méthode getSentimentDistribution() - Distribution des sentiments
☐ Méthode prepareDataForAI() - Préparer les données pour l'IA
☐ Tester avec des données réelles
```

```
[TASK-AI-05] Créer AIReportService
Description : Service pour appeler l'API Mistral et générer des rapports
Assigné à : [Nom Backend]
Temps estimé : 4h
Fichier : src/Service/AIReportService.php
Checklist :
☐ Créer la classe AIReportService
☐ Méthode callMistralAPI() - Appel API Hugging Face
☐ Méthode generateAnalysisReport() - Rapport d'analyse
☐ Méthode generateEventRecommendations() - Recommandations
☐ Méthode generateImprovementSuggestions() - Suggestions
☐ Gestion des erreurs API
☐ Tester avec l'API réelle
```

#### 🎨 Frontend - Interface Feedback

```
[TASK-AI-06] Créer le formulaire de feedback
Description : Interface pour que les étudiants donnent leur feedback
Assigné à : [Nom Frontend]
Temps estimé : 4h
Fichier : templates/frontoffice/feedback/form.html.twig
Checklist :
☐ Design moderne avec gradient violet/bleu
☐ Étoiles interactives pour les ratings (1-5)
☐ Ratings par catégorie (organisation, contenu, lieu, animation)
☐ Sélection de sentiment avec emojis
☐ Zone de commentaire libre
☐ Validation côté client
☐ Soumission AJAX
☐ Responsive (mobile-friendly)
```

```
[TASK-AI-07] Ajouter le bouton "Donner mon feedback"
Description : Bouton dans la liste des participations
Assigné à : [Nom Frontend]
Temps estimé : 1h
Fichier : templates/frontoffice/participation/mes_participations.html.twig
Checklist :
☐ Afficher uniquement si événement terminé
☐ Couleur différente si feedback déjà donné
☐ Lien vers /feedback/participation/{id}
☐ Icône et texte clairs
```

#### 🔵 Backend - Contrôleur Feedback

```
[TASK-AI-08] Créer FeedbackController
Description : Contrôleur pour gérer les feedbacks
Assigné à : [Nom Backend]
Temps estimé : 2h
Fichier : src/Controller/FeedbackController.php
Checklist :
☐ Route GET /feedback/participation/{id} - Afficher formulaire
☐ Route POST /feedback/submit/{id} - Soumettre feedback
☐ Vérifier que l'événement est terminé
☐ Vérifier que l'utilisateur est membre de l'équipe
☐ Sauvegarder le feedback dans participation->feedbacks (JSON)
☐ Messages flash de confirmation
```

#### 🎨 Frontend - Dashboard Admin AI

```
[TASK-AI-09] Créer la page dashboard AI
Description : Interface backoffice pour visualiser les rapports AI
Assigné à : [Nom Frontend]
Temps estimé : 5h
Fichier : templates/backoffice/ai/dashboard.html.twig
Checklist :
☐ Section "Rapport d'Analyse" avec bouton "Générer"
☐ Section "Recommandations d'Événements"
☐ Section "Suggestions d'Amélioration"
☐ Graphiques Chart.js (satisfaction, types d'événements)
☐ Loading state pendant génération AI
☐ Affichage formaté des rapports (Markdown)
☐ Responsive
```

```
[TASK-AI-10] Créer le contrôleur AI Dashboard
Description : Routes pour le dashboard AI
Assigné à : [Nom Backend]
Temps estimé : 2h
Fichier : src/Controller/AIDashboardController.php
Checklist :
☐ Route GET /backoffice/ai/dashboard - Afficher dashboard
☐ Route POST /backoffice/ai/generate-analysis - Générer rapport
☐ Route POST /backoffice/ai/generate-recommendations - Recommandations
☐ Route POST /backoffice/ai/generate-improvements - Améliorations
☐ Gestion des erreurs API
☐ Messages flash
```

---

## 📅 MODULE 2 : CALENDAR BUNDLE

### 📝 BACKLOG - User Stories Calendar

```
📌 [US-CAL-01] Vue calendrier des événements
Description : Afficher tous les événements dans un calendrier interactif
Story Points : 8 SP
Priorité : HAUTE
Labels : Calendar, Frontend, Backend

📌 [US-CAL-02] Filtres par type d'événement
Description : Filtrer les événements affichés par type (Workshop, Hackathon, etc.)
Story Points : 3 SP
Priorité : MOYENNE
Labels : Calendar, Frontend

📌 [US-CAL-03] Détails au clic sur événement
Description : Afficher une modal avec les détails quand on clique sur un événement
Story Points : 3 SP
Priorité : MOYENNE
Labels : Calendar, Frontend

📌 [US-CAL-04] Export iCal
Description : Permettre l'export du calendrier au format .ics
Story Points : 5 SP
Priorité : BASSE
Labels : Calendar, Backend
```

### 📅 À FAIRE - Tâches Techniques Calendar

#### 🔧 Installation

```
[TASK-CAL-01] Installer CalendarBundle
Description : Installer le bundle Symfony Calendar
Assigné à : [Nom]
Temps estimé : 30 min
Commandes :
composer require tattali/calendar-bundle
php bin/console assets:install
Checklist :
☐ Exécuter composer require
☐ Vérifier que le bundle est dans config/bundles.php
☐ Exécuter assets:install
```

```
[TASK-CAL-02] Configurer le bundle
Description : Créer le fichier de configuration
Assigné à : [Nom]
Temps estimé : 15 min
Fichier : config/packages/calendar.yaml
Checklist :
☐ Créer calendar.yaml
☐ Configurer les options de base
☐ Tester la configuration
```

#### 🔵 Backend - CalendarSubscriber

```
[TASK-CAL-03] Créer CalendarSubscriber
Description : EventSubscriber pour charger les événements dans le calendrier
Assigné à : [Nom Backend]
Temps estimé : 2h
Fichier : src/EventSubscriber/CalendarSubscriber.php
Checklist :
☐ Créer la classe CalendarSubscriber
☐ Implémenter EventSubscriberInterface
☐ Méthode getSubscribedEvents() - Écouter SET_DATA
☐ Méthode onCalendarSetData() - Charger les événements
☐ Filtrer par dates (start/end)
☐ Définir les couleurs par type
☐ Ajouter les métadonnées (lieu, description, etc.)
☐ Tester avec des événements réels
```

#### 🎨 Frontend - Vue Calendrier

```
[TASK-CAL-04] Créer la page calendrier
Description : Template Twig pour afficher le calendrier
Assigné à : [Nom Frontend]
Temps estimé : 3h
Fichier : templates/frontoffice/evenement/calendar.html.twig
Checklist :
☐ Intégrer FullCalendar.js
☐ Configuration en français
☐ Vues : Mois, Semaine, Jour, Liste
☐ Boutons de navigation
☐ Responsive
☐ Loading state
☐ Styles personnalisés
```

```
[TASK-CAL-05] Créer la modal de détails
Description : Modal qui s'affiche au clic sur un événement
Assigné à : [Nom Frontend]
Temps estimé : 2h
Checklist :
☐ Design de la modal
☐ Afficher titre, type, lieu, dates
☐ Afficher description
☐ Afficher nombre de participations
☐ Boutons "Voir détails" et "Participer"
☐ Bouton fermer
☐ Animation d'ouverture/fermeture
```

#### 🔵 Backend - Routes Calendar

```
[TASK-CAL-06] Créer les routes calendrier
Description : Routes pour afficher le calendrier
Assigné à : [Nom Backend]
Temps estimé : 1h
Fichier : src/Controller/EvenementController.php
Checklist :
☐ Route GET /calendar - Afficher le calendrier
☐ Vérifier les permissions (public ou authentifié?)
☐ Passer les données nécessaires au template
```

---

## 🔄 MODULE 3 : WORKFLOW BUNDLE

### 📝 BACKLOG - User Stories Workflow

```
📌 [US-WF-01] Gestion des états d'événement
Description : Le système gère automatiquement les états (planifié, en cours, terminé, annulé)
Story Points : 13 SP
Priorité : CRITIQUE
Labels : Workflow, Backend

📌 [US-WF-02] Transitions automatiques basées sur les dates
Description : Le système change automatiquement l'état selon les dates
Story Points : 8 SP
Priorité : HAUTE
Labels : Workflow, Backend

📌 [US-WF-03] Envoi d'emails lors des transitions
Description : Le système envoie des emails automatiques lors du démarrage/annulation
Story Points : 5 SP
Priorité : HAUTE
Labels : Workflow, Email, Backend

📌 [US-WF-04] Historique des transitions (Audit Trail)
Description : Le système enregistre qui a fait quelle transition et quand
Story Points : 5 SP
Priorité : MOYENNE
Labels : Workflow, Backend

📌 [US-WF-05] Bouton d'annulation manuelle
Description : L'admin peut annuler manuellement un événement
Story Points : 3 SP
Priorité : MOYENNE
Labels : Workflow, Frontend, Backend
```

### 📅 À FAIRE - Tâches Techniques Workflow

#### 🔧 Installation & Configuration

```
[TASK-WF-01] Installer Workflow Component
Description : Installer le composant Symfony Workflow
Assigné à : [Nom]
Temps estimé : 10 min
Commande : composer require symfony/workflow
Checklist :
☐ Exécuter composer require
☐ Vérifier que le bundle est installé
```

```
[TASK-WF-02] Configurer le workflow
Description : Créer la configuration du workflow evenement_publishing
Assigné à : [Nom Backend]
Temps estimé : 1h
Fichier : config/packages/workflow.yaml
Checklist :
☐ Créer workflow.yaml
☐ Définir type: state_machine
☐ Définir places: planifie, en_cours, termine, annule
☐ Définir transitions: demarrer, terminer, annuler
☐ Configurer marking_store (property: workflowStatus)
☐ Activer audit_trail
☐ Ajouter métadonnées (title, color, icon)
```

#### 🔵 Backend - Entité Evenement

```
[TASK-WF-03] Ajouter workflowStatus à l'entité
Description : Ajouter la propriété pour stocker l'état du workflow
Assigné à : [Nom Backend]
Temps estimé : 30 min
Fichier : src/Entity/Evenement.php
Checklist :
☐ Ajouter propriété workflowStatus (string, 50)
☐ Ajouter getWorkflowStatus()
☐ Ajouter setWorkflowStatus()
☐ Ajouter syncStatusFromWorkflow() - Synchroniser avec l'enum
☐ Valeur par défaut : 'planifie'
```

```
[TASK-WF-04] Créer la migration
Description : Migration pour ajouter la colonne workflow_status
Assigné à : [Nom Backend]
Temps estimé : 15 min
Commandes :
php bin/console make:migration
php bin/console doctrine:migrations:migrate
Checklist :
☐ Générer la migration
☐ Vérifier le SQL généré
☐ Exécuter la migration
☐ Vérifier dans la base de données
```

#### 🔵 Backend - EventSubscriber

```
[TASK-WF-05] Créer EvenementWorkflowSubscriber
Description : EventSubscriber pour écouter les transitions du workflow
Assigné à : [Nom Backend]
Temps estimé : 4h
Fichier : src/EventSubscriber/EvenementWorkflowSubscriber.php
Checklist :
☐ Créer la classe EvenementWorkflowSubscriber
☐ Implémenter EventSubscriberInterface
☐ Méthode getSubscribedEvents() - Écouter tous les événements
☐ Méthode onTransition() - Logger l'historique complet
☐ Méthode onEntered() - Logger l'entrée dans un état
☐ Méthode onCompleted() - Logger la complétion
☐ Méthode onEnCours() - Envoyer emails de démarrage
☐ Méthode onTermine() - Actions de fin (certificats, etc.)
☐ Méthode onAnnule() - Envoyer emails d'annulation
☐ Méthode onGuard() - Valider les conditions
☐ Méthode sendEmailsToParticipants() - Envoi aux équipes
```

#### 🔵 Backend - Command

```
[TASK-WF-06] Créer UpdateEvenementWorkflowCommand
Description : Commande pour appliquer les transitions automatiques
Assigné à : [Nom Backend]
Temps estimé : 2h
Fichier : src/Command/UpdateEvenementWorkflowCommand.php
Checklist :
☐ Créer la classe UpdateEvenementWorkflowCommand
☐ Récupérer tous les événements
☐ Pour chaque événement :
  ☐ Si planifie et dateDebut <= now → demarrer
  ☐ Si en_cours et dateFin < now → terminer
☐ Logger les transitions appliquées
☐ Tester manuellement : php bin/console app:update-evenement-workflow
```

#### 📧 Email - Templates

```
[TASK-WF-07] Créer template email démarrage
Description : Template pour l'email envoyé au démarrage d'un événement
Assigné à : [Nom Frontend]
Temps estimé : 1h
Fichier : templates/emails/event_started.html.twig
Checklist :
☐ Design moderne et professionnel
☐ Afficher titre de l'événement
☐ Afficher date et lieu
☐ Afficher nom de l'équipe
☐ Message de bienvenue
☐ Responsive
```

```
[TASK-WF-08] Créer template email annulation
Description : Template pour l'email envoyé lors de l'annulation
Assigné à : [Nom Frontend]
Temps estimé : 1h
Fichier : templates/emails/event_cancelled.html.twig
Checklist :
☐ Design avec couleur rouge/orange
☐ Afficher titre de l'événement
☐ Afficher date et lieu
☐ Message d'excuse
☐ Informations de contact
☐ Responsive
```

```
[TASK-WF-09] Ajouter méthodes dans EmailService
Description : Méthodes pour envoyer les emails de workflow
Assigné à : [Nom Backend]
Temps estimé : 1h
Fichier : src/Service/EmailService.php
Checklist :
☐ Méthode sendEventStarted() - Email de démarrage
☐ Méthode sendEventCancellation() - Email d'annulation
☐ Utiliser les templates Twig
☐ Gestion des erreurs
☐ Tester avec des emails réels
```

#### 🎨 Frontend - Bouton Annulation

```
[TASK-WF-10] Ajouter bouton d'annulation
Description : Bouton dans le backoffice pour annuler un événement
Assigné à : [Nom Frontend]
Temps estimé : 1h
Fichier : templates/backoffice/evenement/index.html.twig
Checklist :
☐ Ajouter bouton "Annuler" dans la liste
☐ Afficher uniquement si événement planifié ou en cours
☐ Couleur rouge/orange
☐ Confirmation avant annulation (modal)
☐ Icône claire
```

```
[TASK-WF-11] Créer la route d'annulation
Description : Route POST pour annuler un événement
Assigné à : [Nom Backend]
Temps estimé : 1h
Fichier : src/Controller/EvenementController.php
Checklist :
☐ Route POST /backoffice/evenement/{id}/annuler
☐ Vérifier que la transition est possible (can)
☐ Appliquer la transition (apply)
☐ Définir isCanceled = true
☐ Flush en base
☐ Message flash de confirmation
☐ Redirection vers la liste
```

---

## 🧪 TESTS

### Tests AI

```
[TEST-AI-01] Tester l'API Hugging Face
Description : Vérifier que l'API répond correctement
Assigné à : [Nom]
Checklist :
☐ Créer un événement terminé avec feedbacks
☐ Aller sur /backoffice/ai/dashboard
☐ Cliquer sur "Générer Rapport d'Analyse"
☐ Vérifier que le rapport s'affiche
☐ Vérifier que le contenu est pertinent
☐ Tester les recommandations
☐ Tester les suggestions d'amélioration
```

### Tests Calendar

```
[TEST-CAL-01] Tester le calendrier
Description : Vérifier que le calendrier fonctionne correctement
Assigné à : [Nom]
Checklist :
☐ Aller sur /calendar
☐ Vérifier que les événements s'affichent
☐ Tester la navigation (mois précédent/suivant)
☐ Tester les vues (Mois, Semaine, Jour, Liste)
☐ Cliquer sur un événement → modal s'affiche
☐ Vérifier les couleurs par type
☐ Tester sur mobile
```

### Tests Workflow

```
[TEST-WF-01] Tester les transitions automatiques
Description : Vérifier que les transitions se font automatiquement
Assigné à : [Nom]
Checklist :
☐ Créer un événement avec dateDebut = maintenant
☐ Exécuter : php bin/console app:update-evenement-workflow
☐ Vérifier que l'événement passe en "en_cours"
☐ Vérifier que les emails sont envoyés
☐ Vérifier les logs (var/log/dev.log)
☐ Créer un événement avec dateFin passée
☐ Exécuter la commande
☐ Vérifier que l'événement passe en "termine"
```

```
[TEST-WF-02] Tester l'annulation manuelle
Description : Vérifier que l'annulation fonctionne
Assigné à : [Nom]
Checklist :
☐ Créer un événement planifié avec participations
☐ Cliquer sur "Annuler" dans le backoffice
☐ Confirmer l'annulation
☐ Vérifier que l'événement passe en "annule"
☐ Vérifier que les emails sont envoyés aux participants
☐ Vérifier les logs
☐ Vérifier que isCanceled = true
```

---

## 📊 ESTIMATION TOTALE

### Module AI
- Configuration : 20 min
- Backend : 9h
- Frontend : 10h
- Tests : 2h
**Total AI : ~21h**

### Module Calendar
- Installation : 45 min
- Backend : 3h
- Frontend : 5h
- Tests : 1h
**Total Calendar : ~10h**

### Module Workflow
- Installation : 10 min
- Configuration : 1h
- Backend : 8h
- Frontend : 3h
- Tests : 2h
**Total Workflow : ~14h**

**TOTAL GÉNÉRAL : ~45h (environ 1 semaine de travail pour 1 développeur full-stack)**

---

## 💡 CONSEILS POUR TRELLO

### Formulation des Cartes

✅ **BONNE FORMULATION** :
```
[TASK-AI-04] Créer FeedbackAnalyticsService
Description : Service pour analyser les feedbacks et calculer les statistiques
Assigné à : Ahmed
Temps estimé : 3h
```

❌ **MAUVAISE FORMULATION** :
```
Faire le service d'analyse
```

### Organisation Quotidienne

**Chaque matin** :
1. Déplacer les cartes terminées hier vers "À VÉRIFIER"
2. Prendre 1-2 nouvelles cartes de "À FAIRE" vers "EN COURS"
3. Commenter sur les cartes en cours avec l'avancement

**Chaque soir** :
1. Mettre à jour les checklists
2. Ajouter des commentaires sur les difficultés
3. Estimer le temps restant

### Priorités

1. **CRITIQUE** : Configuration Hugging Face, Workflow
2. **HAUTE** : Services AI, Calendar, EventSubscriber
3. **MOYENNE** : Dashboard, Tests
4. **BASSE** : Export iCal, Améliorations

---

## ✅ CHECKLIST DE DÉMARRAGE

```
☐ Créer le board Trello "Module Événements - AI & Bundles"
☐ Créer les 6 listes
☐ Créer tous les labels
☐ Créer toutes les cartes du BACKLOG
☐ Décomposer les User Stories en tâches techniques
☐ Assigner les tâches aux membres
☐ Planifier le premier sprint (2 semaines)
☐ Daily standup tous les matins (15 min)
```

**Prêt à commencer!** 🚀
