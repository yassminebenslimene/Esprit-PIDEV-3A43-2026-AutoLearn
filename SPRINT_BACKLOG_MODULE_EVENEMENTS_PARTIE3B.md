# 🏃 SPRINT BACKLOG - Module Gestion des Événements (PARTIE 3B/4)

## 📋 User Stories US-5.38 à US-5.45

---

## US-5.38: Audit trail logging (Système)

**User Story**: Le système log toutes les transitions d'états avec traçabilité (qui, quand, quoi) afin d'assurer un audit complet

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.38.1 | Créer EvenementWorkflowSubscriber | 30 min | Développeur Backend |
| T5.38.2 | Implémenter getSubscribedEvents() | 20 min | Développeur Backend |
| T5.38.3 | Écouter workflow.transition (toutes transitions) | 15 min | Développeur Backend |
| T5.38.4 | Méthode onTransition() pour logger | 35 min | Développeur Backend |
| T5.38.5 | Logger: événement ID, titre, transition, from, to | 30 min | Développeur Backend |
| T5.38.6 | Logger: timestamp, user (si disponible) | 25 min | Développeur Backend |
| T5.38.7 | Écouter workflow.entered (états spécifiques) | 20 min | Développeur Backend |
| T5.38.8 | Méthodes onEnCours(), onTermine(), onAnnule() | 40 min | Développeur Backend |
| T5.38.9 | Logger dans var/log/dev.log avec contexte | 25 min | Développeur Backend |
| T5.38.10 | Tests vérification logs après transitions | 30 min | Testeur |

**Total Estimation**: 4h 30min

---

## US-5.39: Rejoindre équipe existante (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite rejoindre une équipe existante afin de participer sans créer une nouvelle équipe


| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.39.1 | Route /events/{equipeId}/join/{eventId} (POST) | 15 min | Développeur Backend |
| T5.39.2 | Méthode joinEquipe() dans FrontofficeEvenementController | 30 min | Développeur Backend |
| T5.39.3 | Vérifier que l'équipe existe et appartient à l'événement | 20 min | Développeur Backend |
| T5.39.4 | Vérifier que l'équipe a moins de 6 membres | 20 min | Développeur Backend |
| T5.39.5 | Vérifier que l'étudiant n'est pas déjà dans l'équipe | 25 min | Développeur Backend |
| T5.39.6 | Ajouter l'étudiant à l'équipe (addEtudiant) | 15 min | Développeur Backend |
| T5.39.7 | Afficher liste des équipes disponibles dans participate.html.twig | 40 min | Développeur Frontend |
| T5.39.8 | Afficher nombre de membres actuels (X/6) | 25 min | Développeur Frontend |
| T5.39.9 | Bouton "Rejoindre" pour chaque équipe | 20 min | Développeur Frontend |
| T5.39.10 | Styliser cartes d'équipes avec design moderne | 35 min | Développeur Frontend |
| T5.39.11 | Tests rejoindre équipe avec places disponibles | 20 min | Testeur |
| T5.39.12 | Tests refus si équipe complète (6 membres) | 20 min | Testeur |

**Total Estimation**: 4h 45min

---

## US-5.40: Voir équipes participantes (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite voir les équipes participantes à un événement afin de connaître la compétition

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.40.1 | Récupérer participations acceptées dans index() | 20 min | Développeur Backend |
| T5.40.2 | Pour chaque participation, récupérer l'équipe | 15 min | Développeur Backend |
| T5.40.3 | Passer les équipes au template | 10 min | Développeur Backend |
| T5.40.4 | Créer section "Équipes Participantes" dans index.html.twig | 35 min | Développeur Frontend |
| T5.40.5 | Afficher mini-cartes pour chaque équipe | 40 min | Développeur Frontend |
| T5.40.6 | Afficher nom équipe + nombre de membres | 20 min | Développeur Frontend |
| T5.40.7 | Styliser avec gradients et icônes | 30 min | Développeur Frontend |
| T5.40.8 | Afficher dans accordéon expand/collapse | 25 min | Développeur Frontend |
| T5.40.9 | Tests affichage équipes | 15 min | Testeur |

**Total Estimation**: 3h 30min

---

## US-5.41: Détails événement (Admin)

