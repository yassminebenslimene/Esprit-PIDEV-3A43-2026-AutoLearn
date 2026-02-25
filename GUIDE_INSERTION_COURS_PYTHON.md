# 📚 Guide d'Insertion du Cours Python Programming

## 🎯 Objectif
Insérer un cours Python complet avec 7 chapitres de contenu pédagogique dans la base de données.

---

## ✅ Prérequis

1. MySQL/MariaDB démarré (XAMPP avec bouton MySQL vert)
2. Base de données `autolearn_db` existante
3. Tables `cours` et `chapitre` créées

---

## 📋 Étapes d'Insertion

### Méthode 1 : Via phpMyAdmin (RECOMMANDÉ)

#### 1. Ouvrir phpMyAdmin
```
http://localhost/phpmyadmin
```

#### 2. Sélectionner la base de données
- Cliquer sur `autolearn_db` dans la colonne de gauche

#### 3. Aller dans l'onglet SQL
- Cliquer sur l'onglet "SQL" en haut

#### 4. Copier-coller le script
- Ouvrir le fichier `insert_python_course.sql`
- Copier TOUT le contenu
- Coller dans la zone de texte SQL

#### 5. Exécuter
- Cliquer sur le bouton "Exécuter" en bas à droite
- Attendre quelques secondes

#### 6. Vérifier le résultat
Vous devriez voir :
```
✅ Cours Python Programming créé avec succès !
✅ ID du cours: [un nombre]
✅ Nombre de chapitres créés: 7
```

---

### Méthode 2 : Via ligne de commande

```bash
mysql -u root -p autolearn_db < insert_python_course.sql
```

---

## 🔍 Vérification

### 1. Vérifier le cours
```sql
SELECT * FROM cours WHERE titre = 'Python Programming';
```

**Résultat attendu :**
- 1 ligne avec le cours Python

### 2. Vérifier les chapitres
```sql
SELECT id, titre, ordre 
FROM chapitre 
WHERE cours_id = (SELECT id FROM cours WHERE titre = 'Python Programming')
ORDER BY ordre;
```

**Résultat attendu :**
```
1. Introduction à Python
2. Variables et Types de Données
3. Structures Conditionnelles
4. Boucles et Itérations
5. Fonctions
6. Listes et Structures de Données
7. Programmation Orientée Objet
```

### 3. Vérifier le contenu d'un chapitre
```sql
SELECT titre, LEFT(contenu, 100) as apercu_contenu
FROM chapitre 
WHERE titre = 'Introduction à Python';
```

---

## 🎨 Tester l'Affichage

### 1. Accéder au backoffice
```
http://localhost:8000/cours
```

### 2. Voir le cours Python
- Vous devriez voir "Python Programming" dans la liste
- Cliquer dessus pour voir les détails

### 3. Voir les chapitres
```
http://localhost:8000/cours/[ID_COURS]/chapitres
```

### 4. Voir un chapitre spécifique
- Cliquer sur un chapitre dans la liste
- Le contenu HTML devrait s'afficher correctement

### 5. Frontoffice (si disponible)
```
http://localhost:8000/chapitre/front
```

---

## 📊 Contenu du Cours

### Cours : Python Programming
- **Matière** : Informatique
- **Niveau** : Débutant
- **Durée** : 40 heures
- **Chapitres** : 7

### Chapitres inclus :

1. **Introduction à Python** (ordre 1)
   - Présentation du langage
   - Installation
   - Premier programme
   - L'interpréteur Python

2. **Variables et Types de Données** (ordre 2)
   - Créer des variables
   - Types principaux (int, float, str, bool, list, dict)
   - Opérations sur les variables
   - Conversion de types

3. **Structures Conditionnelles** (ordre 3)
   - if, elif, else
   - Opérateurs de comparaison
   - Opérateurs logiques (and, or, not)
   - Conditions imbriquées

4. **Boucles et Itérations** (ordre 4)
   - Boucle for
   - Boucle while
   - break, continue
   - Boucles imbriquées

5. **Fonctions** (ordre 5)
   - Définir une fonction
   - Paramètres et arguments
   - Valeurs de retour
   - Paramètres par défaut
   - *args et **kwargs
   - Portée des variables

6. **Listes et Structures de Données** (ordre 6)
   - Listes (création, modification, opérations)
   - Slicing
   - Tuples
   - Dictionnaires
   - Sets

7. **Programmation Orientée Objet** (ordre 7)
   - Classes et objets
   - Attributs et méthodes
   - Encapsulation
   - Héritage
   - Méthodes spéciales
   - Bonnes pratiques

---

## 🎯 Caractéristiques du Contenu

### Format HTML
- Le contenu est formaté en HTML
- Utilise des balises sémantiques (h2, h3, p, ul, li, pre, code)
- Prêt pour l'affichage dans les vues Twig

### Exemples de Code
- Chaque chapitre contient des exemples pratiques
- Code formaté avec `<pre><code>`
- Commentaires explicatifs

### Structure Pédagogique
- Progression logique du simple au complexe
- Explications claires
- Exemples concrets
- Bonnes pratiques

---

## 🆘 Problèmes Courants

### Erreur : "Table 'cours' doesn't exist"
**Solution :** Vérifier que les tables sont créées
```sql
SHOW TABLES;
```

### Erreur : "Duplicate entry"
**Solution :** Le cours existe déjà, supprimer d'abord
```sql
DELETE FROM chapitre WHERE cours_id IN (SELECT id FROM cours WHERE titre = 'Python Programming');
DELETE FROM cours WHERE titre = 'Python Programming';
```

### Erreur : "Column 'created_at' doesn't exist"
**Solution :** Vérifier la structure de la table cours
```sql
DESCRIBE cours;
```

### Le contenu ne s'affiche pas correctement
**Solution :** Vérifier que le contenu est bien en HTML
```sql
SELECT contenu FROM chapitre WHERE id = 1;
```

---

## 📝 Modifier le Contenu

Si vous voulez modifier un chapitre :

```sql
UPDATE chapitre 
SET contenu = 'Nouveau contenu HTML ici'
WHERE titre = 'Introduction à Python';
```

---

## 🎉 Prochaines Étapes

Une fois le cours inséré :

1. ✅ Vérifier l'affichage dans le backoffice
2. ✅ Tester la navigation entre chapitres
3. ✅ Vérifier le frontoffice
4. ✅ Ajouter des quiz (optionnel)
5. ✅ Ajouter des ressources (optionnel)

---

## 📚 Fichiers Créés

- `insert_python_course.sql` - Script d'insertion complet
- `GUIDE_INSERTION_COURS_PYTHON.md` - Ce guide

---

## ✅ Checklist

- [ ] MySQL démarré
- [ ] phpMyAdmin accessible
- [ ] Base autolearn_db sélectionnée
- [ ] Script SQL copié
- [ ] Script exécuté
- [ ] Cours vérifié en base
- [ ] Chapitres vérifiés en base
- [ ] Affichage testé dans le backoffice
- [ ] Navigation testée

---

**Le cours Python Programming est prêt à être utilisé ! 🚀**
