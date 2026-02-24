# SPRINT BACKLOG - GESTION DE QUIZ
## Version Simplifiée avec Phrases Simples

**Note importante:** Pour un user story il faut mettre tous les tâches effectuées afin de la réaliser de la conception jusqu'à le test de scénario.

---

## US 3.1 - En tant qu'administrateur, je souhaite créer un quiz

### T3.1.1 - Préparer le diagramme de classe (30 min)
**Responsable:** Concepteur  
**Description:** Je dois préparer le diagramme de classe pour les entités Quiz, Question et Option.  
**Fichiers concernés:** Diagramme UML (StarUML, Draw.io)

### T3.1.2 - Créer les tables en base de données (1h)
**Responsable:** Admin BD  
**Description:** Je dois créer les tables quiz, question et option dans la base de données avec toutes les colonnes nécessaires.  
**Fichiers concernés:** migrations/VersionXXXX.php

### T3.1.3 - Créer les entités (1h 30min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer les entités Quiz, Question et Option avec toutes leurs propriétés et validations.  
**Fichiers concernés:**
- src/Entity/Quiz.php
- src/Entity/Question.php
- src/Entity/Option.php

### T3.1.4 - Créer le formulaire (1h)
**Responsable:** Développeur Backend  
**Description:** Je dois créer le formulaire de création de quiz avec tous les champs nécessaires.  
**Fichiers concernés:** src/Form/QuizType.php

### T3.1.5 - Créer la page de création (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la page de création de quiz dans le backoffice avec gestion du formulaire et sauvegarde en base de données.  
**Fichiers concernés:**
- src/Controller/Backoffice/QuizController.php
- src/Service/QuizManagementService.php

### T3.1.6 - Créer l'interface utilisateur (2h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer l'interface de création de quiz avec un design moderne et responsive.  
**Fichiers concernés:**
- templates/backoffice/quiz/new.html.twig
- public/Backoffice/css/custom-forms.css

### T3.1.7 - Tester la fonctionnalité (1h 30min)
**Responsable:** Testeur QA  
**Description:** Je dois tester la création de quiz avec différents scénarios et documenter les résultats.  
**Tests:** Création valide, champs obligatoires, validation des contraintes, sécurité

---

## US 3.2 - En tant qu'administrateur, je souhaite modifier un quiz

### T3.2.1 - Créer la page de modification (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la page de modification de quiz qui récupère les données existantes.  
**Fichiers concernés:** src/Controller/Backoffice/QuizController.php

### T3.2.2 - Créer l'interface de modification (1h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer l'interface de modification avec le formulaire pré-rempli.  
**Fichiers concernés:** templates/backoffice/quiz/edit.html.twig

### T3.2.3 - Valider les modifications (30 min)
**Responsable:** Développeur Backend  
**Description:** Je dois valider les modifications et mettre à jour les données en base.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.2.4 - Ajouter les messages de confirmation (30 min)
**Responsable:** Développeur Backend  
**Description:** Je dois ajouter les messages de confirmation après modification.  
**Fichiers concernés:** src/Controller/Backoffice/QuizController.php

### T3.2.5 - Tester la modification (1h)
**Responsable:** Testeur QA  
**Description:** Je dois tester la modification de quiz avec différents cas.  
**Tests:** Modification valide, quiz non trouvé, validation, persistance

---

## US 3.3 - En tant qu'administrateur, je souhaite supprimer un quiz

### T3.3.1 - Créer la fonctionnalité de suppression (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la fonctionnalité de suppression de quiz avec protection.  
**Fichiers concernés:** src/Controller/Backoffice/QuizController.php

### T3.3.2 - Ajouter la confirmation (1h)
**Responsable:** Développeur Frontend  
**Description:** Je dois ajouter une confirmation avant suppression.  
**Fichiers concernés:**
- templates/backoffice/quiz_management.html.twig
- public/Backoffice/js/templatemo-glass-admin-script.js

