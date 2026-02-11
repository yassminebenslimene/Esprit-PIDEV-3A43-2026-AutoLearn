# Guide d'Intégration - Quiz dans Chapitre

## 📋 Vue d'ensemble
Cette intégration permet de gérer les quiz directement depuis les chapitres dans le backoffice, suivant le même pattern que l'intégration Chapitre-in-Cours.

## 🎯 Fonctionnalités ajoutées
- ✅ Relation OneToMany entre Chapitre et Quiz
- ✅ CRUD complet des quiz depuis un chapitre
- ✅ Bouton "Voir Quiz" dans la liste des chapitres
- ✅ Validation et messages d'erreur
- ✅ Interface cohérente avec le reste du backoffice

## 📁 Fichiers modifiés

### Entités
- `src/Entity/Chapitre.php` - Ajout collection quizzes
- `src/Entity/Quiz.php` - Ajout relation chapitre

### Contrôleurs
- `src/Controller/CoursController.php` - 5 nouvelles routes pour quiz

### Formulaires
- `src/Form/QuizType.php` - Correction du champ état

### Templates
- `templates/backoffice/cours/chapitres.html.twig` - Bouton "Voir Quiz"
- `templates/backoffice/chapitre/index.html.twig` - Bouton "Voir Quiz"
- `templates/backoffice/cours/quizzes.html.twig` - Liste des quiz (NOUVEAU)
- `templates/backoffice/cours/quiz_new.html.twig` - Créer quiz (NOUVEAU)
- `templates/backoffice/cours/quiz_show.html.twig` - Voir quiz (NOUVEAU)
- `templates/backoffice/cours/quiz_edit.html.twig` - Modifier quiz (NOUVEAU)

### Base de données
- `migrations/Version20260211194834.php` - Ajout chapitre_id dans quiz

### Documentation
- `.kiro/specs/quiz-in-chapitre/requirements.md` - Spécifications
- `EXPLICATION_INTEGRATION_QUIZ.md` - Documentation technique

## 🚀 Étapes pour intégrer après un git pull

### 1. Récupérer les modifications
```bash
git pull origin web
```

### 2. Installer les dépendances (si nécessaire)
```bash
composer install
```

### 3. Mettre à jour la base de données

**Option A - Avec les migrations (recommandé pour production):**
```bash
php bin/console doctrine:migrations:migrate
```

**Option B - Mise à jour directe du schéma (développement):**
```bash
php bin/console doctrine:schema:update --force
```

### 4. Vider le cache
```bash
php bin/console cache:clear
```

### 5. Vérifier que tout fonctionne
```bash
# Démarrer le serveur Symfony
symfony serve

# Ou avec PHP
php -S localhost:8000 -t public
```

## 🔍 Comment tester l'intégration

### Test 1: Accès via Cours
1. Aller dans **Backoffice → Cours**
2. Cliquer sur **"Voir chapitres"** pour un cours
3. Cliquer sur le bouton **violet "Voir Quiz"** à côté d'un chapitre
4. Vous devriez voir la liste des quiz du chapitre

### Test 2: Accès via Chapitres standalone
1. Aller dans **Backoffice → Chapitres** (si disponible dans le menu)
2. Cliquer sur le bouton **violet "Voir Quiz"** à côté d'un chapitre
3. Vous devriez voir la liste des quiz du chapitre

### Test 3: Créer un quiz
1. Dans la liste des quiz, cliquer sur **"Nouveau Quiz"**
2. Remplir le formulaire:
   - **Titre**: minimum 3 caractères
   - **Description**: minimum 10 caractères
   - **État**: choisir parmi Actif, Inactif, Brouillon, Archivé
3. Cliquer sur **"Créer"**
4. Vous devriez voir un message de succès vert
5. Le quiz apparaît dans la liste

### Test 4: Modifier un quiz
1. Cliquer sur l'icône **"Modifier"** (crayon) d'un quiz
2. Modifier les champs
3. Cliquer sur **"Enregistrer"**
4. Vérifier que les modifications sont sauvegardées

### Test 5: Supprimer un quiz
1. Cliquer sur l'icône **"Supprimer"** (poubelle rouge) d'un quiz
2. Confirmer la suppression
3. Le quiz disparaît de la liste

## 🗺️ Structure des routes

