# � GUIDE DÉTAILLÉ DES FICHIERS - SYSTÈME DE QUIZ

## 🎯 Vue d'ensemble

Ce document explique le rôle et le contenu de chaque fichier du système de quiz, sans code, pour faciliter la compréhension lors de la soutenance.

---

## � 1. ENTITÉS (src/Entity/)

### Quiz.php
**Rôle** : Représente un quiz dans la base de données

**Contenu** :
- Propriétés : id, titre, description, état, durée maximale, seuil de réussite, nombre max de tentatives
- Relation avec Chapitre (un quiz appartient à un chapitre)
- Relation avec Questions (un quiz contient plusieurs questions)
- Support d'image (nom, taille, fichier)
- Validations : titre obligatoire (3-255 caractères), description obligatoire (10-2000 caractères), état parmi (actif, inactif, brouillon, archive), seuil entre 0-100%

**Utilité** : 
- Définit la structure d'un quiz en base de données
- Assure l'intégrité des données via les validations
- Gère les relations avec les autres entités

---

### Question.php
**Rôle** : Représente une question d'un quiz

**Contenu** :
- Propriétés : id, texte de la question, nombre de points
- Relation avec Quiz (une question appartient à un quiz)
- Relation avec Options (une question a plusieurs options de réponse)
- Support multimédia : image, audio, vidéo (noms et tailles de fichiers)
- Validations : texte obligatoire (10-1000 caractères), points entre 1-100

**Utilité** :
- Stocke les questions avec leur valeur en points
- Permet d'ajouter des médias pour enrichir les questions
- Lie les questions aux options de réponse

---

### Option.php
**Rôle** : Représente une option de réponse pour une question QCM

**Contenu** :
- Propriétés : id, texte de l'option, booléen "est correcte"
- Relation avec Question (une option appartient à une question)
- Validations : texte obligatoire (1-255 caractères), statut correct/incorrect obligatoire

**Utilité** :
- Stocke les différentes réponses possibles
- Indique quelle option est la bonne réponse
- Permet la correction automatique

---

## 📂 2. CONTRÔLEURS (src/Controller/)

### Backoffice/QuizController.php
**Rôle** : Gère toutes les actions administrateur sur les quiz

**Méthodes principales** :
1. **index()** : Affiche la liste de tous les quiz
2. **new()** : Crée un nouveau quiz manuellement
3. **edit()** : Modifie un quiz existant
4. **delete()** : Supprime un quiz
5. **show()** : Affiche les détails d'un quiz
6. **selectChapitre()** : Affiche la liste des chapitres pour générer un quiz
7. **generateFromChapitre()** : Lance la génération automatique via IA
8. **regenerate()** : Régénère les questions d'un quiz existant
9. **getQuestions()** : API pour récupérer les questions d'un quiz

**Utilité** :
- Point d'entrée pour toutes les opérations admin
- Valide les données avec QuizManagementService
- Gère les appels au service de génération IA
- Affiche les messages de succès/erreur
- Protégé par ROLE_ADMIN

---

### FrontOffice/QuizController.php
**Rôle** : Gère l'affichage des quiz pour les étudiants

**Méthodes principales** :
1. **list()** : Affiche tous les quiz disponibles pour un chapitre
2. **show()** : Affiche les détails d'un quiz avant de le commencer

**Utilité** :
- Permet aux étudiants de voir les quiz disponibles
- Affiche les informations : nombre de questions, durée, seuil de réussite
- Montre le nombre de tentatives restantes
- Affiche le meilleur score obtenu
- Accessible aux étudiants connectés

---

### FrontOffice/QuizPassageController.php
**Rôle** : Gère le passage et la soumission des quiz par les étudiants

**Méthodes principales** :
1. **start()** : Démarre une nouvelle tentative de quiz
2. **passage()** : Affiche l'interface de passage avec les questions
3. **submit()** : Reçoit les réponses et effectue la correction
4. **result()** : Affiche les résultats détaillés