### T3.3.3 - Configurer la suppression en cascade (30 min)
**Responsable:** Admin BD  
**Description:** Je dois configurer la suppression automatique des questions et options liées.  
**Fichiers concernés:**
- src/Entity/Quiz.php
- src/Entity/Question.php

### T3.3.4 - Ajouter les messages (30 min)
**Responsable:** Développeur Backend  
**Description:** Je dois ajouter les messages de confirmation après suppression.  
**Fichiers concernés:** src/Controller/Backoffice/QuizController.php

### T3.3.5 - Tester la suppression (1h)
**Responsable:** Testeur QA  
**Description:** Je dois vérifier que la suppression fonctionne correctement avec cascade.  
**Tests:** Suppression valide, confirmation, cascade, sécurité

---

## US 3.4 - En tant qu'administrateur, je souhaite consulter la liste des quiz

### T3.4.1 - Concevoir l'interface (1h 30min)
**Responsable:** Concepteur UX/UI  
**Description:** Je dois concevoir l'interface de liste hiérarchique des quiz.  
**Livrables:** Maquettes wireframe, design glass morphism

### T3.4.2 - Créer la page de liste (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la page qui affiche tous les quiz.  
**Fichiers concernés:**
- src/Controller/BackofficeController.php
- src/Repository/QuizRepository.php

### T3.4.3 - Créer les API (1h 30min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer les API pour récupérer les questions et options.  
**Fichiers concernés:** src/Controller/BackofficeController.php

### T3.4.4 - Créer l'interface de liste (2h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer l'interface de liste avec structure hiérarchique.  
**Fichiers concernés:** templates/backoffice/quiz_management.html.twig

### T3.4.5 - Ajouter le chargement dynamique (2h 30min)
**Responsable:** Développeur Frontend  
**Description:** Je dois ajouter le chargement dynamique des questions et options.  
**Fichiers concernés:** public/Backoffice/js/templatemo-glass-admin-script.js

### T3.4.6 - Tester l'affichage (1h 30min)
**Responsable:** Testeur QA  
**Description:** Je dois tester l'affichage et les interactions de la liste.  
**Tests:** Affichage, chargement AJAX, responsive, multi-navigateurs

---

## US 3.5 - En tant qu'administrateur, je souhaite ajouter une question à un quiz

### T3.5.1 - Préparer le diagramme (30 min)
**Responsable:** Concepteur  
**Description:** Je dois préparer le diagramme de classe pour l'entité Question.  
**Fichiers concernés:** Diagramme UML

### T3.5.2 - Créer la table question (45 min)
**Responsable:** Admin BD  
**Description:** Je dois créer la table question dans la base de données.  
**Fichiers concernés:** migrations/VersionXXXX.php

### T3.5.3 - Créer l'entité Question (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer l'entité Question avec ses propriétés.  
**Fichiers concernés:** src/Entity/Question.php

### T3.5.4 - Créer le formulaire de question (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer le formulaire de question.  
**Fichiers concernés:** src/Form/QuestionType.php

### T3.5.5 - Créer les pages de gestion (2h)
**Responsable:** Développeur Backend + Frontend  
**Description:** Je dois créer les pages de gestion des questions (création, modification, suppression).  
**Fichiers concernés:**
- src/Controller/Backoffice/QuestionController.php
- templates/backoffice/question/new.html.twig
- templates/backoffice/question/edit.html.twig

### T3.5.6 - Tester la gestion des questions (1h)
**Responsable:** Testeur QA  
**Description:** Je dois tester la gestion des questions.  
**Tests:** Création, modification, suppression, validation

---

## US 3.6 - En tant qu'administrateur, je souhaite ajouter/modifier/supprimer des options

### T3.6.1 - Préparer le diagramme (30 min)
**Responsable:** Concepteur  
**Description:** Je dois préparer le diagramme de classe pour l'entité Option.  
**Fichiers concernés:** Diagramme UML

### T3.6.2 - Créer la table option (45 min)
**Responsable:** Admin BD  
**Description:** Je dois créer la table option dans la base de données.  
**Fichiers concernés:** migrations/VersionXXXX.php

