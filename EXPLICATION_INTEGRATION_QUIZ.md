# Intégration Quiz dans Chapitre - Documentation

## Vue d'ensemble
Cette intégration permet de gérer les quiz directement depuis les chapitres dans le backoffice, suivant le même pattern que l'intégration Chapitre-in-Cours.

## Workflow Administrateur

1. **Accéder aux cours** : Aller dans la section "Cours" du backoffice
2. **Voir les chapitres** : Cliquer sur "Voir chapitres" pour un cours
3. **Gérer les quiz** : Cliquer sur le bouton "Voir Quiz" (icône violette) à côté de chaque chapitre
4. **CRUD complet** : Créer, lire, modifier et supprimer des quiz pour ce chapitre

## Modifications apportées

### 1. Entités (src/Entity/)

#### Chapitre.php
- Ajout de la collection `quizzes` (OneToMany vers Quiz)
- Ajout du constructeur pour initialiser la collection
- Ajout des méthodes `getQuizzes()`, `addQuiz()`, `removeQuiz()`

#### Quiz.php
- Ajout de la propriété `chapitre` (ManyToOne vers Chapitre)
- Ajout des méthodes `getChapitre()` et `setChapitre()`
- La relation est nullable pour maintenir la compatibilité avec les quiz existants

### 2. Contrôleur (src/Controller/CoursController.php)

Ajout de 5 nouvelles routes pour la gestion des quiz :

```php
// Liste des quiz d'un chapitre
#[Route('/{coursId}/chapitres/{chapitreId}/quizzes', name: 'app_cours_chapitre_quizzes')]

// Créer un nouveau quiz
#[Route('/{coursId}/chapitres/{chapitreId}/quizzes/new', name: 'app_cours_chapitre_quiz_new')]

// Voir un quiz
#[Route('/{coursId}/chapitres/{chapitreId}/quizzes/{id}', name: 'app_cours_chapitre_quiz_show')]

// Modifier un quiz
#[Route('/{coursId}/chapitres/{chapitreId}/quizzes/{id}/edit', name: 'app_cours_chapitre_quiz_edit')]

// Supprimer un quiz
#[Route('/{coursId}/chapitres/{chapitreId}/quizzes/{id}/delete', name: 'app_cours_chapitre_quiz_delete')]
```

Chaque méthode vérifie que :
- Le chapitre appartient bien au cours
- Le quiz appartient bien au chapitre (pour show, edit, delete)

### 3. Templates (templates/backoffice/cours/)

#### quizzes.html.twig
- Liste tous les quiz d'un chapitre
- Affiche : titre, description, état (avec badges colorés)
- Boutons d'action : Voir, Modifier, Supprimer
- État vide avec lien pour créer le premier quiz

#### quiz_new.html.twig
- Formulaire de création d'un nouveau quiz
- Champs : titre, description, état
- Le quiz est automatiquement associé au chapitre

#### quiz_show.html.twig
- Affiche les détails d'un quiz
- Montre le chapitre et le cours parent
- Boutons : Retour, Modifier

#### quiz_edit.html.twig
- Formulaire de modification d'un quiz
- Zone de danger avec bouton de suppression

#### chapitres.html.twig (modifié)
- Ajout du bouton "Voir Quiz" (icône violette) dans la colonne Actions
- Positionné entre "Voir" et "Modifier"

### 4. Base de données

#### Migration Version20260211194834.php
```sql
ALTER TABLE quiz ADD chapitre_id INT DEFAULT NULL;
ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B 
    FOREIGN KEY (chapitre_id) REFERENCES chapitre (id);
CREATE INDEX IDX_A412FA921FBEEF7B ON quiz (chapitre_id);
```

## Structure des routes

```
/cours/{id}/chapitres                           → Liste des chapitres
/cours/{id}/chapitres/{chapitreId}/quizzes      → Liste des quiz du chapitre
/cours/{id}/chapitres/{chapitreId}/quizzes/new  → Créer un quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/{id} → Voir un quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/{id}/edit   → Modifier un quiz
/cours/{id}/chapitres/{chapitreId}/quizzes/{id}/delete → Supprimer un quiz
```

## Sécurité

- Validation CSRF pour toutes les suppressions
- Vérification de l'appartenance chapitre → cours
- Vérification de l'appartenance quiz → chapitre
- Retour 404 si les relations ne correspondent pas

## États des quiz

Les quiz peuvent avoir 4 états différents :
- **Actif** (vert) : Quiz disponible
- **Inactif** (rouge) : Quiz désactivé
- **Brouillon** (jaune) : Quiz en cours de création
- **Archivé** (gris) : Quiz archivé

## Commandes exécutées

```bash
# Génération de la migration
php bin/console make:migration

# Application des changements de schéma
php bin/console doctrine:schema:update --force

# Nettoyage du cache
php bin/console cache:clear
```

## Compatibilité

- Les quiz existants sans chapitre continuent de fonctionner (relation nullable)
- Aucune modification des fonctionnalités existantes de Quiz
- La gestion des questions dans les quiz reste inchangée

## Pattern de développement

Cette intégration suit exactement le même pattern que l'intégration Chapitre-in-Cours :
1. Relation OneToMany/ManyToOne entre les entités
2. Routes imbriquées dans le contrôleur parent
3. Templates dans le même dossier que l'entité parente
4. Vérifications de sécurité à chaque niveau
5. Interface utilisateur cohérente avec le reste du backoffice

## Prochaines étapes possibles

- Ajouter un compteur de quiz dans la liste des chapitres
- Permettre de réorganiser l'ordre des quiz
- Ajouter des statistiques sur les quiz (nombre de questions, etc.)
- Dupliquer un quiz vers un autre chapitre