**Utilité** :
- Crée une participation (tentative) en base de données
- Gère le timer si durée limitée
- Enregistre les réponses de l'étudiant
- Calcule le score automatiquement
- Détermine si le quiz est réussi ou échoué
- Affiche les bonnes/mauvaises réponses
- Protégé par ROLE_ETUDIANT

---

### FrontOffice/QuizTutorController.php
**Rôle** : Fournit une aide IA pendant le passage du quiz

**Méthodes principales** :
1. **askHelp()** : Reçoit une question de l'étudiant et retourne une aide

**Utilité** :
- Permet aux étudiants de poser des questions sur une question du quiz
- Utilise l'IA pour fournir des indices sans donner la réponse
- Encourage la réflexion plutôt que de donner la solution
- Améliore l'expérience d'apprentissage

---

## 📂 3. SERVICES (src/Service/)

### QuizManagementService.php
**Rôle** : Contient toute la logique métier liée aux quiz

**Méthodes principales** :
1. **validateQuizBusinessRules()** : Valide les règles métier d'un quiz
   - Vérifie qu'il y a au moins 1 question
   - Vérifie que chaque question a au moins 2 options
   - Vérifie qu'il y a exactement 1 bonne réponse par question
   - Vérifie que le seuil de réussite est valide

2. **calculerStatistiques()** : Calcule les statistiques d'un quiz
   - Nombre de participations
   - Taux de réussite
   - Score moyen, min, max
   - Questions les plus difficiles

3. **verifierTentativesRestantes()** : Vérifie si un étudiant peut encore passer le quiz
   - Compte les tentatives déjà effectuées
   - Compare avec le maximum autorisé
   - Retourne si l'étudiant peut passer le quiz

4. **obtenirMeilleurScore()** : Récupère le meilleur score d'un étudiant sur un quiz

**Utilité** :
- Centralise la logique métier
- Évite la duplication de code
- Facilite les tests unitaires
- Assure la cohérence des règles

---

### GrokQuizGeneratorService.php
**Rôle** : Génère automatiquement des quiz via l'API Groq (IA)

**Méthodes principales** :
1. **genererQuizPourChapitre()** : Génère un quiz complet
   - Extrait le contenu du chapitre
   - Construit un prompt pour l'IA
   - Appelle l'API Groq
   - Valide la réponse JSON
   - Crée les entités Quiz, Question, Option
   - Sauvegarde en base de données

2. **regenererQuestions()** : Régénère les questions d'un quiz existant
   - Supprime les anciennes questions
   - Génère de nouvelles questions
   - Conserve les paramètres du quiz

3. **appellerApiGroq()** : Gère la communication avec l'API
   - Configure les headers (clé API)
   - Envoie le prompt
   - Gère les erreurs de connexion
   - Gère les timeouts
   - Retourne la réponse JSON

4. **extraireContenuChapitre()** : Extrait et nettoie le contenu
   - Supprime les balises HTML
   - Limite à 4000 caractères pour l'API

5. **construirePrompt()** : Crée le prompt pour l'IA
   - Définit le contexte (expert pédagogique)
   - Spécifie le nombre de questions
   - Indique la difficulté
   - Définit le format JSON attendu

6. **validerEtNormaliserQuestions()** : Valide la réponse de l'IA
   - Vérifie la structure JSON
   - Vérifie que chaque question a un texte
   - Vérifie qu'il y a au moins 2 options
   - Vérifie qu'il y a une réponse correcte
   - Lance des exceptions si invalide

**Utilité** :
- Innovation majeure du projet
- Économise énormément de temps aux enseignants
- Génère des questions pertinentes et variées
- Adapte la difficulté selon les besoins

---

### QuizCorrectorAIService.php
**Rôle** : Corrige les quiz avec des explications pédagogiques générées par IA

**Méthodes principales** :
1. **corrigerAvecExplications()** : Corrige et génère des explications
   - Calcule le score
   - Pour chaque mauvaise réponse, génère une explication via IA
   - Fournit des recommandations personnalisées
   - Retourne un rapport détaillé

