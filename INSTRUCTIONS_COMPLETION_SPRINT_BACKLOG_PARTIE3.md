# Instructions pour Compléter le Sprint Backlog Partie 3

## Problème Identifié

Le fichier `SPRINT_BACKLOG_COMPLET_PARTIE3.html` est incomplet. Il contient actuellement:
- ✅ US-5.16 à US-5.27 avec tâches détaillées (COMPLET)
- ❌ US-5.28 à US-5.40 MANQUANTES (seulement un message générique)

## User Stories Manquantes à Ajouter

Les User Stories suivantes doivent être ajoutées avec le même niveau de détail que les US-5.16 à US-5.27:

### US-5.28 - Consulter les feedbacks (Admin)
**Description:** En tant qu'Admin, je souhaite consulter tous les feedbacks d'un événement afin d'évaluer sa qualité

**Tâches à ajouter:**
- T5.28.1: Ajouter section "Feedbacks" dans show.html.twig du backoffice
- T5.28.2: Afficher liste de tous les feedbacks avec notes, sentiments, commentaires
- T5.28.3: Styliser avec cartes colorées selon sentiment
- T5.28.4: Afficher statistiques: note moyenne, distribution sentiments
- T5.28.5: Filtrer par sentiment (Très satisfait, Satisfait, etc.)
- T5.28.6: Tester affichage avec événement ayant 10 feedbacks variés

### US-5.29 - Générer rapport statistiques AI
**Description:** En tant qu'Admin, je souhaite générer un rapport statistiques via AI afin d'obtenir des insights

**Tâches à ajouter:**
- T5.29.1: Créer src/Service/AIReportService.php
- T5.29.2: Configurer HUGGINGFACE_API_KEY dans .env.local
- T5.29.3: Installer symfony/http-client
- T5.29.4: Implémenter generateStatisticsReport(Evenement $evenement): string
- T5.29.5: Construire prompt avec données événement (participations, feedbacks, taux acceptation)
- T5.29.6: Appeler API Hugging Face (Mistral-7B-Instruct-v0.2)
- T5.29.7: Parser réponse JSON et extraire texte généré
- T5.29.8: Ajouter route POST /backoffice/evenement/ai/generate-report
- T5.29.9: Ajouter bouton "Generate AI Report" dans index.html.twig
- T5.29.10: Afficher rapport dans modal avec design professionnel
- T5.29.11: Tester génération rapport avec événement terminé

### US-5.30 - Générer analyse sentiments AI
**Description:** En tant qu'Admin, je souhaite générer une analyse sentiments via AI afin de comprendre les retours

**Tâches à ajouter:**
- T5.30.1: Créer méthode generateSentimentAnalysis() dans AIReportService
- T5.30.2: Construire prompt avec tous les commentaires feedbacks
- T5.30.3: Demander à AI: sentiment global, thèmes récurrents, points forts/faibles
- T5.30.4: Ajouter route POST /backoffice/evenement/ai/analyze-sentiment
- T5.30.5: Ajouter bouton "Analyze Sentiment" dans index.html.twig
- T5.30.6: Afficher analyse dans modal avec graphiques
- T5.30.7: Styliser avec badges colorés (positif=vert, négatif=rouge, neutre=gris)
- T5.30.8: Tester avec événement ayant feedbacks variés

### US-5.31 - Générer recommandations événements AI
**Description:** En tant qu'Admin, je souhaite générer recommandations d'événements via AI

**Tâches à ajouter:**
- T5.31.1: Créer méthode buildRecommendationPrompt() dans AIReportService
- T5.31.2: Structurer prompt pour recommander 3 événements
- T5.31.3: Inclure justifications basées sur données
- T5.31.4: Implémenter méthode generateEventRecommendations()
- T5.31.5: Ajouter route POST /backoffice/evenement/ai/generate-recommendations
- T5.31.6: Ajouter bouton "Recommandations AI" dans index.html.twig
- T5.31.7: Implémenter appel AJAX et affichage dans modal
- T5.31.8: Styliser avec cartes pour chaque recommandation
- T5.31.9: Tester génération recommandations

### US-5.32 - Générer suggestions d'amélioration AI
**Description:** En tant qu'Admin, je souhaite générer suggestions d'amélioration via AI

