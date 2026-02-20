# 📋 Sprint Backlog - Module de Gestion de Quiz

## 🎯 Objectif du Sprint
Développer un système complet de gestion de quiz avec timer intelligent, style Kahoot, et fonctionnalités avancées.

---

## 📊 Tableau du Sprint Backlog

| ID US | User Story (US) | ID Tâche | Tâches : chaque membre (scrum team) énumère l'ensemble de ces tâches effectuées de l'analyse jusqu'à le test afin de réaliser ce user story | Estimation (en minute) | Responsable |
|-------|-----------------|----------|---------------------------------------------------------------------------------------------------------------------------------------------|------------------------|-------------|
| **US1** | **En tant qu'administrateur, je veux créer et gérer des quiz pour organiser les évaluations** | | | | |
| US1 | | T1.1 | En tant que concepteur, je dois préparer le diagramme de classe de conception (Quiz, Question, Option) | 45 min | Nom prénom |
| US1 | | T1.2 | En tant qu'Admin BD, je dois créer les tables quiz, question, option avec les relations | 1h 30min | Nom prénom |
| US1 | | T1.3 | En tant que développeur, je dois créer les entités Symfony (Quiz.php, Question.php, Option.php) avec annotations Doctrine | 2h | Nom prénom |
| US1 | | T1.4 | En tant que développeur, je dois créer le formulaire QuizType avec tous les champs (titre, description, chapitre, durée, seuil) | 1h 30min | Nom prénom |
| US1 | | T1.5 | En tant que développeur, je dois créer le contrôleur BackofficeQuizController avec CRUD complet | 3h | Nom prénom |
| US1 | | T1.6 | En tant que développeur, je dois créer les templates Twig pour le backoffice (index, new, edit, show) | 2h 30min | Nom prénom |
| US1 | | T1.7 | En tant que testeur, je dois préparer les cas de test pour la création/modification/suppression de quiz | 1h | Nom prénom |
| US1 | | T1.8 | En tant que testeur, je dois exécuter les tests et valider le CRUD | 45 min | Nom prénom |
| **US2** | **En tant qu'étudiant, je veux voir la liste des quiz disponibles pour choisir celui que je veux passer** | | | | |
| US2 | | T2.1 | En tant que concepteur, je dois concevoir la maquette de la page liste des quiz style Kahoot | 30 min | Nom prénom |
| US2 | | T2.2 | En tant que développeur, je dois créer la route et méthode dans QuizController pour lister les quiz par chapitre | 1h | Nom prénom |
| US2 | | T2.3 | En tant que développeur, je dois créer le template list.html.twig avec design Kahoot (fond violet, cartes colorées) | 2h 30min | Nom prénom |
| US2 | | T2.4 | En tant que développeur, je dois ajouter les icônes FontAwesome variées pour chaque quiz | 45 min | Nom prénom |
| US2 | | T2.5 | En tant que développeur, je dois implémenter le système de sons interactifs (hover, clic) | 1h | Nom prénom |
| US2 | | T2.6 | En tant que développeur, je dois rendre la page responsive pour mobile | 1h | Nom prénom |
| US2 | | T2.7 | En tant que testeur, je dois tester l'affichage sur différents navigateurs et tailles d'écran | 45 min | Nom prénom |
| **US3** | **En tant qu'étudiant, je veux passer un quiz avec un timer pour tester mes connaissances** | | | | |
| US3 | | T3.1 | En tant que concepteur, je dois concevoir la maquette de la page de passage avec timer style Kahoot | 45 min | Nom prénom |
| US3 | | T3.2 | En tant que développeur, je dois créer QuizPassageController avec méthode start() pour initialiser le quiz | 1h 30min | Nom prénom |
| US3 | | T3.3 | En tant que développeur, je dois implémenter la randomisation des questions et options | 1h | Nom prénom |
| US3 | | T3.4 | En tant que développeur, je dois créer le template passage.html.twig avec écran de chargement rotozoom | 2h | Nom prénom |
| US3 | | T3.5 | En tant que développeur, je dois implémenter le timer JavaScript avec changement de couleur (blanc→orange→rouge) | 2h 30min | Nom prénom |
| US3 | | T3.6 | En tant que développeur, je dois créer les gros blocs colorés pour les options (rouge, bleu, jaune, vert) | 1h 30min | Nom prénom |
| US3 | | T3.7 | En tant que développeur, je dois ajouter les icônes géométriques (▲ ◆ ● ■) sur les options | 30 min | Nom prénom |
| US3 | | T3.8 | En tant que développeur, je dois implémenter la barre de progression en bas de page | 1h | Nom prénom |
| US3 | | T3.9 | En tant que développeur, je dois créer la méthode checkTime() pour vérifier le temps côté serveur | 1h | Nom prénom |
| US3 | | T3.10 | En tant que développeur, je dois implémenter la soumission automatique si temps écoulé | 1h 30min | Nom prénom |
| US3 | | T3.11 | En tant que développeur, je dois ajouter tous les sons interactifs (démarrage, clic, sélection, soumission) | 1h 30min | Nom prénom |
| US3 | | T3.12 | En tant que testeur, je dois tester le timer avec différentes durées | 1h | Nom prénom |
| US3 | | T3.13 | En tant que testeur, je dois tester la soumission automatique et manuelle | 45 min | Nom prénom |
| **US4** | **En tant qu'étudiant, je veux voir mes résultats détaillés pour comprendre mes erreurs** | | | | |
| US4 | | T4.1 | En tant que concepteur, je dois concevoir la maquette de la page résultats style Kahoot | 30 min | Nom prénom |
| US4 | | T4.2 | En tant que développeur, je dois créer la méthode submit() pour calculer le score côté serveur | 2h | Nom prénom |
| US4 | | T4.3 | En tant que développeur, je dois implémenter la logique de calcul du pourcentage de réussite | 1h | Nom prénom |
| US4 | | T4.4 | En tant que développeur, je dois créer le template result.html.twig avec header animé | 2h | Nom prénom |
| US4 | | T4.5 | En tant que développeur, je dois créer le cercle de pourcentage avec gradient conique selon le score | 1h 30min | Nom prénom |
| US4 | | T4.6 | En tant que développeur, je dois ajouter les icônes selon le score (trophée, pouce, graphique, livre) | 45 min | Nom prénom |
| US4 | | T4.7 | En tant que développeur, je dois créer les cartes de détails avec bordures colorées (vert/rouge) | 1h 30min | Nom prénom |
| US4 | | T4.8 | En tant que développeur, je dois afficher les bonnes réponses et les réponses de l'utilisateur | 1h | Nom prénom |
| US4 | | T4.9 | En tant que développeur, je dois ajouter les badges de performance avec messages motivants | 1h | Nom prénom |
| US4 | | T4.10 | En tant que développeur, je dois implémenter les sons selon la performance (victoire/réussite/échec) | 1h | Nom prénom |
| US4 | | T4.11 | En tant que testeur, je dois tester l'affichage des résultats avec différents scores | 1h | Nom prénom |
| **US5** | **En tant qu'administrateur, je veux valider automatiquement les quiz pour garantir leur qualité** | | | | |
| US5 | | T5.1 | En tant que concepteur, je dois définir les règles de validation (min 2 questions, 2-4 options, 1 correcte) | 30 min | Nom prénom |
| US5 | | T5.2 | En tant que développeur, je dois créer la classe ValidQuiz constraint | 45 min | Nom prénom |
| US5 | | T5.3 | En tant que développeur, je dois créer QuizValidator avec toutes les règles métier | 2h | Nom prénom |
| US5 | | T5.4 | En tant que développeur, je dois ajouter l'annotation @ValidQuiz à l'entité Quiz | 15 min | Nom prénom |
| US5 | | T5.5 | En tant que testeur, je dois tester la validation avec des quiz invalides | 1h | Nom prénom |
| **US6** | **En tant qu'administrateur, je veux un service de gestion pour centraliser la logique métier** | | | | |
| US6 | | T6.1 | En tant que concepteur, je dois définir les 8 méthodes du service (validation, calcul, randomisation, etc.) | 45 min | Nom prénom |
| US6 | | T6.2 | En tant que développeur, je dois créer QuizManagementService avec injection de dépendances | 3h | Nom prénom |
| US6 | | T6.3 | En tant que développeur, je dois implémenter validateQuiz() | 1h | Nom prénom |
| US6 | | T6.4 | En tant que développeur, je dois implémenter calculateScore() | 1h | Nom prénom |
| US6 | | T6.5 | En tant que développeur, je dois implémenter randomizeQuestions() et randomizeOptions() | 1h 30min | Nom prénom |
| US6 | | T6.6 | En tant que développeur, je dois implémenter checkTimeLimit() | 1h | Nom prénom |
| US6 | | T6.7 | En tant que développeur, je dois implémenter canUserTakeQuiz() | 1h | Nom prénom |
| US6 | | T6.8 | En tant que développeur, je dois implémenter getQuizStatistics() | 1h 30min | Nom prénom |
| US6 | | T6.9 | En tant que testeur, je dois créer des tests unitaires pour chaque méthode du service | 2h | Nom prénom |
| **US7** | **En tant qu'utilisateur, je veux une interface cohérente style Kahoot sur toutes les pages** | | | | |
| US7 | | T7.1 | En tant que designer, je dois définir la charte graphique Kahoot (couleurs, typographie, animations) | 1h | Nom prénom |
| US7 | | T7.2 | En tant que développeur, je dois créer le fond violet avec motif diagonal pour toutes les pages | 30 min | Nom prénom |
| US7 | | T7.3 | En tant que développeur, je dois standardiser les animations (fadeInUp, slideDown, bounceIn, rotateIn) | 1h | Nom prénom |
| US7 | | T7.4 | En tant que développeur, je dois remplacer tous les emojis par des icônes FontAwesome professionnelles | 1h 30min | Nom prénom |
| US7 | | T7.5 | En tant que développeur, je dois créer les boutons en capsule avec gradients cohérents | 1h | Nom prénom |
| US7 | | T7.6 | En tant que développeur, je dois implémenter le système de sons sur toutes les pages | 2h | Nom prénom |
| US7 | | T7.7 | En tant que testeur, je dois vérifier la cohérence visuelle sur toutes les pages | 1h | Nom prénom |
| **US8** | **En tant qu'administrateur, je veux améliorer la page des chapitres avec le même style** | | | | |
| US8 | | T8.1 | En tant que développeur, je dois appliquer le fond violet avec motif diagonal | 30 min | Nom prénom |
| US8 | | T8.2 | En tant que développeur, je dois créer les cartes de chapitres avec barre colorée en haut | 1h | Nom prénom |
| US8 | | T8.3 | En tant que développeur, je dois ajouter les icônes variées dans des carrés colorés | 1h | Nom prénom |
| US8 | | T8.4 | En tant que développeur, je dois créer les 2 boutons d'action (Lire/Quiz) avec couleurs distinctes | 45 min | Nom prénom |
| US8 | | T8.5 | En tant que développeur, je dois ajouter les animations et sons | 1h | Nom prénom |
| US8 | | T8.6 | En tant que testeur, je dois tester la page chapitres sur mobile et desktop | 45 min | Nom prénom |

