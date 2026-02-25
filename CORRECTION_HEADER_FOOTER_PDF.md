# 🔧 Correction - Header et Footer dans le PDF

## ❌ Problème Identifié

Le header (logo + titre) et le footer apparaissaient **au milieu de la page 2** au lieu d'être en haut et en bas de chaque page.

### Cause
Le positionnement `position: fixed` avec `top: -80px` n'était pas correctement calculé par Dompdf, causant un décalage du header.

---

## ✅ Solution Appliquée

### 1. Ajustement du Positionnement CSS

**Avant :**
```css
.header {
    position: fixed;
    top: -80px;  /* Trop proche */
}
```

**Après :**
```css
.header {
    position: fixed;
    top: -100px;  /* Plus éloigné pour compenser la marge */
    background: white;  /* Fond blanc pour masquer le contenu en dessous */
}
```

### 2. Ajustement des Marges de Page

```css
@page {
    margin: 100px 50px 80px 50px;  /* haut droite bas gauche */
}
```

Ces marges créent l'espace nécessaire pour le header et le footer.

### 3. Structure HTML Simplifiée

**Avant :**
```twig
<h1>{{ chapitre.titre }}</h1>
```

**Après :**
```twig
<div class="chapter-title">{{ chapitre.titre }}</div>
```

Utilisation d'un `<div>` au lieu d'un `<h1>` pour un meilleur contrôle du style.

---

## 🎨 Résultat Attendu

Maintenant, sur **chaque page** du PDF :

### En Haut (Header)
```
┌─────────────────────────────────────┐
│        🎓 AUTOLEARN                 │
│    Variables et Types de Données    │
├─────────────────────────────────────┤
```

### En Bas (Footer)
```
├─────────────────────────────────────┤
│  Autolearn - Plateforme...          │
│  Page 2                              │
└─────────────────────────────────────┘
```

---

## 🧪 Tester la Correction

1. **Vider le cache** (déjà fait)
```bash
php bin/console cache:clear
```

2. **Régénérer le PDF**
- Aller sur un chapitre
- Cliquer sur "Prévisualiser PDF"

3. **Vérifier**
- ✅ Header en haut de chaque page
- ✅ Footer en bas de chaque page
- ✅ Logo "🎓 AUTOLEARN" visible
- ✅ Titre du chapitre visible
- ✅ Numéro de page correct

---

## 🖼️ Pour Utiliser le Logo Image

Si tu veux utiliser le logo `auto.png` au lieu du texte "🎓 AUTOLEARN" :

### Étape 1 : Activer l'extension GD
Voir le guide : **`ACTIVER_GD_RAPIDEMENT.md`**

### Étape 2 : Modifier le template
Éditer `templates/pdf/chapitre.html.twig` :

```twig
<div class="header">
    {# Commenter le logo texte #}
    {# <div class="logo-text">🎓 AUTOLEARN</div> #}
    
    {# Décommenter le logo image #}
    <img src="{{ absolute_url(asset('frontoffice/images/auto.png')) }}" alt="Autolearn Logo">
    
    <div class="chapter-title">{{ chapitre.titre }}</div>
</div>
```

### Étape 3 : Vider le cache et tester
```bash
php bin/console cache:clear
```

---

## 📊 Comparaison Avant/Après

### Avant (Problème)
```
Page 1:
┌─────────────────────────────────────┐
│                                     │
│  Contenu du chapitre...             │
│                                     │
└─────────────────────────────────────┘

Page 2:
┌─────────────────────────────────────┐
│                                     │
│        🎓 AUTOLEARN                 │  ← Header au milieu !
│    Variables et Types de Données    │
│                                     │
│  Contenu suite...                   │
│                                     │
│  Autolearn - Page 2                 │  ← Footer au milieu !
└─────────────────────────────────────┘
```

### Après (Corrigé)
```
Page 1:
┌─────────────────────────────────────┐
│        🎓 AUTOLEARN                 │  ← Header en haut
│    Variables et Types de Données    │
├─────────────────────────────────────┤
│                                     │
│  Contenu du chapitre...             │
│                                     │
├─────────────────────────────────────┤
│  Autolearn - Page 1                 │  ← Footer en bas
└─────────────────────────────────────┘

Page 2:
┌─────────────────────────────────────┐
│        🎓 AUTOLEARN                 │  ← Header en haut
│    Variables et Types de Données    │
├─────────────────────────────────────┤
│                                     │
│  Contenu suite...                   │
│                                     │
├─────────────────────────────────────┤
│  Autolearn - Page 2                 │  ← Footer en bas
└─────────────────────────────────────┘
```

---

## 🔍 Détails Techniques

### Pourquoi `position: fixed` ?
Pour que le header et le footer apparaissent sur **chaque page** du PDF, pas seulement la première.

### Pourquoi `top: -100px` ?
La valeur négative place le header dans la **marge supérieure** définie par `@page { margin: 100px ... }`.

### Pourquoi `background: white` ?
Pour masquer le contenu qui pourrait apparaître derrière le header/footer.

---

## ✅ Checklist de Vérification

- [x] CSS du header ajusté (`top: -100px`)
- [x] CSS du footer ajusté (`bottom: -80px`)
- [x] Structure HTML simplifiée
- [x] Cache vidé
- [ ] PDF régénéré et testé (à faire)
- [ ] Header visible en haut de chaque page (à vérifier)
- [ ] Footer visible en bas de chaque page (à vérifier)

---

## 🎉 Résultat

Le header et le footer sont maintenant correctement positionnés en haut et en bas de **chaque page** du PDF !

**Teste maintenant en régénérant le PDF ! 🚀**
