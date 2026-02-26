# 🔧 Correction du Problème d'Encodage UTF-8

## 🚨 Problème Identifié

Le fichier `SPRINT_BACKLOG_COMPLET_PARTIE3.html` contient des caractères français mal encodés:

- "Créer" apparaît comme "CrÃ©er"
- "Intégrer" apparaît comme "IntÃ©grer"
- "Événements" apparaît comme "Ã‰vÃ©nements"
- "Détaillé" apparaît comme "DÃ©taillÃ©"

## 🎯 Solutions Possibles

### Solution 1: Utiliser Notepad++ (RECOMMANDÉ)

1. **Télécharger Notepad++** (si pas déjà installé):
   - https://notepad-plus-plus.org/downloads/

2. **Ouvrir le fichier**:
   - Ouvrir `SPRINT_BACKLOG_COMPLET_PARTIE3.html` avec Notepad++

3. **Convertir l'encodage**:
   - Menu: `Encodage` → `Convertir en UTF-8 (sans BOM)`
   - Sauvegarder: `Ctrl+S`

4. **Vérifier**:
   - Ouvrir le fichier dans le navigateur
   - Les caractères français doivent être corrects

### Solution 2: Utiliser Visual Studio Code

1. **Ouvrir le fichier** dans VS Code

2. **Changer l'encodage**:
   - Cliquer sur l'encodage en bas à droite (probablement "UTF-8 with BOM" ou "Windows-1252")
   - Sélectionner "Reopen with Encoding"
   - Choisir "Western (Windows 1252)"
   - Le fichier devrait maintenant afficher correctement les caractères

3. **Sauvegarder avec le bon encodage**:
   - Cliquer à nouveau sur l'encodage en bas à droite
   - Sélectionner "Save with Encoding"
   - Choisir "UTF-8"
   - Sauvegarder

### Solution 3: Utiliser PowerShell (Avancé)

```powershell
# Lire le fichier avec l'encodage Windows-1252
$content = [System.IO.File]::ReadAllText("SPRINT_BACKLOG_COMPLET_PARTIE3.html", [System.Text.Encoding]::GetEncoding(1252))

# Écrire avec UTF-8 sans BOM
$utf8NoBom = New-Object System.Text.UTF8Encoding $false
[System.IO.File]::WriteAllText("SPRINT_BACKLOG_COMPLET_PARTIE3.html", $content, $utf8NoBom)
```

### Solution 4: Rechercher/Remplacer Manuel

Si les solutions ci-dessus ne fonctionnent pas, vous pouvez faire un rechercher/remplacer:

**Dans Notepad++ ou VS Code:**

| Rechercher | Remplacer par |
|------------|---------------|
| Ã© | é |
| Ã¨ | è |
| Ã  | à |
| Ã§ | ç |
| Ã´ | ô |
| Ã® | î |
| Ã» | û |
| Ã‰ | É |
| Ã€ | À |
| Ãª | ê |

**Caractères les plus fréquents à corriger:**
- `DÃ©taillÃ©` → `Détaillé`
- `CrÃ©er` → `Créer`
- `IntÃ©grer` → `Intégrer`
- `Ã‰vÃ©nements` → `Événements`
- `gÃ©nÃ¨re` → `génère`
- `mÃ©tÃ©o` → `météo`
- `prÃ©vue` → `prévue`
- `Ã©quipes` → `équipes`
- `RÃ©cupÃ©rer` → `Récupérer`
- `vÃ©rifier` → `vérifier`

## ✅ Vérification

Après correction, ouvrez le fichier dans le navigateur et vérifiez que:

1. Le titre affiche: "Sprint Backlog Détaillé - Partie 3"
2. Les descriptions sont lisibles en français
3. Les accents sont corrects partout

## 🔍 Pourquoi ce Problème?

Le fichier a été créé en fusionnant plusieurs fichiers temporaires avec PowerShell, et l'encodage par défaut de Windows (Windows-1252) a été utilisé au lieu de UTF-8.

## 📝 Note Importante

Les fichiers `SPRINT_BACKLOG_COMPLET_PARTIE1.html` et `SPRINT_BACKLOG_COMPLET_PARTIE2.html` peuvent avoir le même problème. Appliquez la même solution si nécessaire.

## 🆘 Si Rien ne Fonctionne

Si aucune solution ne fonctionne, je peux recréer le fichier complètement avec le bon encodage. Faites-le moi savoir!

---

**Recommandation:** Utilisez la Solution 1 (Notepad++) - c'est la plus simple et la plus fiable.
