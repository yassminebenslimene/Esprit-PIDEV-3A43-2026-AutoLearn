# ⚡ Charger le Cours Java - Guide Rapide

## 🎯 En 2 Commandes

### 1. Charger les fixtures (avec le cours Python existant)
```bash
php bin/console doctrine:fixtures:load --append
```

### 2. Vérifier dans le navigateur
```
http://localhost:8000/
```

Le cours "Java Programming for Beginners" apparaît avec 8 chapitres ! ✅

---

## 📊 Ce qui sera chargé

**1 Cours :**
- Titre : Java Programming for Beginners
- Matière : Informatique
- Niveau : Débutant
- Durée : 50 heures

**8 Chapitres :**
1. Introduction to Java
2. Variables and Data Types
3. Operators and Expressions
4. Control Flow Statements
5. Loops and Iterations
6. Methods and Functions
7. Object-Oriented Programming
8. Arrays and Collections

---

## ⚠️ Important

### Option 1 : Ajouter Java (Garder Python)
```bash
php bin/console doctrine:fixtures:load --append
```
✅ Garde le cours Python existant  
✅ Ajoute le cours Java

### Option 2 : Remplacer Tout
```bash
php bin/console doctrine:fixtures:load
```
❌ Supprime TOUS les cours (Python inclus)  
✅ Charge uniquement Java

**Recommandé : Utilise `--append` !**

---

## 🧪 Tester

1. **Voir les cours**
   ```
   http://localhost:8000/
   ```

2. **Voir les chapitres Java**
   ```
   http://localhost:8000/chapitre/cours/[ID_COURS_JAVA]
   ```

3. **Lire un chapitre**
   - Cliquer sur "Lire le chapitre"
   - Voir le contenu Java formaté

4. **Générer le PDF**
   - Cliquer sur "Prévisualiser PDF"
   - Voir le PDF avec le contenu Java

---

**C'est tout ! Charge maintenant ! 🚀**
