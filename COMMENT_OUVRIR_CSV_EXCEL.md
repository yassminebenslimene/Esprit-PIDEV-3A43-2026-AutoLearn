# 📊 Comment Ouvrir le Sprint Backlog CSV dans Excel

## 🎯 Méthode 1: Import de Données (RECOMMANDÉE)

### Étapes:
1. **Ouvrir Excel** (nouveau classeur vide)
2. Aller dans l'onglet **"Données"** (Data)
3. Cliquer sur **"À partir d'un fichier texte/CSV"** (From Text/CSV)
4. Sélectionner le fichier `SPRINT_BACKLOG_MODULE_GESTION_UTILISATEUR.csv`
5. Dans la fenêtre d'aperçu:
   - **Délimiteur**: Choisir **"Point-virgule"** (;)
   - **Encodage**: UTF-8
   - Vérifier que les colonnes sont bien séparées dans l'aperçu
6. Cliquer sur **"Charger"** (Load)

✅ **Résultat**: Tableau parfaitement organisé avec 8 colonnes!

---

## 🎯 Méthode 2: Assistant d'Importation

### Étapes:
1. **Ouvrir Excel** (nouveau classeur vide)
2. Aller dans **"Fichier" → "Ouvrir"**
3. Sélectionner **"Tous les fichiers (*.*)"** dans le filtre
4. Sélectionner le fichier CSV
5. L'**Assistant Importation** s'ouvre:
   - **Étape 1**: Choisir "Délimité" → Suivant
   - **Étape 2**: Cocher **"Point-virgule"** (décocher les autres) → Suivant
   - **Étape 3**: Cliquer sur "Terminer"

✅ **Résultat**: Colonnes bien organisées!

---

## 🎯 Méthode 3: Ouverture Directe (Si Excel est configuré)

### Étapes:
1. **Clic droit** sur le fichier CSV
2. **"Ouvrir avec" → "Excel"**
3. Si les colonnes ne sont pas séparées:
   - Sélectionner toute la colonne A
   - Aller dans **"Données" → "Convertir"**
   - Choisir **"Délimité"** → Suivant
   - Cocher **"Point-virgule"** → Terminer

---

## 📋 Structure du Tableau

Le fichier contient **8 colonnes**:

| # | Colonne | Description |
|---|---------|-------------|
| 1 | **ID US** | Identifiant User Story (US-1.1, US-1.2, etc.) |
| 2 | **User Story (US)** | Description complète de la User Story |
| 3 | **ID Tâche** | Identifiant de la tâche (T1.1, T1.2, etc.) |
| 4 | **Tâches effectuées** | Description détaillée de la tâche |
| 5 | **Estimation** | Temps estimé (0.5h, 1h, 2h, etc.) |
| 6 | **Responsable** | Ilef Yousfi (toutes les tâches) |
| 7 | **Sprint** | Sprint 1, Sprint 2 ou Sprint 3 |
| 8 | **Statut** | Terminé (toutes les tâches) |

---

## 📊 Statistiques

- **Total lignes**: 131 (1 en-tête + 130 tâches)
- **Total User Stories**: 18
- **Total Tâches**: 130
- **Total Heures**: 120h
- **Sprints**: 3 (1 semaine chacun)

---

## 🎨 Mise en Forme Recommandée

### Après ouverture dans Excel:

1. **Figer la première ligne** (en-têtes):
   - Sélectionner ligne 2
   - "Affichage" → "Figer les volets" → "Figer la ligne supérieure"

2. **Mettre en forme l'en-tête**:
   - Sélectionner ligne 1
   - Fond: Bleu foncé
   - Texte: Blanc, Gras
   - Alignement: Centré

3. **Ajuster largeur colonnes**:
   - Double-cliquer sur les séparateurs de colonnes (ajustement auto)
   - Ou sélectionner toutes les colonnes → Clic droit → "Largeur de colonne optimale"

4. **Ajouter filtres**:
   - Sélectionner ligne 1
   - "Données" → "Filtrer"