**Tâches à ajouter:**
- T5.32.1: Créer méthode buildImprovementPrompt() dans AIReportService
- T5.32.2: Structurer prompt pour plan d'amélioration
- T5.32.3: Inclure priorités (HAUTE, MOYENNE, BASSE)
- T5.32.4: Implémenter méthode generateImprovementSuggestions()
- T5.32.5: Ajouter route POST /backoffice/evenement/ai/generate-improvements
- T5.32.6: Ajouter bouton "Suggestions d'Amélioration AI" dans index.html.twig
- T5.32.7: Implémenter appel AJAX et affichage dans modal
- T5.32.8: Styliser avec badges de priorité colorés
- T5.32.9: Tester génération suggestions

### US-5.33 - Le système empêche participations aux événements en cours ou terminés
**Description:** Le système empêche toute nouvelle participation aux événements en cours ou terminés

**Tâches à ajouter:**
- T5.33.1: Implémenter méthode areParticipationsOpen() dans Evenement.php
- T5.33.2: Vérifier workflowStatus === 'planifie'
- T5.33.3: Vérifier !isCanceled
- T5.33.4: Créer méthode canAcceptParticipations() (alias)
- T5.33.5: Modifier FrontofficeEvenementController->participate()
- T5.33.6: Vérifier canAcceptParticipations() avant affichage
- T5.33.7: Rediriger avec message d'erreur si bloqué
- T5.33.8: Modifier index.html.twig pour cacher bouton "Participer"
- T5.33.9: Afficher messages conditionnels (En cours, Terminé, Annulé)
- T5.33.10: Styliser messages avec couleurs appropriées
- T5.33.11: Tester blocage participations (en_cours)
- T5.33.12: Tester blocage participations (termine)
- T5.33.13: Tester blocage participations (annule)

### US-5.34 - Le système log toutes les transitions d'états
**Description:** Le système log toutes les transitions d'états avec traçabilité

**Tâches à ajouter:**
- T5.34.1: Créer src/EventSubscriber/EvenementWorkflowSubscriber.php (déjà fait)
- T5.34.2: Implémenter getSubscribedEvents()
- T5.34.3: Écouter workflow.transition (toutes transitions)
- T5.34.4: Implémenter méthode onTransition() pour logger
- T5.34.5: Logger: événement ID, titre, transition, from, to
- T5.34.6: Logger: timestamp, user (si disponible)
- T5.34.7: Écouter workflow.entered (états spécifiques)
- T5.34.8: Implémenter méthodes onEnCours(), onTermine(), onAnnule()
- T5.34.9: Logger dans var/log/dev.log avec contexte
- T5.34.10: Tester vérification logs après transitions

### US-5.35 - Rejoindre une équipe existante (Étudiant)
**Description:** En tant qu'Étudiant, je souhaite rejoindre une équipe existante

**Tâches à ajouter:**
- T5.35.1: Créer route /events/{equipeId}/join/{eventId} (POST)
- T5.35.2: Implémenter méthode joinEquipe() dans FrontofficeEvenementController
- T5.35.3: Vérifier que équipe existe et appartient à l'événement
- T5.35.4: Vérifier que équipe a moins de 6 membres
- T5.35.5: Vérifier que étudiant n'est pas déjà dans l'équipe
- T5.35.6: Ajouter étudiant à l'équipe (addEtudiant)
- T5.35.7: Afficher liste des équipes disponibles dans participate.html.twig
- T5.35.8: Afficher nombre de membres actuels (X/6)
- T5.35.9: Ajouter bouton "Rejoindre" pour chaque équipe
- T5.35.10: Styliser cartes d'équipes avec design moderne
- T5.35.11: Tester rejoindre équipe avec places disponibles
- T5.35.12: Tester refus si équipe complète (6 membres)

### US-5.36 - Voir les équipes participantes (Étudiant)
**Description:** En tant qu'Étudiant, je souhaite voir les équipes participantes

**Tâches à ajouter:**
- T5.36.1: Récupérer participations acceptées dans index()
- T5.36.2: Pour chaque participation, récupérer l'équipe
- T5.36.3: Passer les équipes au template
- T5.36.4: Créer section "Équipes Participantes" dans index.html.twig
- T5.36.5: Afficher mini-cartes pour chaque équipe
- T5.36.6: Afficher nom équipe + nombre de membres
- T5.36.7: Styliser avec gradients et icônes
- T5.36.8: Afficher dans accordéon expand/collapse
- T5.36.9: Tester affichage équipes