---

## 📈 Résumé des Estimations

### Par User Story

| User Story | Nombre de Tâches | Temps Total Estimé |
|------------|------------------|-------------------|
| US1 - CRUD Quiz Admin | 8 | 13h 30min |
| US2 - Liste des Quiz | 7 | 8h 30min |
| US3 - Passage du Quiz | 13 | 17h 45min |
| US4 - Résultats Détaillés | 11 | 13h 15min |
| US5 - Validation Auto | 5 | 4h 30min |
| US6 - Service Métier | 9 | 13h |
| US7 - Interface Cohérente | 7 | 8h |
| US8 - Page Chapitres | 6 | 5h |
| **TOTAL** | **66 tâches** | **83h 30min** |

### Par Type de Tâche

| Type de Tâche | Nombre | Temps Total |
|---------------|--------|-------------|
| Conception/Design | 8 | 5h 30min |
| Base de données | 2 | 3h |
| Développement Backend | 25 | 35h 30min |
| Développement Frontend | 20 | 26h 30min |
| Tests | 11 | 13h |
| **TOTAL** | **66** | **83h 30min** |

---

## 🎯 Objectifs de Sprint

### Sprint 1 (2 semaines) - Fondations
- ✅ US1: CRUD Quiz Admin
- ✅ US5: Validation automatique
- ✅ US6: Service de gestion

