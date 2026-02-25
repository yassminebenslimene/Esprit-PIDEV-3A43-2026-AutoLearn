# 🎓 Charger Tous les Cours - Guide Complet

## 🎯 Vue d'Ensemble

Tu as maintenant **3 cours complets** prêts à être chargés :

1. **Python Programming** (7 chapitres)
2. **Java Programming for Beginners** (8 chapitres)
3. **Web Development - HTML CSS JavaScript** (10 chapitres)

**Total : 3 cours, 25 chapitres !**

---

## ⚡ Commande Unique

Pour charger les 3 cours en une seule commande :

```bash
php bin/console doctrine:fixtures:load --append
```

**Note :** `--append` garde les données existantes et ajoute les nouveaux cours.

---

## 📊 Ce qui sera chargé

### Cours 1 : Python Programming
- **Matière** : Informatique
- **Niveau** : Débutant
- **Durée** : 40 heures
- **Chapitres** : 7
  1. Introduction à Python
  2. Variables et Types de Données
  3. Structures Conditionnelles
  4. Boucles et Itérations
  5. Fonctions
  6. Listes et Structures de Données
  7. Programmation Orientée Objet

### Cours 2 : Java Programming for Beginners
- **Matière** : Informatique
- **Niveau** : Débutant
- **Durée** : 50 heures
- **Chapitres** : 8
  1. Introduction to Java
  2. Variables and Data Types
  3. Operators and Expressions
  4. Control Flow Statements
  5. Loops and Iterations
  6. Methods and Functions
  7. Object-Oriented Programming
  8. Arrays and Collections

### Cours 3 : Web Development - HTML CSS JavaScript
- **Matière** : Développement Web
- **Niveau** : Débutant
- **Durée** : 60 heures
- **Chapitres** : 10
  1. Introduction to HTML
  2. HTML Structure and Semantics
  3. Introduction to CSS
  4. CSS Layout and Positioning
  5. Responsive Web Design
  6. Introduction to JavaScript
  7. JavaScript DOM Manipulation
  8. JavaScript Events and Interactivity
  9. JavaScript Async and Fetch API
  10. Building a Complete Web Project

---

## 🧪 Vérifier le Chargement

### 1. Via phpMyAdmin

```sql
-- Compter les cours
SELECT COUNT(*) as total_cours FROM cours;

-- Lister tous les cours
SELECT id, titre, matiere, niveau, duree 
FROM cours 
ORDER BY id;

-- Compter les chapitres par cours
SELECT co.titre, COUNT(c.id) as nb_chapitres
FROM cours co
LEFT JOIN chapitre c ON c.cours_id = co.id
GROUP BY co.id, co.titre;
```

**Résultat attendu :**
```
Python Programming          | 7 chapitres
Java Programming            | 8 chapitres
Web Development             | 10 chapitres
```

### 2. Via l'Interface Web

1. **Page d'accueil**
   ```
   http://localhost:8000/
   ```
   Tu devrais voir 3 cartes de cours.

2. **Tester chaque cours**
   - Cliquer sur "Voir le cours"
   - Vérifier que tous les chapitres s'affichent
   - Cliquer sur "Lire le chapitre"
   - Vérifier le contenu formaté

3. **Tester les PDF**
   - Sur un chapitre, cliquer sur "Prévisualiser PDF"
   - Vérifier que le PDF se génère correctement

---

## 🎨 Aperçu de la Page d'Accueil

```
┌─────────────────────────────────────────────────────────────┐
│                    AUTOLEARN                                │
│                Learn Programming                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📚 Nos Cours                                               │
│                                                             │
│  ┌──────────────────┐  ┌──────────────────┐  ┌───────────┐│
│  │ Python           │  │ Java             │  │ Web Dev   ││
│  │ Programming      │  │ Programming      │  │ HTML/CSS  ││
│  │                  │  │                  │  │ JavaScript││
│  │ 7 chapitres      │  │ 8 chapitres      │  │ 10 chap.  ││
│  │ 40h              │  │ 50h              │  │ 60h       ││
│  │                  │  │                  │  │           ││
│  │ [Voir le cours]  │  │ [Voir le cours]  │  │ [Voir]    ││
│  └──────────────────┘  └──────────────────┘  └───────────┘│
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 Options de Chargement

### Option 1 : Charger Tout (Recommandé)
```bash
php bin/console doctrine:fixtures:load --append
```
✅ Garde les données existantes  
✅ Ajoute les 3 cours

### Option 2 : Remplacer Tout
```bash
php bin/console doctrine:fixtures:load
```
❌ Supprime TOUTES les données  
✅ Charge uniquement les 3 cours

### Option 3 : Charger Cours par Cours
```bash
# Charger Python
php bin/console doctrine:fixtures:load --group=python --append

# Charger Java
php bin/console doctrine:fixtures:load --group=java --append

# Charger Web
php bin/console doctrine:fixtures:load --group=web --append
```
(Nécessite de configurer les groupes dans les fixtures)

---

## 📈 Statistiques

### Contenu Total
- **3 cours**
- **25 chapitres**
- **150 heures** de contenu pédagogique
- **3 langages** : Python, Java, JavaScript
- **2 matières** : Informatique, Développement Web

### Par Langage
- **Python** : 7 chapitres, 40h
- **Java** : 8 chapitres, 50h
- **Web** : 10 chapitres, 60h

---

## 🎯 Workflow Complet

### Étape 1 : Charger les Fixtures
```bash
php bin/console doctrine:fixtures:load --append
```

### Étape 2 : Vérifier dans phpMyAdmin
```sql
SELECT titre, COUNT(*) as chapitres 
FROM cours co
LEFT JOIN chapitre c ON c.cours_id = co.id
GROUP BY co.id;
```

### Étape 3 : Tester dans le Navigateur
```
http://localhost:8000/
```

### Étape 4 : Tester les Fonctionnalités
- ✅ Affichage des cours
- ✅ Navigation dans les chapitres
- ✅ Génération PDF
- ✅ Traduction multilingue
- ✅ Recherche globale

---

## 🐛 Dépannage

### Erreur : "Table not found"
```bash
php bin/console doctrine:schema:update --force
```

### Erreur : "Duplicate entry"
Les cours existent déjà. Utilise `--append` ou purge la base :
```bash
php bin/console doctrine:fixtures:load
```

### Vérifier les Fixtures Disponibles
```bash
php bin/console doctrine:fixtures:load --help
```

---

## ✅ Checklist

- [ ] Fixtures créées (Python, Java, Web)
- [ ] Commande exécutée (`--append`)
- [ ] Vérification phpMyAdmin (3 cours, 25 chapitres)
- [ ] Test page d'accueil (3 cartes visibles)
- [ ] Test navigation chapitres
- [ ] Test génération PDF
- [ ] Test traduction
- [ ] Test recherche

---

## 🎉 Résultat Final

Une fois les fixtures chargées, tu auras une plateforme complète avec :

✅ **3 cours professionnels**  
✅ **25 chapitres de contenu**  
✅ **Exemples de code pratiques**  
✅ **Génération PDF automatique**  
✅ **Traduction multilingue**  
✅ **Interface moderne et responsive**  

---

**Charge maintenant les 3 cours avec une seule commande ! 🚀**

```bash
php bin/console doctrine:fixtures:load --append
```
