# Commandes Git pour Push - Intégration Quiz-Chapitre

## 📦 Fichiers à commiter

### Fichiers modifiés
- `src/Controller/CoursController.php`
- `src/Entity/Chapitre.php`
- `src/Entity/Quiz.php`
- `src/Form/QuizType.php`
- `templates/backoffice/chapitre/index.html.twig`
- `templates/backoffice/cours/chapitres.html.twig`

### Nouveaux fichiers
- `.kiro/specs/quiz-in-chapitre/requirements.md`
- `EXPLICATION_INTEGRATION_QUIZ.md`
- `GUIDE_INTEGRATION_QUIZ_CHAPITRE.md`
- `migrations/Version20260211194834.php`
- `templates/backoffice/cours/quiz_edit.html.twig`
- `templates/backoffice/cours/quiz_new.html.twig`
- `templates/backoffice/cours/quiz_show.html.twig`
- `templates/backoffice/cours/quizzes.html.twig`

## 🚀 Commandes à exécuter

### 1. Ajouter tous les fichiers modifiés et nouveaux
```bash
git add src/Controller/CoursController.php
git add src/Entity/Chapitre.php
git add src/Entity/Quiz.php
git add src/Form/QuizType.php
git add templates/backoffice/chapitre/index.html.twig
git add templates/backoffice/cours/chapitres.html.twig
git add .kiro/specs/quiz-in-chapitre/requirements.md
git add EXPLICATION_INTEGRATION_QUIZ.md
git add GUIDE_INTEGRATION_QUIZ_CHAPITRE.md
git add COMMANDES_GIT_PUSH.md
git add migrations/Version20260211194834.php
git add templates/backoffice/cours/quiz_edit.html.twig
git add templates/backoffice/cours/quiz_new.html.twig
git add templates/backoffice/cours/quiz_show.html.twig
git add templates/backoffice/cours/quizzes.html.twig
```

### OU (plus simple) - Ajouter tous les fichiers en une fois
```bash
git add .
```

### 2. Vérifier les fichiers ajoutés
```bash
git status
```

### 3. Créer le commit avec un message descriptif
```bash
git commit -m "feat: Intégration Quiz dans Chapitre - CRUD complet

- Ajout relation OneToMany Chapitre->Quiz
- Ajout relation ManyToOne Quiz->Chapitre
- Création de 5 routes pour gestion des quiz
- Ajout bouton 'Voir Quiz' dans liste chapitres
- Création templates CRUD quiz (list, new, show, edit)
- Migration base de données (chapitre_id dans quiz)
- Correction validation formulaire QuizType
- Ajout messages flash succès/erreur
- Documentation complète (specs + guides)"
```

### 4. Pousser vers le dépôt distant
```bash
git push origin web
```

## 📋 Vérification avant push

Assurez-vous que:
- [ ] Tous les fichiers sont ajoutés (`git status` ne montre que des fichiers en vert)
- [ ] Le message de commit est descriptif
- [ ] Vous êtes sur la bonne branche (`git branch` montre `* web`)
- [ ] Les tests locaux fonctionnent (création de quiz OK)
- [ ] Le cache a été vidé (`php bin/console cache:clear`)

## 🔄 Commandes complètes (copier-coller)

```bash
# Vérifier la branche actuelle
git branch

# Ajouter tous les fichiers
git add .

# Vérifier ce qui va être commité
git status

# Créer le commit
git commit -m "feat: Intégration Quiz dans Chapitre - CRUD complet

- Ajout relation OneToMany Chapitre->Quiz
- Ajout relation ManyToOne Quiz->Chapitre
- Création de 5 routes pour gestion des quiz
- Ajout bouton 'Voir Quiz' dans liste chapitres
- Création templates CRUD quiz (list, new, show, edit)
- Migration base de données (chapitre_id dans quiz)
- Correction validation formulaire QuizType
- Ajout messages flash succès/erreur
- Documentation complète (specs + guides)"

# Pousser vers origin
git push origin web
```

## 📝 Message de commit alternatif (court)

Si vous préférez un message plus court:
```bash
git commit -m "feat: Intégration Quiz-Chapitre avec CRUD complet et documentation"
```

## 🔙 Pour annuler si nécessaire

Si vous voulez annuler avant le push:
```bash
# Annuler le dernier commit (garde les modifications)
git reset --soft HEAD~1

# Annuler le dernier commit (supprime les modifications)
git reset --hard HEAD~1

# Retirer un fichier du staging
git restore --staged <fichier>
```

## 📊 Statistiques du commit

Nombre de fichiers:
- **Modifiés**: 6 fichiers
- **Nouveaux**: 9 fichiers
- **Total**: 15 fichiers

Lignes de code (approximatif):
- **Ajoutées**: ~800 lignes
- **Modifiées**: ~150 lignes

## ✅ Après le push

Une fois le push effectué, vos collègues devront:
1. `git pull origin web`
2. `composer install`
3. `php bin/console doctrine:schema:update --force`
4. `php bin/console cache:clear`

Voir le fichier `GUIDE_INTEGRATION_QUIZ_CHAPITRE.md` pour les détails complets.

---

**Branche**: web  
**Date**: 11 février 2026  
**Feature**: Quiz-Chapitre Integration