**Objectif**: Infrastructure de base fonctionnelle

### Sprint 2 (2 semaines) - Interface Utilisateur
- ✅ US2: Liste des quiz
- ✅ US3: Passage du quiz avec timer
- ✅ US7: Interface cohérente

**Objectif**: Expérience utilisateur complète

### Sprint 3 (1 semaine) - Finalisation
- ✅ US4: Résultats détaillés
- ✅ US8: Page chapitres améliorée

**Objectif**: Système complet et testé

---

## 📋 Critères d'Acceptation Globaux

### Fonctionnels
- ✅ Un administrateur peut créer/modifier/supprimer des quiz
- ✅ Les quiz sont validés automatiquement selon les règles métier
- ✅ Les étudiants peuvent voir la liste des quiz disponibles
- ✅ Les étudiants peuvent passer un quiz avec timer
- ✅ Le timer change de couleur selon le temps restant
- ✅ La soumission est automatique si le temps expire
- ✅ Les résultats sont calculés côté serveur uniquement
- ✅ Les résultats détaillés montrent les bonnes/mauvaises réponses
- ✅ L'interface est cohérente style Kahoot sur toutes les pages

### Non-Fonctionnels
- ✅ Design responsive (mobile, tablette, desktop)
- ✅ Temps de chargement < 2 secondes
- ✅ Compatibilité navigateurs (Chrome, Firefox, Safari, Edge)
- ✅ Accessibilité (icônes avec texte alternatif)
- ✅ Sécurité (validation serveur, pas de réponses en frontend)
- ✅ Code maintenable et documenté

