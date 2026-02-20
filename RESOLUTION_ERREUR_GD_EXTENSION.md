# 🔧 Résolution - Extension PHP GD Manquante

## ❌ Erreur

```
The PHP GD extension is required, but is not installed.
```

## 🎯 Cause

L'extension PHP GD n'est pas activée dans votre configuration PHP. Cette extension est nécessaire pour que Dompdf puisse traiter les images (logo Autolearn dans le PDF).

---

## ✅ Solution - Activer GD dans XAMPP

### Étape 1 : Ouvrir le fichier php.ini

**Option A : Via XAMPP Control Panel**
1. Ouvrir XAMPP Control Panel
2. Cliquer sur le bouton **"Config"** à côté de Apache
3. Sélectionner **"PHP (php.ini)"**

**Option B : Manuellement**
1. Aller dans le dossier XAMPP : `C:\xampp\php\`
2. Ouvrir le fichier `php.ini` avec un éditeur de texte

---

### Étape 2 : Activer l'extension GD

1. Dans le fichier `php.ini`, chercher la ligne :
```ini
;extension=gd
```

2. **Supprimer le point-virgule** au début de la ligne :
```ini
extension=gd
```

**Note :** Le point-virgule `;` signifie que la ligne est commentée (désactivée).

---

### Étape 3 : Sauvegarder et Redémarrer Apache

1. **Sauvegarder** le fichier `php.ini`
2. Ouvrir **XAMPP Control Panel**
3. **Arrêter** Apache (bouton "Stop")
4. **Démarrer** Apache (bouton "Start")

---

### Étape 4 : Vérifier que GD est activé

**Option A : Via PHP Info**
1. Créer un fichier `info.php` dans `C:\xampp\htdocs\` :
```php
<?php
phpinfo();
?>
```

2. Ouvrir dans le navigateur : `http://localhost/info.php`
3. Chercher "gd" dans la page (Ctrl+F)
4. Vérifier que la section **"GD Support"** affiche **"enabled"**

**Option B : Via ligne de commande**
```bash
php -m | findstr gd
```

Si GD est activé, vous verrez :
```
gd
```

---

## 🚀 Tester le PDF

Une fois GD activé :

1. Retourner sur la page du chapitre
2. Cliquer sur **"Prévisualiser PDF"** ou **"Télécharger PDF"**
3. Le PDF devrait se générer correctement avec le logo

---

## 🔄 Solution Alternative (Sans Logo)

Si vous ne pouvez pas activer GD immédiatement, vous pouvez temporairement désactiver le logo dans le template PDF.

### Modifier le template

Éditer `templates/pdf/chapitre.html.twig` :

**Avant :**
```twig
<div class="header">
    <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" alt="Autolearn Logo">
    <h1>{{ chapitre.titre }}</h1>
</div>
```

**Après :**
```twig
<div class="header">
    {# <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" alt="Autolearn Logo"> #}
    <h2 style="color: #4A90E2; margin: 0;">🎓 AUTOLEARN</h2>
    <h1>{{ chapitre.titre }}</h1>
</div>
```

Cette solution remplace le logo par du texte stylisé.

---

## 📋 Checklist de Vérification

- [ ] Fichier `php.ini` ouvert
- [ ] Ligne `extension=gd` décommentée (sans `;`)
- [ ] Fichier `php.ini` sauvegardé
- [ ] Apache redémarré
- [ ] GD vérifié avec `php -m | findstr gd`
- [ ] PDF testé et fonctionne

---

## 🐛 Si ça ne marche toujours pas

### Vérifier la version de PHP

```bash
php -v
```

Si vous utilisez PHP 8.x, GD devrait être inclus par défaut.

### Vérifier le bon fichier php.ini

XAMPP peut avoir plusieurs fichiers `php.ini`. Vérifier lequel est utilisé :

```bash
php --ini
```

Cela affichera :
```
Loaded Configuration File: C:\xampp\php\php.ini
```

Assurez-vous de modifier le bon fichier.

### Réinstaller XAMPP

Si rien ne fonctionne, vous pouvez réinstaller XAMPP avec la dernière version qui inclut GD par défaut.

---

## ✅ Résultat Attendu

Après avoir activé GD et redémarré Apache :

- ✅ Le PDF se génère sans erreur
- ✅ Le logo Autolearn s'affiche dans le header
- ✅ Toutes les images du PDF sont visibles

---

**L'extension GD est maintenant activée ! Le PDF devrait fonctionner. 🎉**
