# 🚀 Guide d'Installation - Pour les Collègues

## 🎯 Après avoir fait `git pull`

Tu as récupéré le code, mais ta base de données est vide. Voici comment charger les cours.

---

## ⚡ Installation Rapide (3 Commandes)

### 1. Installer les dépendances
```bash
composer install
```

### 2. Créer la base de données (si pas déjà fait)
```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

### 3. Charger les cours (Python, Java, Web)
```bash
php bin/console doctrine:fixtures:load --append
```

**C'est tout ! Les 3 cours sont maintenant dans ta base. 🎉**

---

## 🧪 Vérifier que ça fonctionne

### 1. Démarrer le serveur
```bash
symfony server:start
```

### 2. Ouvrir dans le navigateur
```
http://localhost:8000/
```

### 3. Vérifier
Tu devrais voir **3 cours** :
- Python Programming (7 chapitres)
- Java Programming for Beginners (8 chapitres)
- Web Development - HTML CSS JavaScript (10 chapitres)

---

## 📊 Que contient la commande fixtures ?

La commande `php bin/console doctrine:fixtures:load --append` charge automatiquement :

### Cours 1 : Python Programming
- 7 chapitres
- 40 heures de contenu
- Variables, boucles, fonctions, POO

### Cours 2 : Java Programming
- 8 chapitres
- 50 heures de contenu
- Variables, boucles, méthodes, POO, collections

### Cours 3 : Web Development
- 10 chapitres
- 60 heures de contenu
- HTML, CSS, JavaScript, DOM, Fetch API

**Total : 25 chapitres de contenu pédagogique !**

---

## 🔄 Si tu as déjà des données

### Option 1 : Ajouter les cours (garde tes données)
```bash
php bin/console doctrine:fixtures:load --append
```

### Option 2 : Remplacer tout (supprime tes données)
```bash
php bin/console doctrine:fixtures:load
```

**Recommandé : Utilise `--append` !**

---

## 🐛 Problèmes Courants

### Erreur : "Database does not exist"
```bash
php bin/console doctrine:database:create
```

### Erreur : "Table not found"
```bash
php bin/console doctrine:schema:update --force
```

### Erreur : "Composer dependencies"
```bash
composer install
```

### Vérifier que MySQL tourne
- Ouvre XAMPP Control Panel
- Démarre MySQL

---

## 📁 Fichiers Importants

Les fixtures sont dans :
```
src/DataFixtures/
├── JavaCourseFixtures.php          (Cours Java)
├── WebDevelopmentFixtures.php      (Cours Web)
└── WebDevelopmentContent.php       (Contenu Web)
```

**Note :** Le cours Python est dans `insert_python_course.sql` (ancien système).

---

## ✅ Checklist Complète

- [ ] `git pull` effectué
- [ ] `composer install` exécuté
- [ ] Base de données créée
- [ ] Tables créées (`doctrine:schema:update`)
- [ ] Fixtures chargées (`doctrine:fixtures:load --append`)
- [ ] Serveur démarré
- [ ] Page d'accueil testée
- [ ] 3 cours visibles

---

## 🎯 Commandes Résumées

```bash
# 1. Récupérer le code
git pull

# 2. Installer les dépendances
composer install

# 3. Créer la base (si nécessaire)
php bin/console doctrine:database:create

# 4. Créer les tables
php bin/console doctrine:schema:update --force

# 5. Charger les cours
php bin/console doctrine:fixtures:load --append

# 6. Démarrer le serveur
symfony server:start

# 7. Ouvrir le navigateur
# http://localhost:8000/
```

---

## 💡 Pourquoi les Fixtures ?

### Avantages
- ✅ **Reproductible** : Même données pour toute l'équipe
- ✅ **Rapide** : Une seule commande
- ✅ **Versionné** : Les fixtures sont dans Git
- ✅ **Facile** : Pas besoin de SQL manuel

### Avant (Sans Fixtures)
1. Récupérer un dump SQL
2. Importer dans phpMyAdmin
3. Espérer que ça marche
4. Problèmes de compatibilité

### Maintenant (Avec Fixtures)
1. `git pull`
2. `php bin/console doctrine:fixtures:load --append`
3. ✅ Ça marche !

---

## 🎉 Résultat

Après avoir suivi ce guide, tu auras :

✅ **3 cours complets** dans ta base  
✅ **25 chapitres** de contenu  
✅ **Génération PDF** fonctionnelle  
✅ **Traduction multilingue** active  
✅ **Interface moderne** prête  

---

**Bienvenue dans l'équipe ! 🚀**