### US-5.37 - Voir détails d'un événement (Admin)
**Description:** En tant qu'Admin, je souhaite voir détails d'un événement

**Tâches à ajouter:**
- T5.37.1: Créer route /backoffice/evenement/{id} (GET)
- T5.37.2: Implémenter méthode show() dans EvenementController
- T5.37.3: Récupérer toutes participations de l'événement
- T5.37.4: Calculer statistiques (acceptées, refusées, en attente)
- T5.37.5: Récupérer feedbacks si disponibles
- T5.37.6: Créer template templates/backoffice/evenement/show.html.twig
- T5.37.7: Section informations générales (titre, type, dates, lieu)
- T5.37.8: Section participations avec tableau
- T5.37.9: Section équipes avec détails membres
- T5.37.10: Section statistiques avec graphiques
- T5.37.11: Boutons d'action (Modifier, Annuler, Supprimer)
- T5.37.12: Tester affichage détails

### US-5.38 - Le système supprime automatiquement participations refusées
**Description:** Le système supprime automatiquement les participations refusées

**Tâches à ajouter:**
- T5.38.1: Créer src/Command/CleanupRefusedParticipationsCommand.php
- T5.38.2: Récupérer toutes participations avec statut REFUSE
- T5.38.3: Supprimer chaque participation refusée
- T5.38.4: Logger les suppressions (nombre, IDs)
- T5.38.5: Configurer cron job pour exécution quotidienne
- T5.38.6: Tester suppression automatique

### US-5.39 - Le système génère fichier .ics calendrier
**Description:** Le système génère fichier .ics pour import dans calendriers

**Tâches à ajouter:**
- T5.39.1: Implémenter méthode generateIcsFile() dans EmailService
- T5.39.2: Format iCalendar (BEGIN:VCALENDAR, VERSION:2.0)
- T5.39.3: Ajouter VEVENT avec DTSTART, DTEND, SUMMARY, LOCATION
- T5.39.4: Ajouter DESCRIPTION avec détails événement
- T5.39.5: Ajouter UID unique pour chaque événement
- T5.39.6: Attacher fichier .ics à l'email de confirmation
- T5.39.7: Tester génération fichier .ics
- T5.39.8: Tester import dans Google Calendar / Outlook

### US-5.40 - Le système valide contraintes de dates
**Description:** Le système valide que les dates sont cohérentes

**Tâches à ajouter:**
- T5.40.1: Ajouter Assert\Expression dans Evenement.php
- T5.40.2: Expression: "this.getDateFin() >= this.getDateDebut()"
- T5.40.3: Message d'erreur personnalisé
- T5.40.4: Ajouter Assert\GreaterThan("today") pour dateDebut
- T5.40.5: Tester validation avec date fin < date début
- T5.40.6: Tester validation avec date début dans le passé

## Format à Respecter

Chaque User Story doit suivre EXACTEMENT le même format que les US-5.16 à US-5.27:

```html
<!-- US-5.XX -->
<div class="us-section">
    <div class="us-header">
        <h2>US-5.XX - Titre de la User Story</h2>
        <p>Description complète de la user story</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID Tâche</th>
                <th style="width: 67%;">Tâche Technique Détaillée</th>
                <th style="width: 12%;">Estimation</th>
                <th style="width: 13%;">Responsable</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="task-id">T5.XX.1</td>
                <td>Description détaillée avec code examples</td>
                <td class="estimation">XX min</td>
                <td>Admin</td>
            </tr>
            <!-- Plus de tâches... -->
        </tbody>
    </table>
</div>
```

## Action Requise

Le fichier doit être complété en ajoutant toutes les US-5.28 à US-5.40 avec leurs tâches détaillées AVANT la section footer.

Les estimations et détails techniques sont disponibles dans `SPRINT_BACKLOG_DETAILLE_PARTIE3.csv`.

## Résultat Attendu

Un fichier HTML professionnel et complet contenant:
- ✅ US-5.16 à US-5.40 (25 User Stories au total)
- ✅ Toutes les tâches techniques détaillées
- ✅ Estimations en minutes
- ✅ Responsable = Admin pour toutes les tâches
- ✅ Code examples et configurations
- ✅ Design professionnel cohérent