```
/cours/{id}/chapitres                                    → Liste des chapitres
/cours/{id}/chapitres/{chapitreId}/quizzes               → Liste des quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/new           → Créer un quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/{id}          → Voir un quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/{id}/edit     → Modifier un quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/{id}/delete   → Supprimer un quiz
```

## 🔧 Résolution des problèmes courants

### Problème 1: Erreur "Cannot autowire argument $cours"
**Solution**: Vider le cache
```bash
php bin/console cache:clear
```

### Problème 2: Table 'quiz' doesn't have column 'chapitre_id'
**Solution**: Exécuter la migration
```bash
php bin/console doctrine:schema:update --force
```

### Problème 3: Le formulaire ne s'enregistre pas
**Causes possibles**:
- Champ "État" non sélectionné → Vérifier que tous les champs requis sont remplis
- Erreur de validation → Regarder les messages d'erreur en rouge sous les champs
- Cache non vidé → Exécuter `php bin/console cache:clear`

### Problème 4: Bouton "Voir Quiz" n'apparaît pas
**Vérifications**:
- Le chapitre doit être associé à un cours
- Vider le cache du navigateur (Ctrl+F5)
- Vérifier que le template a été mis à jour

## 📊 Structure de la base de données

### Table: quiz
```sql
ALTER TABLE quiz ADD chapitre_id INT DEFAULT NULL;
ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B 
    FOREIGN KEY (chapitre_id) REFERENCES chapitre (id);
CREATE INDEX IDX_A412FA921FBEEF7B ON quiz (chapitre_id);
```

### Relation
- **Chapitre** (1) ←→ (N) **Quiz**
- Un chapitre peut avoir plusieurs quiz
- Un quiz appartient à un seul chapitre (nullable pour compatibilité)

## 🎨 Interface utilisateur

### Bouton "Voir Quiz"
- **Couleur**: Violet (#8b5cf6)
- **Icône**: Checkmark dans un carré
- **Position**: Entre "Voir" et "Modifier" dans la liste des chapitres

### États des quiz (badges colorés)
- **Actif**: Vert (#22c55e)
- **Inactif**: Rouge (#ef4444)
- **Brouillon**: Jaune (#fbbf24)
- **Archivé**: Gris (#9ca3af)

## 📝 Validation des champs

### Titre
- Minimum: 3 caractères
- Maximum: 255 caractères
- Caractères autorisés: lettres, chiffres, espaces, tirets, apostrophes, ponctuation

### Description
- Minimum: 10 caractères
- Maximum: 2000 caractères

### État
- Valeurs autorisées: actif, inactif, brouillon, archive
- Champ obligatoire

## 🔐 Sécurité

- ✅ Vérification que le chapitre appartient au cours
- ✅ Vérification que le quiz appartient au chapitre
- ✅ Protection CSRF sur les suppressions
- ✅ Validation des données côté serveur
- ✅ Retour 404 si les relations ne correspondent pas

## 📚 Documentation supplémentaire

- **Spécifications complètes**: `.kiro/specs/quiz-in-chapitre/requirements.md`
- **Détails techniques**: `EXPLICATION_INTEGRATION_QUIZ.md`
- **Guide d'intégration général**: `INTEGRATION_GUIDE.md`

## 🤝 Support

Si vous rencontrez des problèmes:
1. Vérifier les logs Symfony: `var/log/dev.log`
2. Activer le mode debug dans `.env`: `APP_ENV=dev`
3. Consulter la documentation Symfony: https://symfony.com/doc

## ✅ Checklist d'intégration

Après avoir fait `git pull`, vérifiez:

- [ ] `composer install` exécuté
- [ ] Base de données mise à jour (`doctrine:schema:update --force`)
- [ ] Cache vidé (`cache:clear`)
- [ ] Serveur Symfony démarré
- [ ] Test de création d'un quiz réussi
- [ ] Bouton "Voir Quiz" visible dans la liste des chapitres
- [ ] Navigation entre cours → chapitres → quiz fonctionne
- [ ] Messages de succès/erreur s'affichent correctement

## 🎉 Résultat attendu

Après l'intégration, vous devriez pouvoir:
1. Naviguer: **Cours → Chapitres → Quiz**
2. Voir la liste des quiz d'un chapitre
3. Créer, modifier, voir et supprimer des quiz
4. Voir les badges colorés selon l'état du quiz
5. Recevoir des messages de confirmation/erreur

---

**Date de création**: 11 février 2026  
**Version**: 1.0  
**Auteur**: Intégration Quiz-Chapitre