2. **genererExplicationIA()** : Génère une explication pour une erreur
   - Envoie la question et les réponses à l'IA
   - Demande une explication pédagogique
   - Retourne un texte encourageant et instructif

3. **analyserErreurs()** : Analyse les erreurs récurrentes
   - Identifie les thèmes difficiles
   - Suggère des ressources de révision

**Utilité** :
- Correction automatique instantanée
- Explications personnalisées pour chaque erreur
- Aide l'étudiant à comprendre ses erreurs
- Améliore l'apprentissage

---

### QuizTutorAIService.php
**Rôle** : Assistant IA qui aide les étudiants pendant le quiz

**Méthodes principales** :
1. **obtenirAide()** : Fournit une aide contextuelle
   - Reçoit la question du quiz et la question de l'étudiant
   - Demande à l'IA de fournir des indices
   - Ne donne JAMAIS la réponse directe
   - Encourage la réflexion

2. **analyserDifficultes()** : Analyse les difficultés d'un étudiant
   - Identifie les thèmes où l'étudiant a des difficultés
   - Suggère des ressources
   - Recommande des exercices

**Utilité** :
- Aide personnalisée pendant le quiz
- Encourage l'apprentissage actif
- Guide sans donner la solution
- Améliore l'autonomie de l'étudiant

---

## 📂 4. FORMULAIRES (src/Form/)

### QuizType.php
**Rôle** : Formulaire de création/édition de quiz

**Champs** :
- Titre (champ texte)
- Description (zone de texte)
- Chapitre (liste déroulante)
- État (choix : actif, inactif, brouillon, archive)
- Durée maximale en minutes (nombre, optionnel)
- Seuil de réussite en % (nombre, 0-100)
- Nombre max de tentatives (nombre, optionnel)
- Image (upload de fichier, optionnel)

**Utilité** :
- Interface pour créer/modifier un quiz
- Validation côté client et serveur
- Gestion de l'upload d'image
- Réutilisable dans new.html.twig et edit.html.twig

---

### QuestionType.php
**Rôle** : Formulaire pour créer/modifier une question

**Champs** :
- Texte de la question (zone de texte)
- Points (nombre, 1-100)
- Quiz associé (liste déroulante)
- Image (upload, optionnel)
- Audio (upload, optionnel)
- Vidéo (upload, optionnel)
- Collection d'options (sous-formulaire répétable)

**Sous-formulaire Option** :
- Texte de l'option
- Case à cocher "Est correcte"

**Utilité** :
- Création de questions avec médias
- Gestion dynamique des options (ajout/suppression)
- Validation des données

---

## 📂 5. REPOSITORY (src/Repository/)

### QuizRepository.php
**Rôle** : Requêtes personnalisées pour récupérer des quiz

**Méthodes personnalisées possibles** :
1. **findActiveQuizzes()** : Récupère uniquement les quiz actifs
2. **findByChapitreAndEtat()** : Filtre par chapitre et état
3. **findQuizzesWithStatistics()** : Récupère les quiz avec leurs statistiques
4. **findPopularQuizzes()** : Quiz les plus passés
5. **searchByTitle()** : Recherche par titre

**Utilité** :
- Requêtes optimisées pour des besoins spécifiques
- Évite les requêtes N+1
- Améliore les performances
- Centralise les requêtes complexes

---

## 📂 6. TEMPLATES BACKOFFICE (templates/backoffice/quiz/)

