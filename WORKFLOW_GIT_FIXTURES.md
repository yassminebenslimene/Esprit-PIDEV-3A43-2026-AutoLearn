# 🔄 Workflow Git + Fixtures - Guide Complet

## 🎯 Comprendre le Système

### Ce qui est dans Git
✅ **Code** (PHP, Twig, CSS, JS)  
✅ **Fixtures** (fichiers `.php` dans `src/DataFixtures/`)  
✅ **Configuration** (`.env.example`, `composer.json`)  
✅ **Documentation** (fichiers `.md`)  

### Ce qui N'est PAS dans Git
❌ **Base de données** (fichiers MySQL)  
❌ **Données** (cours, chapitres, utilisateurs)  
❌ **Vendor** (dossier `vendor/`)  
❌ **Configuration locale** (`.env`)  

---

## 📤 Workflow : Toi (Push)

### Étape 1 : Tu crées/modifies les fixtures
```bash
# Éditer les fixtures
nano src/DataFixtures/JavaCourseFixtures.php
```

### Étape 2 : Tu testes localement
```bash
php bin/console doctrine:fixtures:load --append
```

### Étape 3 : Tu commites et push
```bash
git add src/DataFixtures/
git commit -m "Add Java and Web courses fixtures"
git push origin main
```

**Ce qui est poussé :**
- ✅ Fichiers de fixtures (code PHP)
- ✅ Documentation
- ❌ Données de la base (pas dans Git)

---

## 📥 Workflow : Tes Collègues (Pull)

### Étape 1 : Ils récupèrent le code
```bash
git pull origin main
```

**Ce qu'ils reçoivent :**
- ✅ Fichiers de fixtures
- ✅ Documentation
- ❌ Données (leur base est vide)

### Étape 2 : Ils installent les dépendances
```bash
composer install
```

### Étape 3 : Ils chargent les fixtures
```bash
php bin/console doctrine:fixtures:load --append
```

**Résultat :**
- ✅ Les 3 cours sont maintenant dans leur base
- ✅ Ils voient le même contenu que toi

---

## 🔄 Scénarios Courants

### Scénario 1 : Nouveau Collègue

**Situation :** Un nouveau développeur rejoint l'équipe.

**Actions :**
```bash
# 1. Cloner le repo
git clone https://github.com/votre-repo/autolearn.git
cd autolearn

# 2. Installer les dépendances
composer install

# 3. Configurer la base
cp .env.example .env
# Éditer .env avec les paramètres MySQL

# 4. Créer la base
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

# 5. Charger les cours
php bin/console doctrine:fixtures:load --append

# 6. Démarrer
symfony server:start
```

---

### Scénario 2 : Tu ajoutes un nouveau cours

**Situation :** Tu crées une fixture pour un cours C++.

**Tes actions :**
```bash
# 1. Créer la fixture
nano src/DataFixtures/CppCourseFixtures.php

# 2. Tester localement
php bin/console doctrine:fixtures:load --append

# 3. Vérifier que ça marche
# http://localhost:8000/

# 4. Commiter et push
git add src/DataFixtures/CppCourseFixtures.php
git commit -m "Add C++ course fixtures"
git push
```

**Actions de tes collègues :**
```bash
# 1. Récupérer le code
git pull

# 2. Charger le nouveau cours
php bin/console doctrine:fixtures:load --append

# 3. Vérifier
# http://localhost:8000/
```

---

### Scénario 3 : Tu modifies un chapitre existant

**Situation :** Tu corriges une faute dans le chapitre Java.

**Tes actions :**
```bash
# 1. Modifier la fixture
nano src/DataFixtures/JavaCourseFixtures.php

# 2. Recharger les fixtures
php bin/console doctrine:fixtures:load

# 3. Vérifier la correction
# http://localhost:8000/

# 4. Push
git add src/DataFixtures/JavaCourseFixtures.php
git commit -m "Fix typo in Java chapter 3"
git push
```

