# 🏃 SPRINT BACKLOG - Module Gestion des Événements (PARTIE 3A/4)

## 📋 User Stories US-5.31 à US-5.37

---

## US-5.31: Évaluer par catégories (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite évaluer l'événement par catégories (organisation, contenu, lieu, animation) afin de donner un feedback détaillé

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.31.1 | Ajouter rating_categories dans la structure JSON du feedback | 20 min | Développeur Backend |
| T5.31.2 | Créer 4 sections de notation dans form.html.twig | 45 min | Développeur Frontend |
| T5.31.3 | Implémenter système d'étoiles pour chaque catégorie | 50 min | Développeur Frontend |
| T5.31.4 | Styliser avec couleurs différentes par catégorie | 35 min | Développeur Frontend |
| T5.31.5 | Valider que toutes les catégories sont notées | 25 min | Développeur Backend |
| T5.31.6 | Sauvegarder dans rating_categories du JSON | 20 min | Développeur Backend |
| T5.31.7 | Tests soumission avec toutes catégories | 25 min | Testeur |

**Total Estimation**: 3h 40min

---

## US-5.32: Statistiques feedbacks par type (Admin)

**User Story**: En tant qu'Admin, je souhaite consulter les statistiques de feedbacks par type d'événement afin d'analyser la satisfaction

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.32.1 | Créer FeedbackAnalyticsService | 30 min | Développeur Backend |
| T5.32.2 | Méthode analyzeEventFeedbacks() pour un événement | 45 min | Développeur Backend |
| T5.32.3 | Calculer ratings moyens (global, par catégorie) | 35 min | Développeur Backend |
| T5.32.4 | Calculer distribution des sentiments | 30 min | Développeur Backend |
| T5.32.5 | Méthode analyzeByEventType() pour grouper par type | 50 min | Développeur Backend |
| T5.32.6 | Calculer taux de satisfaction par type | 30 min | Développeur Backend |
| T5.32.7 | Injecter FeedbackAnalyticsService dans EvenementController | 15 min | Développeur Backend |
| T5.32.8 | Afficher section "Statistiques & Rapports AI" dans index.html.twig | 40 min | Développeur Frontend |
| T5.32.9 | Créer graphiques avec Chart.js (ratings par type) | 60 min | Développeur Frontend |
| T5.32.10 | Afficher tableaux de statistiques détaillées | 45 min | Développeur Frontend |
| T5.32.11 | Tests calculs statistiques | 30 min | Testeur |

**Total Estimation**: 6h 30min

---

## US-5.33: Rapport d'analyse AI (Admin)

**User Story**: En tant qu'Admin, je souhaite générer un rapport d'analyse AI basé sur les feedbacks afin d'obtenir des insights

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.33.1 | Créer compte Hugging Face et obtenir API key | 15 min | Développeur Backend |
| T5.33.2 | Configurer HUGGINGFACE_API_KEY dans .env.local | 10 min | Développeur Backend |
| T5.33.3 | Configurer HUGGINGFACE_MODEL (mistralai/Mistral-7B-Instruct-v0.2) | 10 min | Développeur Backend |
| T5.33.4 | Créer AIReportService avec HttpClientInterface | 30 min | Développeur Backend |
| T5.33.5 | Méthode prepareDataForAI() dans FeedbackAnalyticsService | 35 min | Développeur Backend |
| T5.33.6 | Méthode buildAnalysisPrompt() avec données structurées | 45 min | Développeur Backend |
| T5.33.7 | Méthode callMistralAPI() avec nouvelle API router | 50 min | Développeur Backend |
| T5.33.8 | Gérer format OpenAI-compatible (messages, choices) | 40 min | Développeur Backend |
| T5.33.9 | Méthode generateAnalysisReport() | 30 min | Développeur Backend |
| T5.33.10 | Route POST /backoffice/evenement/ai/generate-analysis | 20 min | Développeur Backend |
| T5.33.11 | Retourner JsonResponse avec rapport généré | 20 min | Développeur Backend |
| T5.33.12 | Bouton "Générer Analyse AI" dans index.html.twig | 30 min | Développeur Frontend |
| T5.33.13 | Appel AJAX avec fetch() | 35 min | Développeur Frontend |
| T5.33.14 | Afficher loading spinner pendant génération | 25 min | Développeur Frontend |
| T5.33.15 | Afficher rapport dans modal avec formatage | 45 min | Développeur Frontend |
| T5.33.16 | Gérer erreurs API (401, 403, 500) | 30 min | Développeur Backend |
| T5.33.17 | Tests génération rapport | 30 min | Testeur |

**Total Estimation**: 8h 00min

---

## US-5.34: Recommandations d'événements AI (Admin)

