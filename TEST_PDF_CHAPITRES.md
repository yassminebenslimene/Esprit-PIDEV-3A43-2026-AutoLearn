# 🧪 Test Rapide - Génération PDF des Chapitres

## ⚡ Test en 3 Minutes

### Étape 1 : Vérifier les prérequis
```bash
# MySQL démarré ?
# Cours Python inséré ?
# Serveur Symfony démarré ?
symfony server:start
```

### Étape 2 : Trouver l'ID d'un chapitre

**Option A : Via phpMyAdmin**
```sql
SELECT id, titre, ordre 
FROM chapitre 
WHERE cours_id = (SELECT id FROM cours WHERE titre = 'Python Programming')
ORDER BY ordre
LIMIT 1;
```

**Option B : Via la page d'accueil**
1. Aller sur `http://localhost:8000/`
2. Cliquer sur "Voir le cours" du cours Python
3. Noter l'ID dans l'URL : `/chapitre/cours/[ID_COURS]`
4. Cliquer sur un chapitre
5. Noter l'ID dans l'URL : `/chapitre/front/[ID_CHAPITRE]`

### Étape 3 : Tester la prévisualisation PDF

**URL à tester :**
```
http://localhost:8000/chapitre/front/[ID_CHAPITRE]/pdf
```

**Exemple si ID = 1 :**
```
http://localhost:8000/chapitre/front/1/pdf
```

**Résultat attendu :**
- ✅ PDF s'ouvre dans le navigateur
- ✅ Logo Autolearn visible en haut
- ✅ Titre du chapitre affiché
- ✅ Contenu formaté avec code Python
- ✅ Footer avec "Page 1"

### Étape 4 : Tester le téléchargement PDF

**URL à tester :**
```
http://localhost:8000/chapitre/front/[ID_CHAPITRE]/pdf/download
```

**Résultat attendu :**
- ✅ Téléchargement automatique
- ✅ Fichier nommé : `chapitre-1-introduction-a-python.pdf`
- ✅ PDF sauvegardé dans Téléchargements

### Étape 5 : Tester depuis l'interface

**Via la page du chapitre :**
1. Aller sur `http://localhost:8000/chapitre/front/[ID_CHAPITRE]`
2. Scroller jusqu'à la section violette "Télécharger ce chapitre"
3. Cliquer sur "Prévisualiser PDF" → Nouvel onglet avec PDF
4. Cliquer sur "Télécharger PDF" → Téléchargement direct

**Via la liste des chapitres :**
1. Aller sur `http://localhost:8000/chapitre/cours/[ID_COURS]`
2. Sur une carte de chapitre, cliquer sur le bouton violet "PDF"
3. Téléchargement direct

---

## ✅ Checklist Rapide

- [ ] PDF s'affiche dans le navigateur
- [ ] Logo Autolearn visible
- [ ] Titre du chapitre correct
- [ ] Métadonnées affichées (ordre, cours, matière)
- [ ] Contenu HTML formaté
- [ ] Code Python visible avec fond gris
- [ ] Footer avec pagination
- [ ] Téléchargement fonctionne
- [ ] Nom du fichier correct
- [ ] Boutons dans l'interface fonctionnent

---

## 🐛 Si ça ne marche pas

### Erreur 404
**Cause :** Route non trouvée  
**Solution :** Vider le cache
```bash
php bin/console cache:clear
```

### Logo ne s'affiche pas
**Cause :** Fichier manquant  
**Solution :** Vérifier que `public/frontoffice/images/auto.png` existe

### PDF vide ou mal formaté
**Cause :** Contenu du chapitre vide  
**Solution :** Vérifier que le cours Python est bien inséré
```sql
SELECT titre, LEFT(contenu, 100) FROM chapitre WHERE id = 1;
```

### Erreur "Class Dompdf not found"
**Cause :** Dompdf pas installé  
**Solution :**
```bash
composer require dompdf/dompdf
```

---

## 🎯 URLs de Test Complètes

Remplacer `[ID_CHAPITRE]` par l'ID réel :

```
# Page du chapitre
http://localhost:8000/chapitre/front/[ID_CHAPITRE]

# Prévisualisation PDF
http://localhost:8000/chapitre/front/[ID_CHAPITRE]/pdf

# Téléchargement PDF
http://localhost:8000/chapitre/front/[ID_CHAPITRE]/pdf/download

# Liste des chapitres du cours
http://localhost:8000/chapitre/cours/[ID_COURS]
```

---

**Test terminé ! Le système PDF fonctionne ! 🎉**