### T3.6.3 - Créer l'entité Option (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer l'entité Option avec ses propriétés.  
**Fichiers concernés:** src/Entity/Option.php

### T3.6.4 - Créer le formulaire d'option (45 min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer le formulaire d'option.  
**Fichiers concernés:** src/Form/OptionType.php

### T3.6.5 - Créer les pages de gestion (2h 30min)
**Responsable:** Développeur Backend + Frontend  
**Description:** Je dois créer les pages de gestion des options avec indicateur visuel pour les bonnes réponses.  
**Fichiers concernés:**
- src/Controller/Backoffice/OptionController.php
- templates/backoffice/option/new.html.twig
- templates/backoffice/option/edit.html.twig

### T3.6.6 - Tester la gestion des options (1h)
**Responsable:** Testeur QA  
**Description:** Je dois tester la gestion des options.  
**Tests:** Création, modification, suppression, marquage bonne réponse

---

## US 3.7 - En tant qu'étudiant, je souhaite consulter un quiz

### T3.7.1 - Concevoir l'interface (1h 30min)
**Responsable:** Concepteur UX/UI  
**Description:** Je dois concevoir l'interface de liste des quiz style Kahoot.  
**Livrables:** Maquettes, cartes colorées, design responsive

### T3.7.2 - Créer la page de liste (1h 30min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la page qui affiche les quiz d'un chapitre.  
**Fichiers concernés:** src/Controller/FrontOffice/QuizController.php

### T3.7.3 - Calculer les statistiques (2h)
**Responsable:** Développeur Backend  
**Description:** Je dois calculer les tentatives restantes et les scores de l'étudiant.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.7.4 - Créer l'interface de liste (2h 30min)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer l'interface de liste avec design Kahoot.  
**Fichiers concernés:**
- templates/frontoffice/quiz/list.html.twig
- public/frontoffice/css/quiz-style.css

### T3.7.5 - Tester l'affichage (1h)
**Responsable:** Testeur QA  
**Description:** Je dois tester l'affichage de la liste des quiz.  
**Tests:** Affichage, filtrage, statistiques, responsivité

---

## US 3.8 - En tant qu'étudiant, je souhaite démarrer un quiz

### T3.8.1 - Concevoir l'interface de passage (1h 30min)
**Responsable:** Concepteur UX/UI  
**Description:** Je dois concevoir l'interface de passage de quiz style Kahoot.  
**Livrables:** Maquettes, grandes cartes colorées, timer

### T3.8.2 - Créer la page de démarrage (1h 30min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la page de démarrage de quiz avec vérifications.  
**Fichiers concernés:** src/Controller/FrontOffice/QuizPassageController.php