**User Story**: En tant qu'Admin, je souhaite générer des recommandations d'événements via AI afin de planifier de futurs événements

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.34.1 | Méthode buildRecommendationPrompt() dans AIReportService | 40 min | Développeur Backend |
| T5.34.2 | Structurer prompt pour recommander 3 événements | 35 min | Développeur Backend |
| T5.34.3 | Inclure justifications basées sur données | 30 min | Développeur Backend |
| T5.34.4 | Méthode generateEventRecommendations() | 25 min | Développeur Backend |
| T5.34.5 | Route POST /backoffice/evenement/ai/generate-recommendations | 15 min | Développeur Backend |
| T5.34.6 | Bouton "Recommandations AI" dans index.html.twig | 25 min | Développeur Frontend |
| T5.34.7 | Appel AJAX et affichage dans modal | 35 min | Développeur Frontend |
| T5.34.8 | Styliser avec cartes pour chaque recommandation | 40 min | Développeur Frontend |
| T5.34.9 | Tests génération recommandations | 25 min | Testeur |

**Total Estimation**: 4h 30min

---

## US-5.35: Suggestions d'amélioration AI (Admin)

**User Story**: En tant qu'Admin, je souhaite générer des suggestions d'amélioration via AI afin d'optimiser les événements futurs

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.35.1 | Méthode buildImprovementPrompt() dans AIReportService | 40 min | Développeur Backend |
| T5.35.2 | Structurer prompt pour plan d'amélioration | 35 min | Développeur Backend |
| T5.35.3 | Inclure priorités (HAUTE, MOYENNE, BASSE) | 30 min | Développeur Backend |
| T5.35.4 | Méthode generateImprovementSuggestions() | 25 min | Développeur Backend |
| T5.35.5 | Route POST /backoffice/evenement/ai/generate-improvements | 15 min | Développeur Backend |
| T5.35.6 | Bouton "Suggestions d'Amélioration AI" dans index.html.twig | 25 min | Développeur Frontend |
| T5.35.7 | Appel AJAX et affichage dans modal | 35 min | Développeur Frontend |
| T5.35.8 | Styliser avec badges de priorité colorés | 40 min | Développeur Frontend |
| T5.35.9 | Tests génération suggestions | 25 min | Testeur |

**Total Estimation**: 4h 30min

---

## US-5.36: Workflow automatique (Système)

**User Story**: Le système gère automatiquement les transitions d'états via Workflow (planifié → en cours → terminé)

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.36.1 | Installer symfony/workflow via composer | 10 min | Développeur Backend |
| T5.36.2 | Créer config/packages/workflow.yaml | 30 min | Développeur Backend |
| T5.36.3 | Définir type: state_machine | 10 min | Développeur Backend |
| T5.36.4 | Définir marking_store avec property: workflowStatus | 15 min | Développeur Backend |
| T5.36.5 | Définir places (planifie, en_cours, termine, annule) | 20 min | Développeur Backend |
| T5.36.6 | Définir transition demarrer (planifie → en_cours) | 15 min | Développeur Backend |
| T5.36.7 | Définir transition terminer (en_cours → termine) | 15 min | Développeur Backend |
| T5.36.8 | Définir transition annuler (planifie/en_cours → annule) | 20 min | Développeur Backend |
| T5.36.9 | Activer audit trail (logs des transitions) | 20 min | Développeur Backend |
| T5.36.10 | Créer UpdateEvenementWorkflowCommand | 45 min | Développeur Backend |
| T5.36.11 | Logique de vérification des dates (dateDebut, dateFin) | 40 min | Développeur Backend |
| T5.36.12 | Appliquer transitions automatiquement (demarrer, terminer) | 35 min | Développeur Backend |
| T5.36.13 | Configurer cron job pour exécution automatique | 25 min | DevOps |
| T5.36.14 | Tests transitions automatiques | 30 min | Testeur |

**Total Estimation**: 5h 30min

---

## US-5.37: Bloquer participations (Système)

**User Story**: Le système empêche les participations aux événements en cours ou terminés afin de respecter les règles métier

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.37.1 | Méthode areParticipationsOpen() dans Evenement | 25 min | Développeur Backend |
| T5.37.2 | Vérifier workflowStatus === 'planifie' | 15 min | Développeur Backend |
| T5.37.3 | Vérifier !isCanceled | 10 min | Développeur Backend |
| T5.37.4 | Méthode canAcceptParticipations() (alias) | 10 min | Développeur Backend |
| T5.37.5 | Modifier FrontofficeEvenementController->participate() | 30 min | Développeur Backend |
| T5.37.6 | Vérifier canAcceptParticipations() avant affichage | 20 min | Développeur Backend |
| T5.37.7 | Rediriger avec message d'erreur si bloqué | 20 min | Développeur Backend |
| T5.37.8 | Modifier index.html.twig pour cacher bouton "Participer" | 30 min | Développeur Frontend |
| T5.37.9 | Afficher messages conditionnels (En cours, Terminé, Annulé) | 35 min | Développeur Frontend |
| T5.37.10 | Styliser messages avec couleurs appropriées | 25 min | Développeur Frontend |
| T5.37.11 | Tests blocage participations (en_cours) | 20 min | Testeur |
| T5.37.12 | Tests blocage participations (termine) | 20 min | Testeur |
| T5.37.13 | Tests blocage participations (annule) | 20 min | Testeur |

**Total Estimation**: 4h 40min

---

**FIN PARTIE 3A/4**

**Total Estimation Partie 3A**: 37h 20min  
**User Stories Couvertes**: US-5.31 à US-5.37 (7 US)