**Actions de tes collègues :**
```bash
# 1. Pull
git pull

# 2. Recharger les fixtures (écrase les anciennes)
php bin/console doctrine:fixtures:load

# 3. Vérifier la correction
```

---

## 📊 Comparaison : Avec vs Sans Fixtures

### Sans Fixtures (Ancien Système)

**Toi :**
1. Insérer les cours manuellement dans phpMyAdmin
2. Exporter la base en SQL
3. Envoyer le fichier SQL par email/Slack
4. Expliquer comment l'importer

**Tes collègues :**
1. Télécharger le fichier SQL
2. Ouvrir phpMyAdmin
3. Importer le fichier
4. Espérer que ça marche
5. Problèmes de compatibilité

### Avec Fixtures (Système Actuel)

**Toi :**
1. Créer/modifier la fixture
2. `git push`

**Tes collègues :**
1. `git pull`
2. `php bin/console doctrine:fixtures:load --append`
3. ✅ Ça marche !

---

## 🎯 Commandes Essentielles

### Pour Toi (Développement)

```bash
# Charger les fixtures (ajouter)
php bin/console doctrine:fixtures:load --append

# Recharger les fixtures (remplacer)
php bin/console doctrine:fixtures:load

# Vérifier les fixtures disponibles
ls src/DataFixtures/
```

### Pour Tes Collègues (Après Pull)

```bash
# Installation complète
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --append

# Mise à jour simple
git pull
php bin/console doctrine:fixtures:load --append
```

---

## 🔐 Bonnes Pratiques

### 1. Toujours Tester Avant de Push
```bash
# Tester les fixtures
php bin/console doctrine:fixtures:load --append

# Vérifier dans le navigateur
# http://localhost:8000/
```

### 2. Documenter les Changements
```bash
git commit -m "Add C++ course with 12 chapters"
```

### 3. Utiliser --append par Défaut
```bash
# Ajouter sans supprimer
php bin/console doctrine:fixtures:load --append
```

### 4. Communiquer avec l'Équipe
Après un push important, préviens l'équipe :
> "J'ai ajouté le cours C++. Faites `git pull` puis `php bin/console doctrine:fixtures:load --append`"

---

## 📁 Structure des Fixtures

```
src/DataFixtures/
├── JavaCourseFixtures.php          # Cours Java (8 chapitres)
├── WebDevelopmentFixtures.php      # Cours Web (10 chapitres)
└── WebDevelopmentContent.php       # Contenu Web (trait)
```

**Note :** Le cours Python est dans `insert_python_course.sql` (ancien système, à migrer).

---

## 🚀 Migration Future : Python en Fixture

Pour uniformiser, tu peux créer `PythonCourseFixtures.php` :

```php
<?php

namespace App\DataFixtures;

use App\Entity\GestionDeCours\Cours;
use App\Entity\GestionDeCours\Chapitre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PythonCourseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cours = new Cours();
        $cours->setTitre('Python Programming');
        // ... reste du code
    }
}
```

Puis supprimer `insert_python_course.sql`.

---

## ✅ Checklist pour l'Équipe

### Après Chaque Pull
- [ ] `git pull` effectué
- [ ] `composer install` (si `composer.lock` a changé)
- [ ] `php bin/console doctrine:fixtures:load --append` (si fixtures modifiées)
- [ ] Cache vidé si nécessaire (`php bin/console cache:clear`)
- [ ] Serveur redémarré si nécessaire

### Avant Chaque Push
- [ ] Fixtures testées localement
- [ ] Pas d'erreurs dans le code
- [ ] Documentation mise à jour
- [ ] Commit message clair

---

## 🎉 Résultat

Avec ce système :

✅ **Synchronisation facile** : `git pull` + une commande  
✅ **Pas de fichiers SQL** à échanger  
✅ **Reproductible** : Même données pour tous  
✅ **Versionné** : Historique dans Git  
✅ **Maintenable** : Facile à modifier  

---

**Le workflow Git + Fixtures est maintenant clair ! 🚀**
