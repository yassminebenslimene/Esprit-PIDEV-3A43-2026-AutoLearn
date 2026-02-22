# 📚 Guide - Fixtures Doctrine pour le Cours Java

## 🎯 Objectif

Charger automatiquement le cours "Java Programming for Beginners" avec 8 chapitres dans la base de données via Doctrine Fixtures.

---

## ✅ Ce qui a été fait

### 1. Installation du Bundle Fixtures
```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

### 2. Création de la Fixture
**Fichier :** `src/DataFixtures/JavaCourseFixtures.php`

**Contenu :**
- 1 cours : "Java Programming for Beginners"
- 8 chapitres avec contenu HTML formaté :
  1. Introduction to Java
  2. Variables and Data Types
  3. Operators and Expressions
  4. Control Flow Statements
  5. Loops and Iterations
  6. Methods and Functions
  7. Object-Oriented Programming
  8. Arrays and Collections

---

## 🚀 Comment Charger les Fixtures

### Commande Simple
```bash
php bin/console doctrine:fixtures:load
```

**Attention :** Cette commande **supprime toutes les données existantes** avant de charger les fixtures !

### Confirmation
Quand tu exécutes la commande, tu verras :
```
Careful, database "autolearn_db" will be purged. Do you want to continue? (yes/no) [no]:
```

Tape **`yes`** pour confirmer.

---

## 🔄 Charger Sans Supprimer les Données Existantes

Si tu veux **ajouter** le cours Java sans supprimer les cours existants (comme Python) :

### Option 1 : Append Mode
```bash
php bin/console doctrine:fixtures:load --append
```

Cette commande ajoute les fixtures sans purger la base.

### Option 2 : Groupes de Fixtures (Recommandé)

Modifier la fixture pour utiliser des groupes :

```php
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class JavaCourseFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['java'];
    }
    
    // ... reste du code
}
```

Puis charger uniquement le groupe Java :
```bash
php bin/console doctrine:fixtures:load --group=java --append
```

---

## 📊 Vérifier le Chargement

### Via phpMyAdmin

1. Ouvrir http://localhost/phpmyadmin
2. Sélectionner la base `autolearn_db`
3. Onglet "SQL"
4. Exécuter :

```sql
-- Vérifier le cours
SELECT id, titre, matiere, niveau, duree 
FROM cours 
WHERE titre LIKE '%Java%';

-- Vérifier les chapitres
SELECT c.id, c.titre, c.ordre, co.titre as cours
FROM chapitre c
JOIN cours co ON c.cours_id = co.id
WHERE co.titre LIKE '%Java%'
ORDER BY c.ordre;
```

### Via l'Interface Web

1. Aller sur : `http://localhost:8000/`
2. Vérifier que le cours "Java Programming for Beginners" apparaît
3. Cliquer sur "Voir le cours"
4. Vérifier que les 8 chapitres s'affichent

---

## 🎨 Contenu des Chapitres

Chaque chapitre contient :
- ✅ Titre descriptif
- ✅ Contenu HTML formaté
- ✅ Exemples de code Java
- ✅ Explications détaillées
- ✅ Bonnes pratiques

### Exemple de Contenu

**Chapitre 1 : Introduction to Java**
- Présentation de Java
- Pourquoi apprendre Java
- Applications Java
- Premier programme "Hello World"
- Structure d'un programme Java

**Chapitre 2 : Variables and Data Types**
- Types primitifs (int, double, boolean, char)
- Types référence (String)
- Déclaration et initialisation
- Type casting
- Constantes

**Chapitre 3 : Operators and Expressions**
- Opérateurs arithmétiques
- Opérateurs de comparaison
- Opérateurs logiques
- Opérateurs d'affectation
- Opérateur ternaire

**Chapitre 4 : Control Flow Statements**
- if, else, else-if
- switch statement
- Nested conditions

**Chapitre 5 : Loops and Iterations**
- for loop
- while loop
- do-while loop
- Enhanced for loop (for-each)
- break et continue

**Chapitre 6 : Methods and Functions**
- Déclaration de méthodes
- Paramètres et valeurs de retour
- Method overloading
- Varargs
- Récursion

**Chapitre 7 : Object-Oriented Programming**
- Classes et objets
- Encapsulation
- Héritage
- Polymorphisme
- Classes abstraites

**Chapitre 8 : Arrays and Collections**
- Arrays
- ArrayList
- HashMap
- HashSet
- Collections utilities

---

## 🔧 Personnalisation

### Modifier le Contenu

Éditer le fichier : `src/DataFixtures/JavaCourseFixtures.php`

**Exemple : Ajouter un chapitre**

```php
// Chapitre 9: Exception Handling
$chapitre9 = new Chapitre();
$chapitre9->setTitre('Exception Handling');
$chapitre9->setOrdre(9);
$chapitre9->setContenu($this->getChapter9Content());
$cours->addChapitre($chapitre9);
```

Puis créer la méthode `getChapter9Content()`.

### Modifier les Métadonnées du Cours

```php
$cours->setTitre('Advanced Java Programming');
$cours->setNiveau('Intermediaire');
$cours->setDuree(80);
```

---

## 🐛 Dépannage

### Erreur : "Table not found"

**Cause :** Les tables n'existent pas.

**Solution :**
```bash
php bin/console doctrine:schema:update --force
```

### Erreur : "Integrity constraint violation"

**Cause :** Données existantes en conflit.

**Solution :** Purger la base avant de charger :
```bash
php bin/console doctrine:fixtures:load
```

### Erreur : "Class not found"

**Cause :** Autoload pas à jour.

**Solution :**
```bash
composer dump-autoload
```

---

## 📋 Workflow Complet

### Première Installation

```bash
# 1. Créer les tables
php bin/console doctrine:schema:update --force

# 2. Charger les fixtures
php bin/console doctrine:fixtures:load

# 3. Vérifier dans le navigateur
# http://localhost:8000/
```

### Ajouter Java Sans Supprimer Python

```bash
# Charger en mode append
php bin/console doctrine:fixtures:load --append
```

### Recharger Toutes les Données

```bash
# Purger et recharger
php bin/console doctrine:fixtures:load
```

---

## ✅ Avantages des Fixtures

### 1. Reproductibilité
- Même données sur tous les environnements
- Facile à partager avec l'équipe

### 2. Développement Rapide
- Pas besoin d'insérer manuellement les données
- Une seule commande pour tout charger

### 3. Tests
- Données de test cohérentes
- Facile à réinitialiser

### 4. Versioning
- Les fixtures sont dans Git
- Historique des changements

### 5. Maintenance
- Modifier le contenu dans le code
- Pas de scripts SQL à maintenir

---

## 🎯 Prochaines Étapes

### 1. Charger les Fixtures
```bash
php bin/console doctrine:fixtures:load --append
```

### 2. Vérifier l'Affichage
- Aller sur http://localhost:8000/
- Voir le cours Java
- Tester les chapitres

### 3. Générer les PDF
- Cliquer sur un chapitre Java
- Tester "Prévisualiser PDF"
- Vérifier le contenu

### 4. Ajouter d'Autres Cours
- Créer de nouvelles fixtures
- Charger avec `--append`

---

## 📚 Ressources

### Documentation Doctrine Fixtures
https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html

### Source du Contenu
Basé sur : https://github.com/in28minutes/java-a-course-for-beginners

**Note :** Le contenu a été reformulé et adapté pour la plateforme Autolearn.

---

**Les fixtures sont prêtes ! Charge-les maintenant ! 🚀**
