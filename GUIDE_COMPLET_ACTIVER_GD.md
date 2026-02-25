# 🔧 Guide Complet - Activer l'Extension PHP GD

## ✅ Solution Temporaire Appliquée

Le PDF fonctionne maintenant avec un **logo texte** "🎓 AUTOLEARN".

Pour utiliser ton **logo image** `auto.png`, suis ce guide.

---

## 📋 Étapes Détaillées pour Activer GD

### Étape 1 : Localiser le fichier php.ini

**Option A : Via XAMPP Control Panel (Recommandé)**

1. Ouvre **XAMPP Control Panel**
2. À côté de **"Apache"**, clique sur le bouton **"Config"**
3. Dans le menu qui s'ouvre, clique sur **"PHP (php.ini)"**
4. Le fichier `php.ini` s'ouvre dans ton éditeur de texte

**Option B : Manuellement**

1. Va dans le dossier : `C:\xampp\php\`
2. Trouve le fichier : `php.ini`
3. Ouvre-le avec Notepad++ ou un éditeur de texte

---

### Étape 2 : Trouver la ligne extension=gd

1. Dans le fichier `php.ini` ouvert
2. Appuie sur **Ctrl+F** pour ouvrir la recherche
3. Tape : `extension=gd`
4. Clique sur "Suivant" ou "Find Next"

Tu vas trouver une ligne qui ressemble à :
```ini
;extension=gd
```

**Note :** Le point-virgule `;` au début signifie que la ligne est **commentée** (désactivée).

---

### Étape 3 : Activer l'extension

**Avant :**
```ini
;extension=gd
```

**Après :**
```ini
extension=gd
```

**Action :** Supprime simplement le `;` au début de la ligne.

---

### Étape 4 : Sauvegarder le fichier

1. Appuie sur **Ctrl+S** pour sauvegarder
2. Ou va dans **Fichier → Enregistrer**
3. Ferme l'éditeur

---

### Étape 5 : Redémarrer Apache

**Dans XAMPP Control Panel :**

1. Clique sur le bouton **"Stop"** à côté de Apache
2. Attends que Apache s'arrête (le bouton devient vert)
3. Clique sur le bouton **"Start"** à côté de Apache
4. Attends que Apache démarre (le bouton devient rouge)

**Important :** Apache DOIT être redémarré pour que les changements prennent effet !

---

### Étape 6 : Vérifier que GD est activé

**Option A : Via ligne de commande**

Ouvre un terminal (CMD ou PowerShell) et tape :
```bash
php -m | findstr gd
```

**Résultat attendu :**
```
gd
```

Si tu vois `gd`, l'extension est activée ! ✅

**Option B : Via phpinfo()**

1. Crée un fichier `info.php` dans `C:\xampp\htdocs\`
2. Contenu du fichier :
```php
<?php
phpinfo();
?>
```
3. Ouvre dans le navigateur : `http://localhost/info.php`
4. Appuie sur Ctrl+F et cherche "gd"
5. Tu devrais voir une section **"GD Support"** avec **"enabled"**

---

### Étape 7 : Modifier le template PDF

Une fois GD activé, édite le fichier : `templates/pdf/chapitre.html.twig`

**Trouver cette section :**
```twig
<!-- Header fixe -->
<div class="header">
    {# Logo texte - Fonctionne sans GD #}
    <div class="logo-text">🎓 AUTOLEARN</div>
    
    {# Logo image - Décommenter après avoir activé GD dans php.ini #}
    {# <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" alt="Autolearn Logo"> #}
    
    <div class="chapter-title">{{ chapitre.titre }}</div>
</div>
```

**Modifier pour :**
```twig
<!-- Header fixe -->
<div class="header">
    {# Logo texte - Fonctionne sans GD #}
    {# <div class="logo-text">🎓 AUTOLEARN</div> #}
    
    {# Logo image - Activé après avoir activé GD dans php.ini #}
    <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" alt="Autolearn Logo">
    
    <div class="chapter-title">{{ chapitre.titre }}</div>
</div>
```

**Changements :**
- Commenter le logo texte : `{# <div class="logo-text">...</div> #}`
- Décommenter le logo image : enlever `{#` et `#}` autour de `<img>`

---

### Étape 8 : Vider le cache Symfony

Ouvre un terminal dans le dossier du projet et tape :
```bash
php bin/console cache:clear
```

---

### Étape 9 : Tester le PDF

1. Va sur un chapitre : `http://localhost:8000/chapitre/front/1`
2. Clique sur **"Prévisualiser PDF"**
3. Le PDF s'ouvre avec ton logo `auto.png` ! 🎉

---

## 🐛 Dépannage

### Erreur persiste après activation

**Vérifier que tu as modifié le bon php.ini :**
```bash
php --ini
```

Cette commande affiche :
```
Loaded Configuration File: C:\xampp\php\php.ini
```

Assure-toi de modifier CE fichier.

---

### GD n'apparaît pas dans php -m

**Vérifier la syntaxe dans php.ini :**
- Pas d'espace avant `extension`
- Pas de `;` au début
- Ligne exacte : `extension=gd`

**Redémarrer Apache :**
- Stop puis Start dans XAMPP

---

### Logo ne s'affiche toujours pas

**Vérifier le chemin du fichier :**
```bash
dir public\frontoffice\images\auto.png
```

Le fichier doit exister.

**Vérifier les permissions :**
Le fichier doit être lisible par Apache.

---

## 📊 Résumé Visuel

```
1. XAMPP Control Panel
   ↓
2. Config → PHP (php.ini)
   ↓
3. Chercher: ;extension=gd
   ↓
4. Modifier: extension=gd
   ↓
5. Sauvegarder (Ctrl+S)
   ↓
6. Stop Apache
   ↓
7. Start Apache
   ↓
8. Vérifier: php -m | findstr gd
   ↓
9. Modifier template PDF
   ↓
10. Cache clear
   ↓
11. Tester PDF
   ↓
12. ✅ Logo s'affiche !
```

---

## ✅ Checklist Complète

- [ ] Fichier php.ini ouvert
- [ ] Ligne `;extension=gd` trouvée
- [ ] `;` supprimé → `extension=gd`
- [ ] Fichier sauvegardé
- [ ] Apache arrêté
- [ ] Apache redémarré
- [ ] GD vérifié avec `php -m | findstr gd`
- [ ] Template PDF modifié (logo image décommenté)
- [ ] Cache Symfony vidé
- [ ] PDF testé
- [ ] Logo `auto.png` s'affiche

---

## 🎉 Résultat Final

Une fois GD activé, ton PDF aura :

```
┌─────────────────────────────────────────┐
│        [TON LOGO AUTO.PNG]              │  ← Ton vrai logo
│    Structures Conditionnelles           │
├─────────────────────────────────────────┤
│  Chapitre: 3                            │
│  Cours: Python Programming              │
│  Matière: Informatique                  │
│  Niveau: Débutant                       │
│  Date: 20/02/2026                       │
├─────────────────────────────────────────┤
│                                         │
│  Prendre des Décisions avec if...      │
│                                         │
│  [... contenu ...]                      │
│                                         │
├─────────────────────────────────────────┤
│  Autolearn - Page 1                     │
└─────────────────────────────────────────┘
```

---

**Suis ces étapes et ton logo s'affichera dans le PDF ! 🚀**