---

## 🔧 Technologies Utilisées

- **Backend**: Symfony 6.x, PHP 8.x
- **Base de données**: MySQL/MariaDB
- **Frontend**: Twig, HTML5, CSS3, JavaScript (Vanilla)
- **Design**: Style Kahoot (fond violet, blocs colorés)
- **Icônes**: FontAwesome
- **Typographie**: Inter (Google Fonts)
- **Audio**: Web Audio API

---

## 📚 Documentation Associée

- `STRUCTURE_FINALE_QUIZ.md` - Architecture complète
- `AMELIORATIONS_KAHOOT_STYLE.md` - Guide de style
- `ICONES_PROFESSIONNELLES.md` - Liste des icônes
- `BUNDLES_RECOMMANDES_QUIZ.md` - Bundles Symfony

---

## 👥 Équipe Scrum

- **Product Owner**: [Nom]
- **Scrum Master**: [Nom]
- **Développeurs**: [Noms]
- **Testeurs**: [Noms]
- **Designer**: [Nom]

---

## 📅 Planning

- **Durée totale**: 5 semaines
- **Sprint 1**: Semaines 1-2 (Fondations)
- **Sprint 2**: Semaines 3-4 (Interface)
- **Sprint 3**: Semaine 5 (Finalisation)
- **Revue de sprint**: Fin de chaque sprint
- **Rétrospective**: Après chaque sprint

---

## ✅ Définition of Done (DoD)

Une tâche est considérée comme terminée quand:
1. Le code est écrit et fonctionne
2. Les tests sont passés avec succès
3. Le code est documenté (commentaires, PHPDoc)
4. Le code est revu par un pair (code review)
5. Le design est responsive
6. La fonctionnalité est testée sur différents navigateurs
7. La documentation utilisateur est mise à jour
8. Le Product Owner a validé la fonctionnalité

---

**Date de création**: 18/02/2026  
**Version**: 1.0  
**Statut**: ✅ Complété
