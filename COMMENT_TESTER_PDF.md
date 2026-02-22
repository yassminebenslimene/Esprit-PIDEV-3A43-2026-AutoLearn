# 🚀 Comment Tester le PDF - Guide Ultra-Rapide

## ⚡ En 3 Étapes

### 1️⃣ Démarre le serveur (si pas déjà fait)
```bash
symfony server:start
```

### 2️⃣ Va sur un chapitre
```
http://localhost:8000/chapitre/front/1
```
(Remplace `1` par l'ID d'un chapitre existant)

### 3️⃣ Clique sur "Prévisualiser PDF"
Le PDF s'ouvre dans un nouvel onglet !

---

## 🎯 Résultat Attendu

Tu devrais voir un PDF avec :
- ✅ Header "🎓 AUTOLEARN"
- ✅ Titre du chapitre
- ✅ Métadonnées (ordre, cours, matière, niveau)
- ✅ Contenu formaté
- ✅ Code Python
- ✅ Footer avec pagination

---

## 🐛 Si ça ne marche pas

### Erreur 404
```bash
php bin/console cache:clear
```

### Chapitre introuvable
Vérifie que le cours Python est inséré :
```sql
SELECT id, titre FROM chapitre LIMIT 5;
```

### Autre erreur
Consulte : `README_PDF_FINAL.md`

---

**C'est tout ! Teste maintenant ! 🎉**