### index.html.twig
**Rôle** : Liste de tous les quiz (page d'administration)

**Contenu** :
- Tableau avec tous les quiz
- Colonnes : Titre, Chapitre, État, Nb questions, Nb participations, Actions
- Boutons : Voir, Modifier, Supprimer
- Bouton "Nouveau Quiz"
- Bouton "Générer avec IA"
- Filtres par état
- Barre de recherche

**Utilité** :
- Vue d'ensemble de tous les quiz
- Accès rapide aux actions
- Gestion centralisée

---

### new.html.twig
**Rôle** : Page de création d'un nouveau quiz

**Contenu** :
- Titre de la page "Nouveau Quiz"
- Formulaire QuizType
- Bouton "Créer"
- Bouton "Annuler" (retour à la liste)
- Messages d'erreur si validation échoue

**Utilité** :
- Création manuelle de quiz
- Interface simple et claire
- Validation en temps réel

---

### edit.html.twig
**Rôle** : Page de modification d'un quiz existant

**Contenu** :
- Titre "Modifier le Quiz"
- Formulaire QuizType pré-rempli
- Liste des questions existantes
- Boutons pour ajouter/modifier/supprimer des questions
- Bouton "Régénérer les questions" (via IA)
- Bouton "Enregistrer"
- Bouton "Supprimer le quiz"

**Utilité** :
- Modification complète du quiz
- Gestion des questions
- Possibilité de régénération

---

### show.html.twig
**Rôle** : Affichage détaillé d'un quiz (lecture seule)

**Contenu** :
- Toutes les informations du quiz
- Liste complète des questions avec leurs options
- Indication des bonnes réponses
- Statistiques : nb participations, taux de réussite, score moyen
- Boutons : Modifier, Supprimer, Retour

**Utilité** :
- Prévisualisation du quiz
- Vérification avant publication
- Consultation des statistiques

---

### _form.html.twig
**Rôle** : Formulaire réutilisable pour new et edit

**Contenu** :
- Rendu du formulaire QuizType
- Styles personnalisés
- Gestion des erreurs
- Bouton de soumission paramétrable

**Utilité** :
- Évite la duplication de code
- Cohérence visuelle
- Facilite la maintenance

---

### select_chapitre.html.twig
**Rôle** : Sélection du chapitre pour générer un quiz via IA

**Contenu** :
- Liste de tous les chapitres disponibles
- Pour chaque chapitre : titre, extrait du contenu, nombre de mots
- Bouton "Générer un quiz" pour chaque chapitre
- Indication si un quiz existe déjà pour ce chapitre

**Utilité** :
- Première étape de la génération IA
- Vue d'ensemble des chapitres
- Facilite le choix

---

### generate.html.twig
**Rôle** : Interface de configuration et lancement de la génération IA

**Contenu** :
- Titre du chapitre sélectionné
- Extrait du contenu
- Formulaire de configuration :
  * Titre du quiz (pré-rempli)
  * Description
  * Nombre de questions (1-10)
  * Difficulté (facile, moyen, difficile)
  * Seuil de réussite
  * Durée maximale
  * Nombre de tentatives
- Bouton "Générer le quiz"
- Indicateur de chargement pendant la génération
- Messages de succès/erreur

**Utilité** :
- Configuration fine de la génération
- Feedback visuel pendant le traitement
- Gestion des erreurs API

---

## 📂 7. TEMPLATES FRONTOFFICE (templates/frontoffice/quiz/)

### list.html.twig
**Rôle** : Liste des quiz disponibles pour un chapitre (vue étudiant)

**Contenu** :
- Titre du chapitre
- Cartes pour chaque quiz avec :
  * Titre et description
  * Image si disponible
  * Nombre de questions
  * Durée estimée
  * Seuil de réussite
  * Votre meilleur score
  * Tentatives restantes
  * Bouton "Commencer" ou "Reprendre"
- Indication si quiz déjà réussi
- Indication si plus de tentatives disponibles

**Utilité** :
- Vue d'ensemble des quiz du chapitre
- Informations claires avant de commencer
- Suivi de la progression

---

### start.html.twig
**Rôle** : Page de démarrage avant de commencer le quiz

**Contenu** :
- Titre du quiz
- Description complète
- Règles :
  * Nombre de questions
  * Durée (si limitée)
  * Seuil de réussite
  * Tentatives restantes
  * Possibilité de revenir en arrière ou non
- Bouton "Commencer le quiz"
- Bouton "Annuler"
- Historique de vos tentatives précédentes

**Utilité** :
- Préparation mentale de l'étudiant
- Clarification des règles
- Évite les surprises

---

### passage.html.twig
**Rôle** : Interface de passage du quiz (questions et réponses)

**Contenu** :
- En-tête :
  * Titre du quiz
  * Barre de progression (Question X/Y)
  * Timer si durée limitée
- Zone de question :
  * Texte de la question
  * Image/audio/vidéo si présent
  * Options de réponse (boutons radio)
- Navigation :
  * Bouton "Précédent"
  * Bouton "Suivant"
  * Bouton "Soumettre" (dernière question)
- Bouton "Demander de l'aide" (tuteur IA)
- Sauvegarde automatique des réponses

**Utilité** :
- Interface claire et intuitive
- Navigation fluide
- Feedback visuel
- Aide disponible

---

### result.html.twig
**Rôle** : Affichage des résultats après soumission

**Contenu** :
- Score principal :
  * Cercle de progression animé
  * Pourcentage
  * Points obtenus / Total
  * Statut : Réussi ✅ ou Échoué ❌
- Détails par question :
  * Texte de la question
  * Votre réponse (verte si correcte, rouge si incorrecte)
  * Bonne réponse (si vous avez eu faux)
  * Explication pédagogique (générée par IA)
- Statistiques :
  * Temps passé
  * Nombre de bonnes/mauvaises réponses
  * Votre historique sur ce quiz
  * Comparaison avec la moyenne
- Actions :
  * Bouton "Refaire le quiz" (si tentatives restantes)
  * Bouton "Autres quiz"
  * Bouton "Retour au chapitre"

**Utilité** :
- Feedback immédiat et complet
- Apprentissage par les erreurs
- Motivation pour s'améliorer
- Suivi de la progression

---

## 📂 8. CONFIGURATION

### config/services.yaml
**Rôle** : Configuration des services Symfony

**Contenu pour les quiz** :
- Configuration de GrokQuizGeneratorService avec la clé API
- Configuration de QuizCorrectorAIService avec la clé API
- Configuration de QuizTutorAIService avec la clé API
- Configuration de QuizManagementService (autowiring)

**Utilité** :
- Injection de dépendances
- Configuration centralisée
- Sécurité (clés API en variables d'environnement)

---

### config/packages/framework.yaml
**Rôle** : Configuration du framework Symfony

**Contenu pour les quiz** :
- Configuration du HTTP Client :
  * Timeout : 60 secondes
  * Retry automatique : 2 tentatives
  * Délai entre tentatives : 1 seconde

**Utilité** :
- Améliore la fiabilité des appels API
- Gère les erreurs réseau
- Optimise les performances

---

### .env.local
**Rôle** : Variables d'environnement locales (non versionnées)

**Contenu pour les quiz** :
- GROQ_API_KEY : Clé API pour l'IA Groq

**Utilité** :
- Sécurité (clé non dans le code)
- Configuration par environnement
- Facilite le déploiement

---

## 🎯 RÉSUMÉ POUR LA SOUTENANCE

### Fichiers Essentiels à Connaître

**Entités** (3 fichiers) :
- Quiz, Question, Option → Structure de données

**Contrôleurs** (3 fichiers) :
- QuizController (admin) → CRUD et génération IA
- QuizController (front) → Affichage
- QuizPassageController → Passage et correction

**Services** (4 fichiers) :
- GrokQuizGeneratorService → ⭐ Innovation IA
- QuizManagementService → Logique métier
- QuizCorrectorAIService → Correction intelligente
- QuizTutorAIService → Aide contextuelle

**Templates** (8 fichiers principaux) :
- Backoffice : index, new, edit, generate
- Frontoffice : list, passage, result

---

## 💡 POINTS CLÉS À RETENIR

1. **Séparation des responsabilités** : Chaque fichier a un rôle précis
2. **Réutilisabilité** : Services utilisables partout
3. **Sécurité** : Validation à tous les niveaux
4. **Innovation** : Génération IA = gain de temps énorme
5. **UX** : Interfaces claires pour admin et étudiants

---

**Vous êtes maintenant prêt à expliquer chaque fichier lors de votre soutenance ! 🎓**
