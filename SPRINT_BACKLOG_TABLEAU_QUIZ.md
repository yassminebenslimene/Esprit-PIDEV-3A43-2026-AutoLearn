# 📋 SPRINT BACKLOG - GESTION DE QUIZ (FORMAT TABLEAU)

## 🎯 Objectif du Sprint
Développer un système complet de gestion de quiz avec interface d'administration (backoffice) et interface de passage pour étudiants (frontoffice).

---

## 📊 TABLEAU DES USER STORIES ET TÂCHES

| ID US | User Story (US) | ID Tâche | Tâches : chaque membre (scrum team) énumère l'ensemble de ces tâches effectuées de l'analyse jusqu'à le test afin de réaliser ce user story | Estimation (en minute) | Responsable |
|-------|-----------------|----------|---------------------------------------------------------------------------------------------------------------------------------------------|------------------------|-------------|
| **US-1.1** | **En tant qu'administrateur je veux créer un nouveau quiz afin de proposer des évaluations aux étudiants** | T1.1.1 | En tant que concepteur je dois préparer le diagramme de classe de conception pour l'entité Quiz | 30 min | Équipe Backend |
| | | T1.1.2 | En tant qu'Admin BD je dois créer les tables quiz avec les colonnes: id, titre, description, etat, duree_max_minutes, seuil_reussite, max_tentatives, chapitre_id | 1h | Équipe Backend |
| | | T1.1.3 | En tant que développeur je dois créer l'entité Quiz.php avec toutes les propriétés et annotations de validation | 1h | Équipe Backend |
| | | T1.1.4 | En tant que développeur je dois créer le formulaire QuizType avec EntityType pour la relation chapitre | 1h | Équipe Backend |
| | | T1.1.5 | En tant que développeur je dois créer l'action new() dans QuizController pour gérer la création | 45 min | Équipe Backend |
| | | T1.1.6 | En tant que développeur frontend je dois créer le template new.html.twig avec design glass morphism | 2h | Équipe Frontend |
| | | T1.1.7 | En tant que développeur je dois implémenter la validation métier dans QuizManagementService | 1h | Équipe Backend |
| | | T1.1.8 | En tant que testeur je dois préparer les cas de test pour la création de quiz | 1h | Équipe QA |
| | | T1.1.9 | En tant que testeur je dois exécuter les tests de validation (champs obligatoires, formats, contraintes) | 45 min | Équipe QA |
| **US-1.2** | **En tant qu'administrateur je veux modifier un quiz existant afin de corriger ou améliorer le contenu** | T1.2.1 | En tant que développeur je dois créer l'action edit() dans QuizController | 45 min | Équipe Backend |
| | | T1.2.2 | En tant que développeur frontend je dois créer le template edit.html.twig avec formulaire pré-rempli | 1h | Équipe Frontend |
| | | T1.2.3 | En tant que développeur je dois implémenter la validation des modifications | 45 min | Équipe Backend |
| | | T1.2.4 | En tant que développeur je dois ajouter les messages flash de confirmation | 30 min | Équipe Backend |
| | | T1.2.5 | En tant que testeur je dois tester les modifications avec différents scénarios | 1h | Équipe QA |
| **US-1.3** | **En tant qu'administrateur je veux supprimer un quiz afin de nettoyer les contenus obsolètes** | T1.3.1 | En tant que développeur je dois créer l'action delete() avec protection CSRF | 45 min | Équipe Backend |
| | | T1.3.2 | En tant que développeur frontend je dois implémenter la confirmation JavaScript (modal) | 1h | Équipe Frontend |
| | | T1.3.3 | En tant qu'Admin BD je dois configurer la suppression en cascade (orphanRemoval) | 30 min | Équipe Backend |
| | | T1.3.4 | En tant que développeur je dois ajouter les messages de retour utilisateur | 30 min | Équipe Backend |
| | | T1.3.5 | En tant que testeur je dois tester la suppression et la cascade sur questions/options | 1h | Équipe QA |
| **US-1.4** | **En tant qu'administrateur je veux voir la liste de tous les quiz avec leurs questions et options afin d'avoir une vue d'ensemble** | T1.4.1 | En tant que concepteur je dois concevoir l'interface hiérarchique Quiz > Questions > Options | 1h | Équipe UX/UI |
| | | T1.4.2 | En tant que développeur frontend je dois créer le template quiz_management.html.twig avec structure hiérarchique | 3h | Équipe Frontend |
| | | T1.4.3 | En tant que développeur je dois créer l'API endpoint GET /api/quiz/{id}/questions | 1h | Équipe Backend |
| | | T1.4.4 | En tant que développeur je dois créer l'API endpoint GET /api/question/{id}/options | 1h | Équipe Backend |
| | | T1.4.5 | En tant que développeur frontend je dois implémenter le JavaScript pour chargement AJAX | 3h | Équipe Frontend |
| | | T1.4.6 | En tant que développeur frontend je dois rendre l'interface responsive (mobile/tablette) | 2h | Équipe Frontend |
| | | T1.4.7 | En tant que testeur je dois tester sur différents navigateurs (Chrome, Firefox, Safari) | 1h | Équipe QA |
| **US-2.1** | **En tant qu'administrateur je veux ajouter des questions à un quiz afin de construire le contenu évaluatif** | T2.1.1 | En tant que concepteur je dois préparer le diagramme de classe pour l'entité Question | 30 min | Équipe Backend |
| | | T2.1.2 | En tant qu'Admin BD je dois créer la table question avec colonnes: id, texte_question, point, quiz_id | 45 min | Équipe Backend |
| | | T2.1.3 | En tant que développeur je dois créer l'entité Question.php avec validation (NotBlank, Length, Range) | 45 min | Équipe Backend |
| | | T2.1.4 | En tant que développeur je dois créer le formulaire QuestionType | 1h | Équipe Backend |
| | | T2.1.5 | En tant que développeur je dois créer QuestionController avec CRUD complet (index, new, show, edit, delete) | 2h | Équipe Backend |
| | | T2.1.6 | En tant que développeur frontend je dois créer les templates (new, edit, show, index) | 2h | Équipe Frontend |
| | | T2.1.7 | En tant que développeur je dois créer l'API endpoint GET /api/question/{id}/options | 30 min | Équipe Backend |
| | | T2.1.8 | En tant que testeur je dois tester la création et modification de questions | 1h | Équipe QA |
| **US-2.2** | **En tant qu'administrateur je veux définir les options de réponse afin de créer des QCM complets** | T2.2.1 | En tant que concepteur je dois préparer le diagramme de classe pour l'entité Option | 30 min | Équipe Backend |
| | | T2.2.2 | En tant qu'Admin BD je dois créer la table option avec colonnes: id, texte_option, est_correcte, question_id | 45 min | Équipe Backend |
| | | T2.2.3 | En tant que développeur je dois créer l'entité Option.php avec validation | 45 min | Équipe Backend |
| | | T2.2.4 | En tant que développeur je dois créer le formulaire OptionType avec CheckboxType pour est_correcte | 1h | Équipe Backend |
| | | T2.2.5 | En tant que développeur je dois créer OptionController avec CRUD complet | 2h | Équipe Backend |
| | | T2.2.6 | En tant que développeur frontend je dois créer les templates avec indicateur visuel pour bonne réponse | 2h | Équipe Frontend |
| | | T2.2.7 | En tant que testeur je dois tester la création et modification d'options | 1h | Équipe QA |
| **US-3.1** | **En tant qu'étudiant je veux voir les quiz disponibles pour mon chapitre afin de choisir lesquels passer** | T3.1.1 | En tant que concepteur je dois concevoir l'interface de liste des quiz style Kahoot | 1h | Équipe UX/UI |
| | | T3.1.2 | En tant que développeur je dois créer QuizController dans namespace FrontOffice | 1h | Équipe Backend |
| | | T3.1.3 | En tant que développeur frontend je dois créer le template list.html.twig avec design Kahoot | 3h | Équipe Frontend |
| | | T3.1.4 | En tant que développeur je dois implémenter la logique des tentatives dans QuizManagementService | 2h | Équipe Backend |
| | | T3.1.5 | En tant que développeur je dois créer les méthodes getStatistiquesEtudiant() et canStudentTakeQuiz() | 2h | Équipe Backend |
| | | T3.1.6 | En tant que testeur je dois tester l'affichage des quiz et des statistiques | 1h | Équipe QA |
| **US-3.2** | **En tant qu'étudiant je veux répondre aux questions d'un quiz afin d'être évalué sur mes connaissances** | T3.2.1 | En tant que concepteur je dois concevoir l'interface de passage de quiz | 1h | Équipe UX/UI |
| | | T3.2.2 | En tant que développeur je dois créer QuizPassageController avec actions start(), submit(), checkTime() | 2h | Équipe Backend |
| | | T3.2.3 | En tant que développeur je dois implémenter la gestion des sessions pour stocker la tentative en cours | 2h | Équipe Backend |
| | | T3.2.4 | En tant que développeur frontend je dois créer le template passage.html.twig style Kahoot | 4h | Équipe Frontend |
| | | T3.2.5 | En tant que développeur frontend je dois implémenter le timer JavaScript avec compte à rebours | 2h | Équipe Frontend |
| | | T3.2.6 | En tant que développeur je dois implémenter la randomisation dans prepareQuizForDisplay() | 1h | Équipe Backend |
| | | T3.2.7 | En tant que développeur frontend je dois ajouter la validation avant soumission | 1h | Équipe Frontend |
| | | T3.2.8 | En tant que testeur je dois tester le passage de quiz avec et sans timer | 1h | Équipe QA |
| **US-3.3** | **En tant qu'étudiant je veux voir mes résultats après un quiz afin de connaître ma performance** | T3.3.1 | En tant que concepteur je dois concevoir l'interface de résultats style Kahoot | 1h | Équipe UX/UI |
| | | T3.3.2 | En tant que développeur je dois implémenter calculateScore() dans QuizManagementService | 2h | Équipe Backend |
| | | T3.3.3 | En tant que développeur frontend je dois créer le template result.html.twig avec animations | 5h | Équipe Frontend |
| | | T3.3.4 | En tant que développeur je dois implémenter la logique de réussite (comparaison avec seuil) | 1h | Équipe Backend |
| | | T3.3.5 | En tant que développeur je dois implémenter enregistrerTentative() pour stocker les résultats | 2h | Équipe Backend |
| | | T3.3.6 | En tant que développeur frontend je dois ajouter les sons JavaScript (Web Audio API) | 1h | Équipe Frontend |
| | | T3.3.7 | En tant que développeur frontend je dois ajouter les animations CSS (fadeIn, slideDown, pulse) | 1h | Équipe Frontend |
| | | T3.3.8 | En tant que testeur je dois tester l'affichage des résultats avec différents scores | 1h | Équipe QA |
| **US-4.1** | **En tant qu'administrateur je veux limiter le nombre de tentatives par quiz afin de contrôler l'évaluation** | T4.1.1 | En tant que développeur je dois ajouter la propriété maxTentatives dans Quiz.php | 30 min | Équipe Backend |
| | | T4.1.2 | En tant que développeur je dois implémenter canStudentTakeQuiz() avec vérification de la limite | 2h | Équipe Backend |
| | | T4.1.3 | En tant que développeur je dois ajouter la validation côté contrôleur avant démarrage | 1h | Équipe Backend |
| | | T4.1.4 | En tant que développeur frontend je dois mettre à jour l'interface avec affichage des tentatives restantes | 1h | Équipe Frontend |
| | | T4.1.5 | En tant que testeur je dois tester la limitation des tentatives | 1h | Équipe QA |
| **US-4.2** | **En tant qu'étudiant je veux voir l'historique de mes tentatives afin de suivre ma progression** | T4.2.1 | En tant que développeur je dois créer getStatistiquesEtudiant() dans QuizManagementService | 2h | Équipe Backend |
| | | T4.2.2 | En tant que développeur je dois implémenter le stockage des résultats en session | 1h | Équipe Backend |
| | | T4.2.3 | En tant que développeur frontend je dois créer l'affichage des statistiques dans result.html.twig | 2h | Équipe Frontend |
| | | T4.2.4 | En tant que testeur je dois tester l'historique et les statistiques | 1h | Équipe QA |
| **US-5.1** | **En tant que système je veux qu'un quiz appartienne obligatoirement à un chapitre afin de maintenir la cohérence pédagogique** | T5.1.1 | En tant qu'Admin BD je dois ajouter la contrainte NOT NULL sur chapitre_id | 30 min | Équipe Backend |
| | | T5.1.2 | En tant que développeur je dois ajouter l'annotation NotNull dans Quiz.php | 30 min | Équipe Backend |
| | | T5.1.3 | En tant que développeur je dois configurer la validation dans QuizType (required) | 30 min | Équipe Backend |
| | | T5.1.4 | En tant que développeur je dois ajouter la validation côté contrôleur | 30 min | Équipe Backend |
| | | T5.1.5 | En tant que développeur je dois implémenter validateQuiz() dans QuizManagementService | 1h | Équipe Backend |
| | | T5.1.6 | En tant que testeur je dois tester la validation de la relation obligatoire | 30 min | Équipe QA |
| **US-6.1** | **En tant qu'utilisateur je veux une interface moderne et attractive afin d'avoir une expérience agréable** | T6.1.1 | En tant que designer je dois créer la charte graphique glass morphism | 2h | Équipe UX/UI |
| | | T6.1.2 | En tant que développeur frontend je dois créer le CSS glass morphism (backdrop-filter) | 3h | Équipe Frontend |
| | | T6.1.3 | En tant que développeur frontend je dois implémenter le style Kahoot (couleurs vives, typographie Inter) | 4h | Équipe Frontend |
| | | T6.1.4 | En tant que développeur frontend je dois ajouter les animations CSS/JS | 3h | Équipe Frontend |
| | | T6.1.5 | En tant que développeur frontend je dois rendre l'interface responsive avec media queries | 3h | Équipe Frontend |
| | | T6.1.6 | En tant que testeur je dois tester sur différents appareils (mobile, tablette, desktop) | 1h | Équipe QA |
| **US-7.1** | **En tant que système je veux protéger les formulaires contre les attaques afin de garantir la sécurité** | T7.1.1 | En tant que développeur je dois activer la protection CSRF dans security.yaml | 30 min | Équipe Backend |
| | | T7.1.2 | En tant que développeur je dois implémenter la validation serveur avec Symfony Validator | 2h | Équipe Backend |
| | | T7.1.3 | En tant que développeur je dois sécuriser l'affichage avec Twig auto-escape | 1h | Équipe Backend |
| | | T7.1.4 | En tant que développeur je dois ajouter la validation des permissions (IsGranted) | 1h | Équipe Backend |
| | | T7.1.5 | En tant que testeur je dois tester la sécurité des formulaires | 1h | Équipe QA |
| **US-7.2** | **En tant que système je veux contrôler l'accès aux fonctionnalités afin de respecter les rôles utilisateurs** | T7.2.1 | En tant que développeur je dois configurer les rôles ROLE_ADMIN et ROLE_ETUDIANT | 30 min | Équipe Backend |
| | | T7.2.2 | En tant que développeur je dois ajouter les annotations IsGranted sur les contrôleurs | 1h | Équipe Backend |
| | | T7.2.3 | En tant que développeur je dois implémenter les vérifications métier dans les contrôleurs | 1h | Équipe Backend |
| | | T7.2.4 | En tant que développeur frontend je dois créer la page 403 personnalisée | 30 min | Équipe Frontend |
| | | T7.2.5 | En tant que testeur je dois tester le contrôle d'accès avec différents rôles | 1h | Équipe QA |