**User Story**: En tant qu'Admin, je souhaite voir les détails d'un événement (participations, équipes, statistiques) afin d'avoir une vue complète

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.41.1 | Route /backoffice/evenement/{id} (GET) | 10 min | Développeur Backend |
| T5.41.2 | Méthode show() dans EvenementController | 25 min | Développeur Backend |
| T5.41.3 | Récupérer toutes les participations de l'événement | 20 min | Développeur Backend |
| T5.41.4 | Calculer statistiques (acceptées, refusées, en attente) | 30 min | Développeur Backend |
| T5.41.5 | Récupérer feedbacks si disponibles | 20 min | Développeur Backend |
| T5.41.6 | Créer template show.html.twig | 50 min | Développeur Frontend |
| T5.41.7 | Section informations générales (titre, type, dates, lieu) | 30 min | Développeur Frontend |
| T5.41.8 | Section participations avec tableau | 40 min | Développeur Frontend |
| T5.41.9 | Section équipes avec détails membres | 40 min | Développeur Frontend |
| T5.41.10 | Section statistiques avec graphiques | 45 min | Développeur Frontend |
| T5.41.11 | Boutons d'action (Modifier, Annuler, Supprimer) | 25 min | Développeur Frontend |
| T5.41.12 | Tests affichage détails | 25 min | Testeur |

**Total Estimation**: 6h 40min

---

## US-5.42: Suppression auto participations refusées (Système)

**User Story**: Le système supprime automatiquement les participations refusées afin de garder une base de données propre

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.42.1 | Créer CleanupRefusedParticipationsCommand | 30 min | Développeur Backend |
| T5.42.2 | Récupérer toutes participations avec statut REFUSE | 20 min | Développeur Backend |
| T5.42.3 | Supprimer chaque participation refusée | 20 min | Développeur Backend |
| T5.42.4 | Logger les suppressions (nombre, IDs) | 20 min | Développeur Backend |
| T5.42.5 | Configurer cron job pour exécution quotidienne | 15 min | DevOps |
| T5.42.6 | Tests suppression automatique | 20 min | Testeur |

**Total Estimation**: 2h 05min

---

## US-5.43: Fichier .ics calendrier (Système)

**User Story**: Le système génère automatiquement un fichier .ics pour ajouter l'événement au calendrier personnel

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.43.1 | Méthode generateIcsFile() dans EmailService | 40 min | Développeur Backend |
| T5.43.2 | Format iCalendar (BEGIN:VCALENDAR, VERSION:2.0) | 30 min | Développeur Backend |
| T5.43.3 | Ajouter VEVENT avec DTSTART, DTEND, SUMMARY, LOCATION | 35 min | Développeur Backend |
| T5.43.4 | Ajouter DESCRIPTION avec détails événement | 20 min | Développeur Backend |
| T5.43.5 | Ajouter UID unique pour chaque événement | 15 min | Développeur Backend |
| T5.43.6 | Attacher fichier .ics à l'email de confirmation | 25 min | Développeur Backend |
| T5.43.7 | Tests génération fichier .ics | 20 min | Testeur |
| T5.43.8 | Tests import dans Google Calendar / Outlook | 25 min | Testeur |

**Total Estimation**: 3h 30min

---

## US-5.44: Validation contraintes dates (Système)

**User Story**: Le système valide automatiquement les contraintes de dates (date fin >= date début)

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.44.1 | Ajouter Assert\Expression dans Evenement | 20 min | Développeur Backend |
| T5.44.2 | Expression: "this.getDateFin() >= this.getDateDebut()" | 15 min | Développeur Backend |
| T5.44.3 | Message d'erreur personnalisé | 10 min | Développeur Backend |
| T5.44.4 | Ajouter Assert\GreaterThan("today") pour dateDebut | 15 min | Développeur Backend |
| T5.44.5 | Tests validation avec date fin < date début | 15 min | Testeur |
| T5.44.6 | Tests validation avec date début dans le passé | 15 min | Testeur |

**Total Estimation**: 1h 30min

---

## US-5.45: Validation taille équipe (Système)

**User Story**: Le système valide automatiquement les contraintes de taille d'équipe (4-6 membres)

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.45.1 | Ajouter Assert\Count dans Equipe | 20 min | Développeur Backend |
| T5.45.2 | Définir min: 4, max: 6 | 10 min | Développeur Backend |
| T5.45.3 | Messages d'erreur personnalisés (minMessage, maxMessage) | 15 min | Développeur Backend |
| T5.45.4 | Valider lors de la création d'équipe | 15 min | Développeur Backend |
| T5.45.5 | Valider lors de l'ajout/retrait de membres | 20 min | Développeur Backend |
| T5.45.6 | Tests création équipe avec 3 membres (refus) | 15 min | Testeur |
| T5.45.7 | Tests création équipe avec 4 membres (accepté) | 10 min | Testeur |
| T5.45.8 | Tests création équipe avec 6 membres (accepté) | 10 min | Testeur |
| T5.45.9 | Tests création équipe avec 7 membres (refus) | 15 min | Testeur |

**Total Estimation**: 2h 10min

---

**FIN PARTIE 3B/4**

**Total Estimation Partie 3B**: 28h 40min  
**User Stories Couvertes**: US-5.38 à US-5.45 (8 US)

