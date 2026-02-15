# 🚀 GUIDE GIT - ÉTAPE PAR ÉTAPE

## 📋 PLAN D'ACTION COMPLET

### Ordre logique des étapes:
1. ✅ Vérifier l'état actuel
2. ✅ Créer un commit de votre travail
3. ✅ Créer votre branche personnelle "Amira"
4. ✅ Pull les modifications de vos camarades
5. ✅ Résoudre les conflits éventuels
6. ✅ Push votre branche
7. ✅ Continuer à travailler sur votre branche

---

## 📍 ÉTAPE 1: VÉRIFIER L'ÉTAT ACTUEL

### Commande:
```bash
git status
```

### Ce que vous devriez voir:
- Fichiers modifiés (en rouge)
- Nouveaux fichiers (en rouge)
- Fichiers .env (ne devraient PAS apparaître car dans .gitignore)

### Action:
```bash
# Voir les fichiers modifiés
git status

# Voir les différences
git diff
```

---

## 📍 ÉTAPE 2: CRÉER UN COMMIT DE VOTRE TRAVAIL

### 2.1 Ajouter les fichiers du module Événement

```bash
# Ajouter tous les fichiers du module Événement
git add src/Entity/Evenement.php
git add src/Entity/Equipe.php
git add src/Entity/Participation.php
git add src/Enum/TypeEvenement.php
git add src/Enum/StatutEvenement.php
git add src/Enum/StatutParticipation.php
git add src/Controller/EvenementController.php
git add src/Controller/FrontofficeEvenementController.php
git add src/Controller/FrontofficeEquipeController.php
git add src/Controller/FrontofficeParticipationController.php
git add src/Controller/ParticipationController.php
git add src/Controller/EquipeController.php
git add src/Form/EvenementType.php
git add src/Form/EquipeFrontType.php
git add src/Form/ParticipationFrontType.php
git add src/Repository/EvenementRepository.php
git add src/Repository/EquipeRepository.php
git add src/Repository/ParticipationRepository.php

# Ajouter les templates
git add templates/backoffice/evenement/
git add templates/backoffice/equipe/
git add templates/backoffice/participation/
git add templates/frontoffice/evenement/
git add templates/frontoffice/equipe/
git add templates/frontoffice/participation/

# Ajouter les migrations
git add migrations/

# Ajouter les templates modifiés (ATTENTION: risque de conflit)
git add templates/frontoffice/base.html.twig
git add templates/frontoffice/index.html.twig
git add templates/backoffice/index.html.twig
```

### 2.2 Ajouter les fichiers de documentation (OPTIONNEL)

```bash
git add *.md
git add *.bat
```

### 2.3 Vérifier ce qui sera commité

```bash
git status
```

### 2.4 Créer le commit

```bash
git commit -m "feat: Ajout module Événement complet avec CRUD et validations

- Création entités: Evenement, Equipe, Participation
- Création enums: TypeEvenement, StatutEvenement, StatutParticipation
- CRUD backoffice pour admin (événements, équipes, participations)
- CRUD frontoffice pour étudiants
- Flux complet: liste événements -> participation -> création équipe
- Validations automatiques (capacité max, doublons étudiants, événement annulé)
- Messages d'erreur détaillés
- Nettoyage automatique des participations refusées
- Templates avec design moderne (gradient violet/bleu)
- Documentation complète du module"
```

---

## 📍 ÉTAPE 3: CRÉER VOTRE BRANCHE PERSONNELLE "Amira"

### Pourquoi créer une branche?
- ✅ Travailler sans affecter la branche commune
- ✅ Éviter les conflits avec vos camarades
- ✅ Pouvoir tester avant de merger
- ✅ Facile de revenir en arrière si problème

### Commande:
```bash
# Créer et basculer sur la branche Amira
git checkout -b Amira
```

### Vérifier:
```bash
# Vérifier que vous êtes sur la branche Amira
git branch
```

Vous devriez voir:
```
* Amira
  web
```

---

## 📍 ÉTAPE 4: PULL LES MODIFICATIONS DE VOS CAMARADES

### 4.1 Récupérer les dernières modifications de la branche web

```bash
# Récupérer les modifications sans merger
git fetch origin web
```

### 4.2 Merger les modifications dans votre branche

```bash
# Merger la branche web dans votre branche Amira
git merge origin/web
```

### Résultats possibles:

#### Cas 1: Pas de conflit ✅
```
Auto-merging ...
Merge made by the 'recursive' strategy.
```
→ Tout va bien, passez à l'étape 6

#### Cas 2: Conflits détectés ⚠️
```
Auto-merging templates/frontoffice/base.html.twig
CONFLICT (content): Merge conflict in templates/frontoffice/base.html.twig
Automatic merge failed; fix conflicts and then commit the result.
```
→ Passez à l'étape 5

