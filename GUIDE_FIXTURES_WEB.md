# 🌐 Guide - Fixtures Web Development (HTML/CSS/JavaScript)

## 🎯 Objectif

Charger automatiquement le cours "Web Development - HTML CSS JavaScript" avec 10 chapitres dans la base de données.

---

## ✅ Ce qui a été créé

### Fichiers
- `src/DataFixtures/WebDevelopmentFixtures.php` - Fixture principale
- `src/DataFixtures/WebDevelopmentContent.php` - Contenu des chapitres (trait)

### Contenu
- **1 cours** : "Web Development - HTML CSS JavaScript"
- **10 chapitres** avec contenu HTML formaté

---

## 📚 Chapitres du Cours

### HTML (Chapitres 1-2)
1. **Introduction to HTML**
   - Structure HTML de base
   - Tags essentiels
   - Liens et images

2. **HTML Structure and Semantics**
   - Éléments sémantiques
   - Listes et tableaux
   - Formulaires

### CSS (Chapitres 3-5)
3. **Introduction to CSS**
   - Syntaxe CSS
   - Sélecteurs
   - Box model

4. **CSS Layout and Positioning**
   - Display et Position
   - Flexbox
   - Grid

5. **Responsive Web Design**
   - Media queries
   - Images responsives
   - Mobile-first

### JavaScript (Chapitres 6-9)
6. **Introduction to JavaScript**
   - Variables et types
   - Fonctions
   - Boucles et conditions

7. **JavaScript DOM Manipulation**
   - Sélection d'éléments
   - Modification du contenu
   - Création d'éléments

8. **JavaScript Events and Interactivity**
   - Event listeners
   - Gestion des événements
   - Event delegation

9. **JavaScript Async and Fetch API**
   - Promises
   - Async/Await
   - Fetch API

### Projet (Chapitre 10)
10. **Building a Complete Web Project**
    - Structure de projet
    - Best practices
    - Déploiement

---

## 🚀 Charger les Fixtures

### Option 1 : Charger Tous les Cours (Python + Java + Web)
```bash
php bin/console doctrine:fixtures:load --append
```

### Option 2 : Charger Uniquement Web Development
Si tu veux charger seulement le cours Web sans les autres, modifie la fixture pour utiliser des groupes.

---

## 📊 Vérifier le Chargement

### Via phpMyAdmin
```sql
-- Vérifier le cours
SELECT id, titre, matiere, niveau, duree 
FROM cours 
WHERE titre LIKE '%Web%';

-- Vérifier les chapitres
SELECT c.id, c.titre, c.ordre, co.titre as cours
FROM chapitre c
JOIN cours co ON c.cours_id = co.id
WHERE co.titre LIKE '%Web%'
ORDER BY c.ordre;
```

### Via l'Interface Web
1. Aller sur : `http://localhost:8000/`
2. Vérifier que le cours "Web Development" apparaît
3. Cliquer sur "Voir le cours"
4. Vérifier les 10 chapitres

---

## 🎨 Exemples de Contenu

### Chapitre 1 : Introduction to HTML
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My First Web Page</title>
</head>
<body>
    <h1>Hello, World!</h1>
    <p>Welcome to web development!</p>
</body>
</html>
```

### Chapitre 3 : Introduction to CSS
```css
.box {
    width: 300px;
    padding: 20px;
    border: 2px solid black;
    margin: 10px;
}
```

### Chapitre 6 : Introduction to JavaScript
```javascript
let name = "Alice";
const age = 25;

function greet(name) {
    return "Hello, " + name;
}

console.log(greet("Alice"));
```

---

## 🔧 Personnalisation

### Ajouter un Chapitre
Éditer `src/DataFixtures/WebDevelopmentFixtures.php` :

```php
private function createChapter11(Cours $cours): void
{
    $chapitre = new Chapitre();
    $chapitre->setTitre('Advanced JavaScript');
    $chapitre->setOrdre(11);
    $chapitre->setContenu($this->getChapter11Content());
    $cours->addChapitre($chapitre);
}
```

Puis ajouter la méthode dans `WebDevelopmentContent.php`.

---

## 📋 Workflow Complet

### Charger les 3 Cours (Python + Java + Web)
```bash
# 1. Charger toutes les fixtures
php bin/console doctrine:fixtures:load --append

# 2. Vérifier dans le navigateur
# http://localhost:8000/
```

### Résultat Attendu
Tu auras maintenant **3 cours** :
1. Python Programming (7 chapitres)
2. Java Programming for Beginners (8 chapitres)
3. Web Development - HTML CSS JavaScript (10 chapitres)

**Total : 25 chapitres de contenu pédagogique !**

---

## ✅ Avantages

- ✅ Contenu complet HTML/CSS/JavaScript
- ✅ Exemples de code pratiques
- ✅ Progression logique (HTML → CSS → JS)
- ✅ Projet final intégré
- ✅ Basé sur MDN Learning Area

---

## 🎯 Prochaines Étapes

1. **Charger les fixtures**
   ```bash
   php bin/console doctrine:fixtures:load --append
   ```

2. **Tester l'affichage**
   - Voir les 3 cours sur la page d'accueil
   - Naviguer dans les chapitres
   - Générer les PDF

3. **Ajouter des quiz**
   - Créer des quiz pour chaque chapitre
   - Tester la progression des étudiants

---

**Les fixtures Web sont prêtes ! Charge-les maintenant ! 🚀**