---

## 📊 RÉCAPITULATIF COMPLET DU SPRINT BACKLOG

### Répartition par Partie

| Partie | User Stories | Estimation | Fichier |
|--------|--------------|------------|---------|
| **Partie 1** | US-5.1 à US-5.15 (15 US) | 60h 35min | SPRINT_BACKLOG_MODULE_EVENEMENTS_PARTIE1.md |
| **Partie 2** | US-5.16 à US-5.30 (15 US) | 75h 30min | SPRINT_BACKLOG_MODULE_EVENEMENTS_PARTIE2.md |
| **Partie 3A** | US-5.31 à US-5.37 (7 US) | 37h 20min | SPRINT_BACKLOG_MODULE_EVENEMENTS_PARTIE3.md |
| **Partie 3B** | US-5.38 à US-5.45 (8 US) | 28h 40min | SPRINT_BACKLOG_MODULE_EVENEMENTS_PARTIE3B.md |

### Totaux Globaux

**Total User Stories**: 45 US  
**Total Estimation**: 202h 05min (≈ 25 jours-homme)

### Répartition par Type de Tâche

- **Backend (Entités, Services, Controllers)**: ~110h (54%)
- **Frontend (Templates, JavaScript, CSS)**: ~65h (32%)
- **Tests**: ~27h (14%)

### Répartition par Catégorie Fonctionnelle

1. **CRUD Événements & Équipes**: 35h
2. **Gestion Participations & Validations**: 45h
3. **Emails Automatiques**: 28h
4. **Feedbacks & Analyse AI**: 32h
5. **Workflow & États**: 22h
6. **Calendrier & Météo**: 17h
7. **Certificats & Badges**: 15h
8. **Fonctionnalités Avancées**: 8h

---

## 🎯 Recommandations pour l'Exécution

### Sprint 1 (2 semaines) - Fondations
- **Objectif**: CRUD complet + Participations de base
- **US**: US-5.1 à US-5.15
- **Estimation**: 60h 35min
- **Livrables**: Création/modification/suppression événements, création équipes, participations avec validation automatique

### Sprint 2 (2 semaines) - Gestion Avancée
- **Objectif**: Validation manuelle + Emails automatiques
- **US**: US-5.16 à US-5.27
- **Estimation**: 65h
- **Livrables**: Validations métier, gestion admin participations, emails (confirmation, annulation, démarrage, rappels, certificats)

### Sprint 3 (2 semaines) - Feedbacks & AI
- **Objectif**: Système de feedback + Analyse AI
- **US**: US-5.28 à US-5.35
- **Estimation**: 45h
- **Livrables**: Calendrier visuel, météo, feedbacks détaillés, rapports AI (analyse, recommandations, améliorations)

### Sprint 4 (1 semaine) - Workflow & Optimisations
- **Objectif**: Workflow automatique + Fonctionnalités finales
- **US**: US-5.36 à US-5.45
- **Estimation**: 31h 30min
- **Livrables**: Workflow Component, audit trail, blocage participations, rejoindre équipes, fichiers .ics, validations finales

---

## 📝 Notes Importantes

### Dépendances Critiques

1. **Workflow** (US-5.36) doit être implémenté avant US-5.37 (blocage participations)
2. **FeedbackAnalyticsService** (US-5.32) doit être créé avant US-5.33, US-5.34, US-5.35 (AI)
3. **EmailService** (US-5.23) doit être créé avant tous les autres emails (US-5.24 à US-5.27)
4. **Entités de base** (US-5.1, US-5.8, US-5.12) doivent être créées en premier

### Technologies Requises

- **Symfony 6.4** avec PHP 8.2
- **Doctrine ORM** pour la persistance
- **symfony/workflow** pour la gestion d'états
- **SendGrid API** pour les emails
- **Hugging Face API** (Mistral-7B) pour l'AI
- **OpenWeatherMap API** pour la météo
- **tattali/calendar-bundle** + FullCalendar pour le calendrier
- **dompdf** pour les PDF (certificats, badges)
- **endroid/qr-code** pour les QR codes

### Points d'Attention

1. **Permissions Hugging Face**: Token doit avoir "Make calls to Inference Providers"
2. **Cron Jobs**: Configurer pour UpdateEvenementWorkflowCommand, SendEventRemindersCommand, SendCertificatesCommand
3. **Cascade Deletes**: Respecter l'ordre (participations → équipes → événements)
4. **Validation Métier**: Un étudiant ne peut pas être dans 2 équipes pour le même événement
5. **Workflow Status**: Synchroniser workflowStatus (string) avec status (enum)

---

**Date de Création**: 22 Février 2026  
**Version**: 1.0  
**Statut**: ✅ Complet et Validé
