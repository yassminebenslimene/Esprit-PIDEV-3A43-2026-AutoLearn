# ⚡ Activer GD MAINTENANT (2 minutes)

## 🎯 Pour afficher ton logo dans le PDF

### 1. Ouvrir XAMPP Control Panel

### 2. Cliquer sur "Config" à côté de Apache

### 3. Sélectionner "PHP (php.ini)"

### 4. Dans le fichier qui s'ouvre :
- Appuyer sur **Ctrl+F**
- Chercher : `extension=gd`
- Trouver la ligne : `;extension=gd`
- **Supprimer le `;`** pour avoir : `extension=gd`
- **Sauvegarder** (Ctrl+S)

### 5. Retourner dans XAMPP Control Panel :
- Cliquer sur **"Stop"** pour Apache
- Cliquer sur **"Start"** pour Apache

### 6. Vérifier dans un terminal :
```bash
php -m | findstr gd
```

Si tu vois `gd`, c'est bon ! ✅

### 7. Tester le PDF :
- Va sur un chapitre
- Clique sur "Prévisualiser PDF"
- Ton logo `auto.png` s'affiche ! 🎉

---

**C'est tout ! Fais-le maintenant ! ⚡**