5. **Bordures**:
   - Sélectionner tout le tableau
   - "Accueil" → "Bordures" → "Toutes les bordures"

---

## 🔗 FUSIONNER LES CELLULES (Éviter Redondance)

### Pour fusionner les User Stories identiques:

**Méthode Automatique avec Macro VBA:**

1. Appuyer sur `Alt + F11` pour ouvrir l'éditeur VBA
2. Menu "Insertion" → "Module"
3. Coller ce code:

```vba
Sub FusionnerUserStories()
    Dim ws As Worksheet
    Dim lastRow As Long, i As Long
    Dim currentUS As String, startRow As Long
    
    Set ws = ActiveSheet
    lastRow = ws.Cells(ws.Rows.Count, "A").End(xlUp).Row
    
    ' Désactiver les alertes
    Application.DisplayAlerts = False
    
    startRow = 2
    currentUS = ws.Cells(2, 1).Value
    
    For i = 3 To lastRow + 1
        If i > lastRow Or ws.Cells(i, 1).Value <> currentUS Then
            ' Fusionner colonne A (ID US)
            If i - 1 > startRow Then
                ws.Range("A" & startRow & ":A" & (i - 1)).Merge
                ws.Range("A" & startRow).VerticalAlignment = xlCenter
                
                ' Fusionner colonne B (User Story)
                ws.Range("B" & startRow & ":B" & (i - 1)).Merge
                ws.Range("B" & startRow).VerticalAlignment = xlCenter
                ws.Range("B" & startRow).WrapText = True
            End If
            
            If i <= lastRow Then
                startRow = i
                currentUS = ws.Cells(i, 1).Value
            End If
        End If
    Next i
    
    Application.DisplayAlerts = True
    MsgBox "Fusion terminée! " & (lastRow - 1) & " lignes traitées.", vbInformation
End Sub
```

4. Appuyer sur `F5` pour exécuter
5. Fermer l'éditeur VBA (`Alt + Q`)

✅ **Résultat**: Toutes les cellules avec la même User Story sont fusionnées automatiquement!

---

**Méthode Manuelle (si VBA ne fonctionne pas):**

Pour chaque User Story répétée:
1. Sélectionner les cellules identiques dans colonne A (ID US)
2. Clic droit → "Fusionner les cellules"
3. Répéter pour colonne B (User Story)

**Exemple**: 
- US-1.1 apparaît lignes 2-11 (10 fois) → Sélectionner A2:A11 → Fusionner
- US-1.2 apparaît lignes 12-20 (9 fois) → Sélectionner A12:A20 → Fusionner
- etc.

---

## ⚠️ Problèmes Courants

### Problème 1: Tout dans une seule colonne
**Solution**: Utiliser Méthode 1 ou 2 ci-dessus avec délimiteur point-virgule

### Problème 2: Caractères bizarres (é, è, à)
**Solution**: Lors de l'import, choisir encodage **UTF-8**

### Problème 3: Excel ouvre automatiquement avec virgule
**Solution**: 
- Ne PAS double-cliquer sur le fichier
- Utiliser Méthode 1 (Import de données)

---

## 📁 Fichiers Disponibles

1. **SPRINT_BACKLOG_MODULE_GESTION_UTILISATEUR.csv** ← Fichier Excel
2. **SPRINT_BACKLOG_RESUME.md** ← Documentation complète (Markdown)
3. **COMMENT_OUVRIR_CSV_EXCEL.md** ← Ce guide

---

## ✅ Vérification

Après ouverture, vous devriez voir:
- ✅ 8 colonnes bien séparées
- ✅ 131 lignes (1 en-tête + 130 tâches)
- ✅ Texte lisible (pas de caractères bizarres)
- ✅ Toutes les User Stories visibles
- ✅ Tous les sprints (1, 2, 3)

---

**Responsable**: Ilef Yousfi  
**Date**: Février 2026  
**Format**: CSV (délimiteur point-virgule)
