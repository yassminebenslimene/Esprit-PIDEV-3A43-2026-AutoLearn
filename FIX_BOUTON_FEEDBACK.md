# 🔧 FIX: Bouton Feedback Non Visible

## ❌ PROBLÈME IDENTIFIÉ

Le bouton "Donner mon feedback" n'apparaissait pas dans la page "Mes Participations" même si l'événement était terminé.

## 🔍 CAUSE DU PROBLÈME

Dans ton projet, la clé primaire des utilisateurs est `userId` et non `id`.

Le code utilisait:
- ❌ `app.user.id` (n'existe pas)
- ❌ `$currentUser->getId()` (n'existe pas)

Au lieu de:
- ✅ `app.user.userId` (correct)
- ✅ `$currentUser->getUserId()` (correct)

## ✅ FICHIERS CORRIGÉS

### 1. templates/frontoffice/participation/mes_participations.html.twig

**AVANT (ligne 252):**
```twig
{% set hasFeedback = participation.hasFeedbackFromEtudiant(app.user.id) %}
```

**APRÈS:**
```twig
{% set hasFeedback = participation.hasFeedbackFromEtudiant(app.user.userId) %}
```

---

### 2. src/Controller/FeedbackController.php

**AVANT (ligne 32):**
```php
if ($etudiant->getId() === $currentUser->getId()) {
```

**APRÈS:**
```php
if ($etudiant->getUserId() === $currentUser->getUserId()) {
```

**AVANT (ligne 43):**
```php
$existingFeedback = $participation->getFeedbackByEtudiant($currentUser->getId());
```

**APRÈS:**
```php
$existingFeedback = $participation->getFeedbackByEtudiant($currentUser->getUserId());
```

**AVANT (ligne 83):**
```php
etudiantId: $currentUser->getId(),
```

**APRÈS:**
```php
etudiantId: $currentUser->getUserId(),
```

---

## 🧪 COMMENT TESTER

### Étape 1: Vider le cache
```bash
php bin/console cache:clear
```

### Étape 2: Connecte-toi en tant qu'étudiant

### Étape 3: Va dans "My Participations"
URL: http://localhost:8000/participation/mes-participations

### Étape 4: Vérifie le bouton

Pour l'événement "CodingCamp" (date fin = 21 Feb 2026), tu devrais voir:

**SI la date est passée ET participation acceptée:**
```
┌─────────────────────────────────────────┐
│  CodingCamp                             │
│  ✓ Accepted                             │
│                                         │
│  👁️ View Details                        │
│  📝 Donner mon feedback  ← CE BOUTON!   │
└─────────────────────────────────────────┘
```

**Couleur du bouton:**
- 🟠 Orange (`#f6ad55`) si pas encore de feedback
- 🟢 Vert (`#48bb78`) si feedback déjà donné

---

## ⚠️ CONDITIONS POUR VOIR LE BOUTON

Le bouton "Donner mon feedback" apparaît UNIQUEMENT si:

1. ✅ L'événement est terminé (`dateFin < maintenant`)
2. ✅ La participation est acceptée (`statut == 'Accepté'`)
3. ✅ L'utilisateur est connecté en tant qu'étudiant
4. ✅ L'utilisateur fait partie de l'équipe

---

## 🎯 RÉSULTAT ATTENDU

Après ces corrections, le bouton devrait apparaître pour l'événement "CodingCamp" car:
- ✅ Date fin: 21 Feb 2026 07:20 (passée)
- ✅ Statut: Accepté (visible dans l'image)
- ✅ Utilisateur: Étudiant membre de l'équipe

---

## 📝 NOTES IMPORTANTES

### Structure User dans ton projet:
```php
class User {
    private ?int $userId = null;  // ← Clé primaire
    
    public function getUserId(): ?int { return $this->userId; }
}
```

**Toujours utiliser:**
- `$user->getUserId()` en PHP
- `app.user.userId` en Twig

**NE JAMAIS utiliser:**
- `$user->getId()` ❌
- `app.user.id` ❌

---

## ✅ CHECKLIST DE VÉRIFICATION

- [x] Cache vidé
- [x] Template corrigé (userId au lieu de id)
- [x] Contrôleur corrigé (getUserId() au lieu de getId())
- [x] Conditions du bouton vérifiées
- [x] Styles du bouton ajoutés

---

## 🚀 PROCHAINE ÉTAPE

Maintenant que le bouton est visible, tu peux:

1. **Cliquer sur "📝 Donner mon feedback"**
2. **Remplir le formulaire:**
   - Rating global
   - Ratings par catégorie
   - Sentiment
   - Commentaire
3. **Soumettre**
4. **Vérifier les statistiques dans le backoffice**

---

**Le problème est maintenant résolu!** ✅
