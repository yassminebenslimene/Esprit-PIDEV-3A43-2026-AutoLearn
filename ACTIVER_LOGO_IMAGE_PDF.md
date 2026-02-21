# 🖼️ Activer le Logo Image dans le PDF

## ✅ Fichier Prêt
Tu as déjà le logo : `public/frontoffice/images/auto.png` ✅

## 🔧 Étapes pour l'Activer

### 1️⃣ Activer l'extension PHP GD

**a) Ouvrir php.ini**
- Ouvre XAMPP Control Panel
- Clique sur "Config" à côté de Apache
- Sélectionne "PHP (php.ini)"

**b) Modifier la ligne**
- Appuie sur Ctrl+F et cherche : `extension=gd`
- Tu vas trouver : `;extension=gd`
- Supprime le `;` pour avoir : `extension=gd`
- Sauvegarde (Ctrl+S)

**c) Redémarrer Apache**
- Dans XAMPP Control Panel
- Stop Apache
- Start Apache

### 2️⃣ Vérifier que GD est activé

Ouvre un terminal et tape :
```bash
php -m | findstr gd
```

Si tu vois `gd`, c'est activé ! ✅

### 3️⃣ Tester le PDF

1. Va sur un chapitre
2. Clique sur "Prévisualiser PDF"
3. Le logo `auto.png` devrait maintenant s'afficher ! 🎉

---

## 🎨 Résultat Attendu

```
┌─────────────────────────────────────────┐
│        [LOGO AUTO.PNG]                  │  ← Ton logo
│    Structures Conditionnelles           │
├─────────────────────────────────────────┤
│                                         │
│  Prendre des Décisions avec if...      │
│                                         │
```

---

## 🐛 Si le logo ne s'affiche toujours pas

### Vérifier le chemin du fichier
```bash
dir public\frontoffice\images\auto.png
```

Le fichier doit exister.

### Vérifier que GD est bien activé
```bash
php -m | findstr gd
```

Doit afficher : `gd`

### Vider le cache
```bash
php bin/console cache:clear
```

---

## ✅ Checklist

- [ ] Extension GD activée dans php.ini
- [ ] Apache redémarré
- [ ] GD vérifié avec `php -m | findstr gd`
- [ ] Fichier `auto.png` existe dans `public/frontoffice/images/`
- [ ] Cache Symfony vidé
- [ ] PDF régénéré et testé

---

**Ton logo devrait maintenant s'afficher dans le PDF ! 🎉**
