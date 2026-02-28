# ✅ Solution Simple pour Corriger l'Encodage

## 🎯 Solution la Plus Rapide

### Étape 1: Télécharger Notepad++
- Aller sur: https://notepad-plus-plus.org/downloads/
- Télécharger et installer (gratuit)

### Étape 2: Ouvrir le Fichier
- Clic droit sur `SPRINT_BACKLOG_COMPLET_PARTIE3.html`
- "Modifier avec Notepad++"

### Étape 3: Convertir l'Encodage
1. Menu `Encodage`
2. Cliquer sur `Convertir en UTF-8 (sans BOM)`
3. Sauvegarder: `Ctrl+S`

### Étape 4: Vérifier
- Double-cliquer sur le fichier HTML
- Il devrait s'ouvrir dans le navigateur avec les accents corrects

## 🔄 Alternative: Rechercher/Remplacer dans Notepad++

Si la conversion automatique ne fonctionne pas:

1. Ouvrir le fichier dans Notepad++
2. `Ctrl+H` (Rechercher/Remplacer)
3. Cocher "Recherche étendue" (\n, \r, \t, \0, \x...)
4. Faire ces remplacements un par un:

```
Rechercher: Ã©     Remplacer: é
Rechercher: Ã¨     Remplacer: è
Rechercher: Ã      Remplacer: à
Rechercher: Ã§     Remplacer: ç
Rechercher: Ã´     Remplacer: ô
Rechercher: Ã®     Remplacer: î
Rechercher: Ã»     Remplacer: û
Rechercher: Ã‰     Remplacer: É
Rechercher: Ã€     Remplacer: À
Rechercher: Ãª     Remplacer: ê
```

5. Cliquer "Remplacer tout" pour chaque ligne
6. Sauvegarder

## ⚠️ Important

Les fichiers suivants peuvent avoir le même problème:
- `SPRINT_BACKLOG_COMPLET_PARTIE1.html`
- `SPRINT_BACKLOG_COMPLET_PARTIE2.html`

Appliquez la même solution si nécessaire.

## ✅ Résultat Attendu

Après correction, vous devriez voir:
- "Sprint Backlog Détaillé" (au lieu de "DÃ©taillÃ©")
- "Créer" (au lieu de "CrÃ©er")
- "Intégrer" (au lieu de "IntÃ©grer")
- "Événements" (au lieu de "Ã‰vÃ©nements")

---

**Temps estimé:** 2-3 minutes avec Notepad++