### T3.8.3 - Randomiser les questions (1h)
**Responsable:** Développeur Backend  
**Description:** Je dois randomiser l'ordre des questions et des options.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.8.4 - Créer l'interface de passage (3h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer l'interface de passage avec design Kahoot.  
**Fichiers concernés:** templates/frontoffice/quiz/passage.html.twig

### T3.8.5 - Ajouter le timer (2h)
**Responsable:** Développeur Frontend  
**Description:** Je dois ajouter le timer avec compte à rebours et soumission automatique.  
**Fichiers concernés:** public/frontoffice/js/quiz-timer.js

### T3.8.6 - Ajouter la validation (1h)
**Responsable:** Développeur Frontend  
**Description:** Je dois ajouter la validation avant soumission.  
**Fichiers concernés:** public/frontoffice/js/quiz-timer.js

### T3.8.7 - Tester le passage de quiz (1h 30min)
**Responsable:** Testeur QA  
**Description:** Je dois tester le démarrage et le passage du quiz.  
**Tests:** Démarrage, randomisation, timer, validation

---

## US 3.9 - En tant qu'étudiant, je souhaite soumettre mes réponses

### T3.9.1 - Créer la soumission (1h 30min)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la fonctionnalité de soumission des réponses.  
**Fichiers concernés:** src/Controller/FrontOffice/QuizPassageController.php

### T3.9.2 - Calculer le score (2h)
**Responsable:** Développeur Backend  
**Description:** Je dois calculer le score de l'étudiant.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.9.3 - Enregistrer la tentative (1h)
**Responsable:** Développeur Backend  
**Description:** Je dois enregistrer la tentative en session.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.9.4 - Gérer la redirection (30 min)
**Responsable:** Développeur Backend  
**Description:** Je dois rediriger vers la page de résultats.  
**Fichiers concernés:** src/Controller/FrontOffice/QuizPassageController.php

### T3.9.5 - Tester la soumission (1h 30min)
**Responsable:** Testeur QA  
**Description:** Je dois tester la soumission et le calcul du score.  
**Tests:** Soumission valide, calcul score, temps, sécurité

---

## US 3.10 - En tant qu'étudiant, je souhaite voir mon score immédiatement

### T3.10.1 - Concevoir l'interface de résultats (1h)
**Responsable:** Concepteur UX/UI  
**Description:** Je dois concevoir l'interface de résultats style Kahoot.  
**Livrables:** Maquettes, couleurs dynamiques, animations

### T3.10.2 - Créer la page de résultats (1h)
**Responsable:** Développeur Backend  
**Description:** Je dois créer la page qui affiche les résultats.  
**Fichiers concernés:** src/Controller/FrontOffice/QuizPassageController.php

### T3.10.3 - Créer l'interface de résultats (3h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer l'interface de résultats avec tous les détails.  
**Fichiers concernés:** templates/frontoffice/quiz/result.html.twig

### T3.10.4 - Ajouter les animations (1h 30min)
**Responsable:** Développeur Frontend  
**Description:** Je dois ajouter les animations CSS.  
**Fichiers concernés:** public/frontoffice/css/quiz-style.css

### T3.10.5 - Ajouter les sons (1h 30min)
**Responsable:** Développeur Frontend  
**Description:** Je dois ajouter les sons de victoire et d'encouragement.  
**Fichiers concernés:** public/frontoffice/js/result-animations.js

### T3.10.6 - Tester l'affichage des résultats (1h)
**Responsable:** Testeur QA  
**Description:** Je dois tester l'affichage des résultats.  
**Tests:** Affichage, couleurs, animations, sons, responsivité

---

## US 3.11 - En tant qu'étudiant, je souhaite consulter l'historique de mes tentatives

### T3.11.1 - Calculer les statistiques (2h)
**Responsable:** Développeur Backend  
**Description:** Je dois calculer les statistiques globales de l'étudiant.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.11.2 - Gérer le stockage (1h)
**Responsable:** Développeur Backend  
**Description:** Je dois gérer le stockage des tentatives en session.  
**Fichiers concernés:** src/Service/QuizManagementService.php

### T3.11.3 - Créer le tableau d'historique (2h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer le tableau qui affiche toutes les tentatives.  
**Fichiers concernés:** templates/frontoffice/quiz/result.html.twig

### T3.11.4 - Créer la section statistiques (1h)
**Responsable:** Développeur Frontend  
**Description:** Je dois créer la section qui affiche les statistiques globales.  
**Fichiers concernés:** templates/frontoffice/quiz/result.html.twig

### T3.11.5 - Tester l'historique (1h)
**Responsable:** Testeur QA  
**Description:** Je dois tester l'affichage de l'historique et des statistiques.  
**Tests:** Affichage, tri, calculs, persistance, responsivité

---

## 📊 RÉSUMÉ DU SPRINT

| Métrique | Valeur |
|----------|--------|
| **Total User Stories** | 11 |
| **Total Tâches** | 62 |
| **Temps Total Estimé** | ~75 heures |
| **Statut** | ✅ 100% IMPLÉMENTÉ |

---

## ✅ STATUT FINAL

Toutes les fonctionnalités ont été implémentées, testées et validées.  
Le système de gestion de quiz est prêt pour la production.

**Date:** 22 février 2026  
**Version:** 1.0.0  
**Statut:** Production Ready
