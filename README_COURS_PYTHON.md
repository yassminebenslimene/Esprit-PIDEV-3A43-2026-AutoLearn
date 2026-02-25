# 🐍 Cours Python Programming - README

## ⚡ Insertion Rapide (2 minutes)

### 1. Ouvrir phpMyAdmin
```
http://localhost/phpmyadmin
```

### 2. Sélectionner autolearn_db
Cliquer sur `autolearn_db` dans la colonne de gauche

### 3. Exécuter le script
1. Onglet "SQL"
2. Copier-coller le contenu de `insert_python_course.sql`
3. Cliquer "Exécuter"

### 4. Vérifier
```
✅ Cours Python Programming créé avec succès !
✅ 7 chapitres créés
```

---

## 📚 Contenu du Cours

### Python Programming
- **Niveau** : Débutant
- **Durée** : 40 heures
- **Chapitres** : 7

### Les 7 Chapitres

1. **Introduction à Python**
   - Présentation, installation, premier programme

2. **Variables et Types de Données**
   - int, float, str, bool, list, dict

3. **Structures Conditionnelles**
   - if, elif, else, opérateurs logiques

4. **Boucles et Itérations**
   - for, while, break, continue

5. **Fonctions**
   - Définition, paramètres, return, *args, **kwargs

6. **Listes et Structures de Données**
   - Listes, tuples, dictionnaires, sets

7. **Programmation Orientée Objet**
   - Classes, objets, héritage, encapsulation

---

## 🎯 Tester l'Affichage

### Backoffice
```
http://localhost:8000/cours
```
→ Voir "Python Programming" dans la liste

### Voir les chapitres
```
http://localhost:8000/cours/[ID]/chapitres
```

### Voir un chapitre
Cliquer sur un chapitre dans la liste

---

## ✅ Vérification SQL

```sql
-- Voir le cours
SELECT * FROM cours WHERE titre = 'Python Programming';

-- Voir les chapitres
SELECT id, titre, ordre FROM chapitre 
WHERE cours_id = (SELECT id FROM cours WHERE titre = 'Python Programming')
ORDER BY ordre;
```

---

## 📁 Fichiers

- `insert_python_course.sql` - Script d'insertion
- `GUIDE_INSERTION_COURS_PYTHON.md` - Guide détaillé
- `README_COURS_PYTHON.md` - Ce fichier

---

## 🆘 Problème ?

### Le cours existe déjà ?
```sql
DELETE FROM chapitre WHERE cours_id IN (SELECT id FROM cours WHERE titre = 'Python Programming');
DELETE FROM cours WHERE titre = 'Python Programming';
```

Puis réexécuter le script.

---

**C'est tout ! Le cours est prêt à être utilisé. 🚀**