---

## 📊 RÉCAPITULATIF DES ESTIMATIONS

| Epic | User Stories | Nombre de Tâches | Temps Total Estimé |
|------|--------------|------------------|-------------------|
| EPIC 1: Gestion Administrative Quiz | US-1.1, US-1.2, US-1.3, US-1.4 | 27 tâches | ~29h |
| EPIC 2: Gestion Questions et Options | US-2.1, US-2.2 | 15 tâches | ~16h |
| EPIC 3: Passage de Quiz (Front-office) | US-3.1, US-3.2, US-3.3 | 22 tâches | ~32h |
| EPIC 4: Système de Tentatives | US-4.1, US-4.2 | 9 tâches | ~9h |
| EPIC 5: Relation Quiz-Chapitre | US-5.1 | 6 tâches | ~3h |
| EPIC 6: Interface Utilisateur | US-6.1 | 6 tâches | ~16h |
| EPIC 7: Sécurité | US-7.1, US-7.2 | 10 tâches | ~8h |
| **TOTAL** | **14 User Stories** | **95 tâches** | **~113h** |

---

## 👥 RÉPARTITION PAR RÔLE

| Rôle | Nombre de Tâches | Temps Estimé |
|------|------------------|--------------|
| Équipe Backend | 45 tâches | ~52h |
| Équipe Frontend | 32 tâches | ~45h |
| Équipe UX/UI | 5 tâches | ~6h |
| Équipe QA | 13 tâches | ~13h |

---

## 📈 STATUT GLOBAL

- ✅ **Toutes les tâches sont TERMINÉES**
- ✅ **Vélocité: 100%**
- ✅ **Qualité: Aucun bug critique**
- ✅ **Couverture: Fonctionnalités complètes**

---

## 🎯 DÉFINITION OF DONE

Pour qu'une tâche soit considérée comme terminée, elle doit respecter:

1. ✅ Code écrit et fonctionnel
2. ✅ Validation côté serveur et client
3. ✅ Tests effectués et passés
4. ✅ Code review effectué
5. ✅ Documentation ajoutée
6. ✅ Pas d'erreurs ou warnings
7. ✅ Responsive (si frontend)
8. ✅ Sécurisé (CSRF, validation)

---

**Date de création:** [À compléter]  
**Sprint:** Sprint 1 - Gestion de Quiz  
**Équipe:** Backend, Frontend, UX/UI, QA  
**Product Owner:** [À compléter]  
**Scrum Master:** [À compléter]
