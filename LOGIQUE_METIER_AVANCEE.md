# 🎯 LOGIQUE MÉTIER AVANCÉE - SYSTÈME DE QUIZ

## 📚 Table des Matières

1. [Règles Métier Complexes](#règles-métier-complexes)
2. [Algorithmes et Calculs](#algorithmes-et-calculs)
3. [Validation Métier](#validation-métier)
4. [Gestion des États](#gestion-des-états)
5. [Logique de Correction](#logique-de-correction)
6. [Statistiques et Analytics](#statistiques-et-analytics)
7. [Gestion des Tentatives](#gestion-des-tentatives)
8. [Système de Notation](#système-de-notation)

---

## 🔐 RÈGLES MÉTIER COMPLEXES

### 1. Règles de Création de Quiz

#### Règle 1.1 : Validation de la Structure
**Énoncé** : Un quiz doit respecter une structure minimale pour être valide

**Contraintes** :
- Au moins 1 question
- Chaque question doit avoir minimum 2 options
- Chaque question doit avoir exactement 1 bonne réponse
- Le total des points doit être > 0

**Implémentation** : `QuizManagementService::validateQuizBusinessRules()`

**Cas d'erreur** :
- Quiz sans question → "Le quiz doit contenir au moins une question"
- Question avec 1 seule option → "La question doit avoir au moins 2 options"
- Question sans bonne réponse → "Aucune réponse correcte définie"
- Question avec 2 bonnes réponses → "Une seule réponse correcte autorisée"

**Justification métier** :
- Garantit la qualité pédagogique
- Évite les quiz impossibles à réussir
- Assure une expérience utilisateur cohérente

---

#### Règle 1.2 : Cohérence des Paramètres
**Énoncé** : Les paramètres du quiz doivent être cohérents entre eux

**Contraintes** :
- Seuil de réussite : 0% ≤ seuil ≤ 100%
- Durée maximale : > 0 minutes (si définie)
- Nombre de tentatives : > 0 (si défini)
- État : parmi {actif, inactif, brouillon, archive}

**Cas particuliers** :
- Seuil à 0% : Quiz toujours réussi (utilisé pour les quiz d'entraînement)
- Seuil à 100% : Quiz très difficile (toutes les réponses doivent être correctes)
- Durée illimitée : NULL (pas de contrainte de temps)
- Tentatives illimitées : NULL (l'étudiant peut repasser autant de fois qu'il veut)

**Justification métier** :
- Flexibilité pédagogique
- Adaptation aux différents types d'évaluation
- Respect des contraintes institutionnelles

---

### 2. Règles de Passage de Quiz

#### Règle 2.1 : Vérification des Prérequis
**Énoncé** : Un étudiant ne peut passer un quiz que si toutes les conditions sont remplies

**Conditions** :
1. L'étudiant doit être authentifié
2. Le quiz doit être à l'état "actif"
3. Le nombre de tentatives max ne doit pas être atteint
4. L'étudiant ne doit pas avoir une tentative en cours

**Algorithme de vérification** :
```
FONCTION peutPasserQuiz(etudiant, quiz):
    SI quiz.etat != "actif" ALORS
        RETOURNER FAUX, "Quiz non disponible"
    FIN SI
    
    SI quiz.maxTentatives != NULL ALORS
        tentativesEffectuees = compterTentatives(etudiant, quiz)
        SI tentativesEffectuees >= quiz.maxTentatives ALORS
            RETOURNER FAUX, "Nombre maximum de tentatives atteint"
        FIN SI
    FIN SI
    
    tentativeEnCours = trouverTentativeEnCours(etudiant, quiz)
    SI tentativeEnCours != NULL ALORS
        RETOURNER FAUX, "Vous avez déjà une tentative en cours"
    FIN SI
    
    RETOURNER VRAI, "Vous pouvez passer le quiz"
FIN FONCTION
```

**Implémentation** : `QuizManagementService::verifierTentativesRestantes()`

---

#### Règle 2.2 : Gestion du Timer
**Énoncé** : Si le quiz a une durée limitée, le temps doit être strictement respecté

**Comportement** :
- Timer démarre au clic sur "Commencer"
- Timer affiché en temps réel (compte à rebours)
- Alerte à 5 minutes restantes
- Alerte à 1 minute restante
- Soumission automatique à 0 seconde

**Calcul du temps restant** :
```
FONCTION calculerTempsRestant(participation, quiz):
    tempsEcoule = maintenant() - participation.dateDebut
    tempsRestant = quiz.dureeMaxMinutes * 60 - tempsEcoule
    
    SI tempsRestant <= 0 ALORS
        soumettreAutomatiquement(participation)
        RETOURNER 0
    FIN SI
    
    RETOURNER tempsRestant
FIN FONCTION
```

**Cas particuliers** :
- Durée NULL : Pas de timer, temps illimité
- Déconnexion pendant le quiz : Le timer continue de tourner
- Rafraîchissement de page : Le timer reprend là où il était

**Justification métier** :
- Équité entre les étudiants
- Simulation de conditions d'examen réelles
- Évite la triche (recherche de réponses)

---

### 3. Règles de Correction

#### Règle 3.1 : Calcul du Score
**Énoncé** : Le score est calculé en fonction des points attribués à chaque question

**Algorithme de correction** :
```
FONCTION corrigerQuiz(participation, reponses):
    scoreTotal = 0
    pointsObtenus = 0
    detailsReponses = []
    
    POUR CHAQUE question DANS quiz.questions FAIRE
        scoreTotal = scoreTotal + question.point
        
        reponseEtudiant = reponses[question.id]
        
        SI reponseEtudiant != NULL ALORS
            option = trouverOption(reponseEtudiant)
            
            SI option.estCorrecte ALORS
                pointsObtenus = pointsObtenus + question.point
                detailsReponses.ajouter({
                    question: question,
                    correct: VRAI,
                    points: question.point
                })
            SINON
                detailsReponses.ajouter({
                    question: question,
                    correct: FAUX,
                    points: 0
                })
            FIN SI
        SINON
            // Question non répondue
            detailsReponses.ajouter({
                question: question,
                correct: FAUX,
                points: 0,
                nonRepondue: VRAI
            })
        FIN SI
    FIN POUR
    
    pourcentage = (pointsObtenus / scoreTotal) * 100
    reussi = pourcentage >= quiz.seuilReussite
    
    RETOURNER {
        scoreTotal: scoreTotal,
        pointsObtenus: pointsObtenus,
        pourcentage: pourcentage,
        reussi: reussi,
        details: detailsReponses
    }
FIN FONCTION
```

**Implémentation** : `QuizPassageController::submit()`

---

#### Règle 3.2 : Gestion des Questions Non Répondues
**Énoncé** : Une question non répondue compte comme une erreur

**Comportement** :
- 0 point attribué
- Marquée comme "non répondue" dans les résultats
- Compte dans le calcul du pourcentage
- Affichée différemment dans les résultats (icône spécifique)

**Justification métier** :
- Encourage à répondre à toutes les questions
- Pénalise l'abandon
- Reflète la réalité d'un examen

---

#### Règle 3.3 : Pondération des Questions
**Énoncé** : Les questions peuvent avoir des poids différents selon leur importance

**Exemples** :
- Question facile : 5 points
- Question moyenne : 10 points
- Question difficile : 20 points

**Impact sur le score** :
```
Quiz avec 3 questions :
- Q1 (facile) : 5 points → Réponse correcte
- Q2 (moyenne) : 10 points → Réponse incorrecte
- Q3 (difficile) : 20 points → Réponse correcte

Score = 5 + 0 + 20 = 25 points sur 35
Pourcentage = 25/35 * 100 = 71.4%
```

**Justification métier** :
- Valorise les questions importantes
- Permet une évaluation plus fine
- Reflète la complexité réelle

---

## 📊 ALGORITHMES ET CALCULS

### 1. Algorithme de Génération de Quiz par IA

**Processus complet** :

```
FONCTION genererQuizIA(chapitre, nombreQuestions, difficulte):
    // 1. Extraction du contenu
    contenu = extraireContenu(chapitre)
    contenu = nettoyerHTML(contenu)
    contenu = limiter(contenu, 4000) // Limite API
    
    // 2. Construction du prompt
    prompt = construirePrompt(contenu, nombreQuestions, difficulte)
    
    // 3. Appel API avec retry
    tentatives = 0
    TANT QUE tentatives < 3 FAIRE
        ESSAYER
            reponse = appellerAPIGroq(prompt)
            SI reponse.valide ALORS
                SORTIR DE LA BOUCLE
            FIN SI
        CAPTURER erreur
            tentatives = tentatives + 1
            attendre(1000 * tentatives) // Backoff exponentiel
        FIN ESSAYER
    FIN TANT QUE
    
    SI tentatives >= 3 ALORS
        LANCER ERREUR "Impossible de générer le quiz après 3 tentatives"
    FIN SI
    
    // 4. Validation de la réponse
    questionsData = parseJSON(reponse.contenu)
    valider(questionsData)
    
    // 5. Création des entités
    quiz = creerQuiz()
    POUR CHAQUE questionData DANS questionsData FAIRE
        question = creerQuestion(questionData)
        POUR CHAQUE optionData DANS questionData.options FAIRE
            option = creerOption(optionData)
            question.ajouterOption(option)
        FIN POUR
        quiz.ajouterQuestion(question)
    FIN POUR
    
    // 6. Sauvegarde
    sauvegarder(quiz)
    
    RETOURNER quiz
FIN FONCTION
```

**Implémentation** : `GrokQuizGeneratorService::genererQuizPourChapitre()`

---

### 2. Algorithme de Calcul de Statistiques

**Statistiques par Quiz** :

```
FONCTION calculerStatistiquesQuiz(quiz):
    participations = trouverParticipations(quiz)
    
    // Statistiques de base
    nombreParticipations = compter(participations)
    
    SI nombreParticipations == 0 ALORS
        RETOURNER statistiquesVides()
    FIN SI
    
    // Calcul des réussites
    nombreReussites = 0
    scores = []
    tempsPassages = []
    
    POUR CHAQUE participation DANS participations FAIRE
        SI participation.reussi ALORS
            nombreReussites = nombreReussites + 1
        FIN SI
        
        scores.ajouter(participation.pourcentage)
        
        SI participation.dateFin != NULL ALORS
            temps = participation.dateFin - participation.dateDebut
            tempsPassages.ajouter(temps)
        FIN SI
    FIN POUR
    
    // Calculs statistiques
    tauxReussite = (nombreReussites / nombreParticipations) * 100
    scoreMoyen = moyenne(scores)
    scoreMedian = mediane(scores)
    scoreMin = minimum(scores)
    scoreMax = maximum(scores)
    ecartType = calculerEcartType(scores)
    tempsMoyen = moyenne(tempsPassages)
    
    // Questions difficiles
    questionsDifficiles = analyserQuestionsDifficiles(quiz)
    
    RETOURNER {
        nombreParticipations: nombreParticipations,
        nombreReussites: nombreReussites,
        tauxReussite: tauxReussite,
        scoreMoyen: scoreMoyen,
        scoreMedian: scoreMedian,
        scoreMin: scoreMin,
        scoreMax: scoreMax,
        ecartType: ecartType,
        tempsMoyen: tempsMoyen,
        questionsDifficiles: questionsDifficiles
    }
FIN FONCTION
```

**Implémentation** : `QuizManagementService::calculerStatistiques()`

---

### 3. Algorithme d'Analyse des Questions Difficiles

```
FONCTION analyserQuestionsDifficiles(quiz):
    questionsDifficiles = []
    
    POUR CHAQUE question DANS quiz.questions FAIRE
        reponses = trouverReponses(question)
        
        SI compter(reponses) < 10 ALORS
            // Pas assez de données
            CONTINUER
        FIN SI
        
        nombreErreurs = 0
        POUR CHAQUE reponse DANS reponses FAIRE
            SI NON reponse.correcte ALORS
                nombreErreurs = nombreErreurs + 1
            FIN SI
        FIN POUR
        
        tauxErreur = (nombreErreurs / compter(reponses)) * 100
        
        SI tauxErreur > 60 ALORS
            questionsDifficiles.ajouter({
                question: question,
                tauxErreur: tauxErreur,
                nombreReponses: compter(reponses)
            })
        FIN SI
    FIN POUR
    
    // Trier par taux d'erreur décroissant
    trier(questionsDifficiles, PAR tauxErreur, DECROISSANT)
    
    RETOURNER questionsDifficiles
FIN FONCTION
```

---

## ✅ VALIDATION MÉTIER

### 1. Validation de Quiz

**Règles de validation** :

```
FONCTION validerQuiz(quiz):
    erreurs = []
    
    // Validation des champs obligatoires
    SI quiz.titre == NULL OU vide(quiz.titre) ALORS
        erreurs.ajouter("Le titre est obligatoire")
    FIN SI
    
    SI longueur(quiz.titre) < 3 ALORS
        erreurs.ajouter("Le titre doit contenir au moins 3 caractères")
    FIN SI
    
    SI quiz.description == NULL OU vide(quiz.description) ALORS
        erreurs.ajouter("La description est obligatoire")
    FIN SI
    
    SI quiz.chapitre == NULL ALORS
        erreurs.ajouter("Le quiz doit être lié à un chapitre")
    FIN SI
    
    // Validation des paramètres
    SI quiz.seuilReussite < 0 OU quiz.seuilReussite > 100 ALORS
        erreurs.ajouter("Le seuil de réussite doit être entre 0 et 100%")
    FIN SI
    
    SI quiz.dureeMaxMinutes != NULL ET quiz.dureeMaxMinutes <= 0 ALORS
        erreurs.ajouter("La durée doit être positive")
    FIN SI
    
    SI quiz.maxTentatives != NULL ET quiz.maxTentatives <= 0 ALORS
        erreurs.ajouter("Le nombre de tentatives doit être positif")
    FIN SI
    
    // Validation de la structure
    SI compter(quiz.questions) == 0 ALORS
        erreurs.ajouter("Le quiz doit contenir au moins une question")
    FIN SI
    
    // Validation des questions
    POUR CHAQUE question DANS quiz.questions FAIRE
        erreursQuestion = validerQuestion(question)
        erreurs.ajouter(erreursQuestion)
    FIN POUR
    
    RETOURNER {
        valide: compter(erreurs) == 0,
        erreurs: erreurs
    }
FIN FONCTION
```

---

### 2. Validation de Question

```
FONCTION validerQuestion(question):
    erreurs = []
    
    // Validation du texte
    SI question.texte == NULL OU vide(question.texte) ALORS
        erreurs.ajouter("Le texte de la question est obligatoire")
    FIN SI
    
    SI longueur(question.texte) < 10 ALORS
        erreurs.ajouter("La question doit contenir au moins 10 caractères")
    FIN SI
    
    // Validation des points
    SI question.point <= 0 ALORS
        erreurs.ajouter("Le nombre de points doit être positif")
    FIN SI
    
    SI question.point > 100 ALORS
        erreurs.ajouter("Le nombre de points ne peut pas dépasser 100")
    FIN SI
    
    // Validation des options
    SI compter(question.options) < 2 ALORS
        erreurs.ajouter("La question doit avoir au moins 2 options")
    FIN SI
    
    // Vérification de la bonne réponse
    nombreBonnesReponses = 0
    POUR CHAQUE option DANS question.options FAIRE
        SI option.estCorrecte ALORS
            nombreBonnesReponses = nombreBonnesReponses + 1
        FIN SI
    FIN POUR
    
    SI nombreBonnesReponses == 0 ALORS
        erreurs.ajouter("La question doit avoir au moins une bonne réponse")
    FIN SI
    
    SI nombreBonnesReponses > 1 ALORS
        erreurs.ajouter("La question ne peut avoir qu'une seule bonne réponse")
    FIN SI
    
    RETOURNER erreurs
FIN FONCTION
```

---

## 🔄 GESTION DES ÉTATS

### Machine à États d'un Quiz

**États possibles** :
1. **brouillon** : Quiz en cours de création
2. **actif** : Quiz disponible pour les étudiants
3. **inactif** : Quiz temporairement désactivé
4. **archive** : Quiz archivé (historique)

**Transitions autorisées** :

```
brouillon → actif : Publication du quiz
brouillon → archive : Suppression logique

actif → inactif : Désactivation temporaire
actif → archive : Archivage

inactif → actif : Réactivation
inactif → archive : Archivage

archive → (aucune) : État final
```

**Règles de transition** :

```
FONCTION changerEtatQuiz(quiz, nouvelEtat):
    etatActuel = quiz.etat
    
    // Vérifier si la transition est autorisée
    SI NON transitionAutorisee(etatActuel, nouvelEtat) ALORS
        LANCER ERREUR "Transition non autorisée"
    FIN SI
    
    // Vérifications spécifiques
    SI nouvelEtat == "actif" ALORS
        validation = validerQuiz(quiz)
        SI NON validation.valide ALORS
            LANCER ERREUR "Le quiz n'est pas valide pour être activé"
        FIN SI
    FIN SI
    
    // Effectuer la transition
    quiz.etat = nouvelEtat
    quiz.dateModification = maintenant()
    
    // Actions post-transition
    SI nouvelEtat == "actif" ALORS
        notifierEtudiants(quiz)
    FIN SI
    
    SI nouvelEtat == "archive" ALORS
        archiverParticipations(quiz)
    FIN SI
    
    sauvegarder(quiz)
FIN FONCTION
```

---

### Machine à États d'une Participation

**États possibles** :
1. **en_cours** : Quiz en cours de passage
2. **termine** : Quiz terminé (soumis)
3. **reussi** : Quiz réussi (score ≥ seuil)
4. **echoue** : Quiz échoué (score < seuil)
5. **abandonne** : Quiz abandonné (timeout ou abandon volontaire)

**Transitions** :

```
en_cours → termine : Soumission manuelle
en_cours → abandonne : Timeout ou abandon
termine → reussi : Score ≥ seuil
termine → echoue : Score < seuil
```

---

## 🎓 SYSTÈME DE NOTATION

### 1. Calcul du Pourcentage

```
FONCTION calculerPourcentage(pointsObtenus, scoreTotal):
    SI scoreTotal == 0 ALORS
        RETOURNER 0
    FIN SI
    
    pourcentage = (pointsObtenus / scoreTotal) * 100
    pourcentage = arrondir(pourcentage, 2) // 2 décimales
    
    RETOURNER pourcentage
FIN FONCTION
```

---

### 2. Détermination du Statut

```
FONCTION determinerStatut(pourcentage, seuilReussite):
    SI pourcentage >= seuilReussite ALORS
        RETOURNER "reussi"
    SINON
        RETOURNER "echoue"
    FIN SI
FIN FONCTION
```

---

### 3. Attribution de Mention (Optionnel)

```
FONCTION attribuerMention(pourcentage):
    SI pourcentage >= 90 ALORS
        RETOURNER "Excellent"
    SINON SI pourcentage >= 80 ALORS
        RETOURNER "Très bien"
    SINON SI pourcentage >= 70 ALORS
        RETOURNER "Bien"
    SINON SI pourcentage >= 60 ALORS
        RETOURNER "Assez bien"
    SINON SI pourcentage >= 50 ALORS
        RETOURNER "Passable"
    SINON
        RETOURNER "Insuffisant"
    FIN SI
FIN FONCTION
```

---

## 📈 STATISTIQUES ET ANALYTICS

### 1. Progression de l'Étudiant

```
FONCTION calculerProgression(etudiant, chapitre):
    quizzes = trouverQuizzes(chapitre)
    quizzesReussis = 0
    
    POUR CHAQUE quiz DANS quizzes FAIRE
        meilleurScore = obtenirMeilleurScore(etudiant, quiz)
        SI meilleurScore >= quiz.seuilReussite ALORS
            quizzesReussis = quizzesReussis + 1
        FIN SI
    FIN POUR
    
    progression = (quizzesReussis / compter(quizzes)) * 100
    
    RETOURNER {
        quizzesTotal: compter(quizzes),
        quizzesReussis: quizzesReussis,
        progression: progression
    }
FIN FONCTION
```

---

### 2. Analyse des Difficultés

```
FONCTION analyserDifficultesEtudiant(etudiant):
    participations = trouverParticipations(etudiant)
    themesErreurs = {}
    
    POUR CHAQUE participation DANS participations FAIRE
        POUR CHAQUE reponse DANS participation.reponses FAIRE
            SI NON reponse.correcte ALORS
                theme = reponse.question.theme
                SI theme DANS themesErreurs ALORS
                    themesErreurs[theme] = themesErreurs[theme] + 1
                SINON
                    themesErreurs[theme] = 1
                FIN SI
            FIN SI
        FIN POUR
    FIN POUR
    
    // Trier par nombre d'erreurs
    themesDifficiles = trier(themesErreurs, DECROISSANT)
    
    RETOURNER themesDifficiles
FIN FONCTION
```

---

## 🔁 GESTION DES TENTATIVES

### 1. Comptage des Tentatives

```
FONCTION compterTentatives(etudiant, quiz):
    participations = trouverParticipations(etudiant, quiz)
    
    // Compter uniquement les tentatives terminées
    tentatives = 0
    POUR CHAQUE participation DANS participations FAIRE
        SI participation.statut DANS ["termine", "reussi", "echoue"] ALORS
            tentatives = tentatives + 1
        FIN SI
    FIN POUR
    
    RETOURNER tentatives
FIN FONCTION
```

---

### 2. Vérification des Tentatives Restantes

```
FONCTION tentativesRestantes(etudiant, quiz):
    SI quiz.maxTentatives == NULL ALORS
        RETOURNER {
            illimite: VRAI,
            restantes: NULL
        }
    FIN SI
    
    tentativesEffectuees = compterTentatives(etudiant, quiz)
    restantes = quiz.maxTentatives - tentativesEffectuees
    
    RETOURNER {
        illimite: FAUX,
        effectuees: tentativesEffectuees,
        maximum: quiz.maxTentatives,
        restantes: restantes,
        peutPasser: restantes > 0
    }
FIN FONCTION
```

---

### 3. Meilleur Score

```
FONCTION obtenirMeilleurScore(etudiant, quiz):
    participations = trouverParticipations(etudiant, quiz)
    
    SI compter(participations) == 0 ALORS
        RETOURNER NULL
    FIN SI
    
    meilleurScore = 0
    POUR CHAQUE participation DANS participations FAIRE
        SI participation.pourcentage > meilleurScore ALORS
            meilleurScore = participation.pourcentage
        FIN SI
    FIN POUR
    
    RETOURNER meilleurScore
FIN FONCTION
```

---

## 🎯 RÉSUMÉ POUR LA SOUTENANCE

### Règles Métier Clés

1. **Validation stricte** : Quiz doit avoir structure valide
2. **Gestion des tentatives** : Limite configurable
3. **Correction automatique** : Calcul précis du score
4. **Pondération** : Questions avec poids différents
5. **États** : Machine à états pour quiz et participations

### Algorithmes Importants

1. **Génération IA** : Extraction → Prompt → API → Validation → Création
2. **Correction** : Parcours questions → Vérification réponses → Calcul score
3. **Statistiques** : Agrégation données → Calculs → Analyse

### Points Forts Métier

✅ **Logique robuste** : Validation à tous les niveaux
✅ **Flexibilité** : Paramètres configurables
✅ **Traçabilité** : Historique complet des tentatives
✅ **Analytics** : Statistiques détaillées
✅ **Équité** : Règles identiques pour tous

---

**Vous maîtrisez maintenant toute la logique métier avancée de votre système ! 🎓**
LOGIQUE_METIER_AVANCEE.md qui contient toute la logique métier avancée :

✅ Ce que contient le fichier :
🔐 Règles Métier Complexes
Règles de création de quiz

Validation de structure
Cohérence des paramètres
Règles de passage de quiz

Vérification des prérequis
Gestion du timer
Règles de correction

Calcul du score
Gestion des questions non répondues
Pondération des questions
📊 Algorithmes et Calculs
Génération de quiz par IA (algorithme complet)
Calcul de statistiques (moyenne, médiane, écart-type)
Analyse des questions difficiles
✅ Validation Métier
Validation de quiz (tous les contrôles)
Validation de question (structure, options, réponses)
🔄 Gestion des États
Machine à états d'un quiz (brouillon → actif → inactif → archive)
Machine à états d'une participation (en_cours → terminé → réussi/échoué)
🎓 Système de Notation
Calcul du pourcentage
Détermination du statut (réussi/échoué)
Attribution de mention (Excellent, Très bien, Bien, etc.)
📈 Statistiques et Analytics
Progression de l'étudiant
Analyse des difficultés
🔁 Gestion des Tentatives
Comptage des tentatives
Vérification des tentatives restantes
Meilleur score
📚 Vous avez maintenant 8 documents complets :
PARTIE 1 - Architecture
PARTIE 2 - Fonctionnalités
PARTIE 3 - Services et IA
PARTIE 4 - BDD et Déploiement
PARTIE 5 - Guide de Soutenance ⭐
GUIDE_DETAILLE_FICHIERS_QUIZ - Explications fichiers
CONCEPTS_AVANCES_BUNDLES_API - Technologies
LOGIQUE_METIER_AVANCEE - Règles métier et algorithmes (NOUVEAU)