---

## 📍 ÉTAPE 5: RÉSOUDRE LES CONFLITS (SI NÉCESSAIRE)

### 5.1 Voir les fichiers en conflit

```bash
git status
```

Vous verrez:
```
Unmerged paths:
  both modified:   templates/frontoffice/base.html.twig
  both modified:   templates/frontoffice/index.html.twig
```

### 5.2 Ouvrir les fichiers en conflit

Les conflits ressemblent à ça:
```html
<<<<<<< HEAD
<!-- Votre code -->
<a href="{{ path('app_events') }}">Events</a>
=======
<!-- Code de votre camarade -->
<a href="{{ path('app_challenges') }}">Challenges</a>
>>>>>>> origin/web
```

### 5.3 Résoudre manuellement

Choisissez ce que vous voulez garder:
```html
<!-- Garder les deux -->
<a href="{{ path('app_events') }}">Events</a>
<a href="{{ path('app_challenges') }}">Challenges</a>
```

### 5.4 Marquer comme résolu

```bash
# Après avoir résolu tous les conflits
git add templates/frontoffice/base.html.twig
git add templates/frontoffice/index.html.twig

# Finaliser le merge
git commit -m "merge: Résolution conflits avec branche web"
```

---

## 📍 ÉTAPE 6: PUSH VOTRE BRANCHE

### 6.1 Push votre branche Amira sur le dépôt distant

```bash
# Push la branche Amira
git push -u origin Amira
```

### 6.2 Vérifier sur GitHub/GitLab

Vous devriez voir votre branche "Amira" sur le dépôt distant.

---

## 📍 ÉTAPE 7: CONTINUER À TRAVAILLER SUR VOTRE BRANCHE

### Workflow quotidien:

#### 1. Vérifier que vous êtes sur votre branche
```bash
git branch
# Devrait afficher: * Amira
```

#### 2. Travailler normalement
- Modifier vos fichiers
- Tester votre code

#### 3. Commiter régulièrement
```bash
git add .
git commit -m "fix: Correction validation participations"
git push
```

#### 4. Récupérer les modifications de vos camarades (optionnel)
```bash
# Récupérer les dernières modifications de web
git fetch origin web

# Merger dans votre branche
git merge origin/web

# Résoudre les conflits si nécessaire
# Puis push
git push
```

---

## 📍 ÉTAPE 8: MERGER DANS LA BRANCHE WEB (QUAND TERMINÉ)

### Quand votre module est complètement terminé et testé:

#### Option A: Via GitHub/GitLab (RECOMMANDÉ)
1. Aller sur GitHub/GitLab
2. Créer une Pull Request (PR) / Merge Request (MR)
3. De: `Amira` → Vers: `web`
4. Demander à vos camarades de reviewer
5. Merger quand approuvé

#### Option B: En ligne de commande
```bash
# Basculer sur la branche web
git checkout web

# Pull les dernières modifications
git pull origin web

# Merger votre branche
git merge Amira

# Résoudre les conflits si nécessaire

# Push
git push origin web
```

---

## 🚨 COMMANDES D'URGENCE

### Annuler le dernier commit (avant push)
```bash
git reset --soft HEAD~1
```

### Annuler toutes les modifications non commitées
```bash
git reset --hard HEAD
```

### Voir l'historique des commits
```bash
git log --oneline
```

### Revenir à un commit précédent
```bash
git checkout <commit-hash>
```

### Supprimer votre branche locale
```bash
git branch -D Amira
```

### Supprimer votre branche distante
```bash
git push origin --delete Amira
```

---

## ✅ CHECKLIST FINALE

Avant de push:
- [ ] Vous êtes sur votre branche Amira
- [ ] Tous vos fichiers sont commitées
- [ ] Vous avez pull les dernières modifications de web
- [ ] Les conflits sont résolus
- [ ] Le projet fonctionne correctement
- [ ] Les tests passent (si vous en avez)

---

## 🎯 RÉSUMÉ DES COMMANDES ESSENTIELLES

```bash
# 1. Créer un commit
git add .
git commit -m "feat: Ajout module Événement"

# 2. Créer votre branche
git checkout -b Amira

# 3. Pull les modifications
git fetch origin web
git merge origin/web

# 4. Push votre branche
git push -u origin Amira

# 5. Workflow quotidien
git add .
git commit -m "fix: Correction bug"
git push
```

---

## 📞 AIDE

Si vous avez un problème:
1. `git status` - Voir l'état actuel
2. `git log` - Voir l'historique
3. `git branch` - Voir les branches
4. Demandez-moi de l'aide avec le message d'erreur exact
