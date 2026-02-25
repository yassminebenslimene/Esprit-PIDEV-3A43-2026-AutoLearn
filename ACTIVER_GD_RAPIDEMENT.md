# ⚡ Activer GD en 2 Minutes

## 🎯 Pour afficher le logo image au lieu du texte

### Étape 1 : Ouvrir php.ini
1. Ouvrir XAMPP Control Panel
2. Cliquer sur **"Config"** à côté de Apache
3. Sélectionner **"PHP (php.ini)"**

### Étape 2 : Chercher et modifier
1. Appuyer sur **Ctrl+F** pour chercher
2. Taper : `extension=gd`
3. Trouver la ligne : `;extension=gd`
4. **Supprimer le `;`** au début pour avoir : `extension=gd`
5. **Sauvegarder** le fichier (Ctrl+S)

### Étape 3 : Redémarrer Apache
1. Dans XAMPP Control Panel
2. Cliquer sur **"Stop"** pour Apache
3. Cliquer sur **"Start"** pour Apache

### Étape 4 : Modifier le template PDF
Éditer `templates/pdf/chapitre.html.twig` :

**Commenter le logo texte et décommenter l'image :**

```twig
<!-- Header fixe -->
<div class="header">
    {# <div class="logo-text">🎓 AUTOLEARN</div> #}
    <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" alt="Autolearn Logo">
    <div class="chapter-title">{{ chapitre.titre }}</div>
</div>
```

### Étape 5 : Vider le cache
```bash
php bin/console cache:clear
```

### Étape 6 : Tester
Régénérer le PDF - le logo image devrait maintenant s'afficher !

---

**C'est tout ! Le logo image s'affichera maintenant. 🎉**
