# 🔧 FIX: Statut des Événements Passés

## 📊 PROBLÈME IDENTIFIÉ

**Symptôme**: Les événements dont la date de fin est dépassée restent avec le statut "En cours" au lieu de passer à "Passé".

**Exemple**: 
- Événement: "Conférence data science"
- Date fin: 20/02/2026 22:00 (déjà passée)
- Statut affiché: "En cours" ❌
- Statut attendu: "Passé" ✅

## 🔍 CAUSE RACINE

### 1. Enum StatutEvenement Incomplet

**Fichier**: `src/Enum/StatutEvenement.php`

**AVANT**:
```php
enum StatutEvenement: string
{
    case PLANIFIE = 'Plannifié';
    case EN_COURS = 'En cours';
    case ANNULE = 'Annulé';
    // ❌ Pas de statut "PASSE"
}
```

### 2. Méthode updateStatus() Incomplète

**Fichier**: `src/Entity/Evenement.php`

**AVANT**:
```php
public function updateStatus(): void
{
    $today = new \DateTime();
    $today->setTime(0, 0, 0); // ❌ Compare seulement les dates, pas les heures
    
    // ...
    
    // Si la date d'aujourd'hui est après dateFin, on garde le statut actuel
    // ❌ Ne met PAS à jour le statut à "PASSE"
}
```

**Problèmes**:
1. Comparaison uniquement sur les dates (sans heures)
2. Aucune gestion du cas "événement terminé"
3. Pas de statut "PASSE" dans l'enum

## ✅ SOLUTION APPLIQUÉE

### 1. Ajout du Statut "PASSE"

**Fichier**: `src/Enum/StatutEvenement.php`

```php
enum StatutEvenement: string
{
    case PLANIFIE = 'Plannifié';
    case EN_COURS = 'En cours';
    case PASSE = 'Passé';      // ✅ AJOUTÉ
    case ANNULE = 'Annulé';
}
```

### 2. Correction de la Méthode updateStatus()

**Fichier**: `src/Entity/Evenement.php`

```php
public function updateStatus(): void
{
    $now = new \DateTime(); // ✅ Utilise date ET heure
    
    // Si l'événement est annulé
    if ($this->getIsCanceled()) {
        $this->setStatus(StatutEvenement::ANNULE);
    } 
    // ✅ Si la date/heure actuelle est après la date de fin
    elseif ($now > $this->getDateFin()) {
        $this->setStatus(StatutEvenement::PASSE);
    }
    // Si la date/heure actuelle est entre dateDebut et dateFin
    elseif ($now >= $this->getDateDebut() && $now <= $this->getDateFin()) {
        $this->setStatus(StatutEvenement::EN_COURS);
    } 
    // Si la date/heure actuelle est avant dateDebut
    elseif ($now < $this->getDateDebut()) {
        $this->setStatus(StatutEvenement::PLANIFIE);
    }
}
```

**Améliorations**:
- ✅ Compare date ET heure (pas seulement la date)
- ✅ Met à jour le statut à "PASSE" quand l'événement est terminé
- ✅ Logique claire et complète

### 3. Mise à Jour du Template

**Fichier**: `templates/backoffice/evenement/index.html.twig`

```twig
<span class="status-badge {% if evenement.status.value == 'En cours' %}processing{% elseif evenement.status.value == 'Annulé' %}pending{% elseif evenement.status.value == 'Passé' %}completed{% else %}completed{% endif %}">
    {{ evenement.status.value }}
</span>
```

Le statut "Passé" s'affiche maintenant avec le badge vert "completed".

### 4. Commande de Mise à Jour

**Fichier**: `src/Command/UpdateEvenementsStatusCommand.php`

Commande Symfony pour mettre à jour les statuts de tous les événements existants:

```bash
php bin/console app:update-evenements-status
```

**Résultat**:
```
✅ Conférence data science: En cours → Passé (Date fin: 20/02/2026 22:00)
✅ 1 événement(s) mis à jour
```

## 🎯 RÉSULTAT

### Avant le Fix
- ❌ Événements passés restent "En cours"
- ❌ Comparaison uniquement sur les dates (sans heures)
- ❌ Pas de statut "Passé" disponible

### Après le Fix
- ✅ Événements passés ont le statut "Passé"
- ✅ Comparaison sur date ET heure
- ✅ Statut "Passé" ajouté à l'enum
- ✅ Badge vert "completed" pour les événements passés
- ✅ Mise à jour automatique à chaque chargement de la page

## 📝 UTILISATION

### Mise à Jour Automatique

Le statut est mis à jour automatiquement:
- À chaque affichage de la liste des événements (backoffice)
- À chaque modification d'un événement
- À chaque création d'un événement

### Mise à Jour Manuelle (si nécessaire)

Si tu veux forcer la mise à jour de tous les événements:

```bash
php bin/console app:update-evenements-status
```

## 🔄 LOGIQUE DES STATUTS

```
Maintenant < dateDebut     → PLANIFIE
dateDebut ≤ Maintenant ≤ dateFin → EN_COURS
Maintenant > dateFin       → PASSE
isCanceled = true          → ANNULE
```

## 📊 FICHIERS MODIFIÉS

```
src/Enum/StatutEvenement.php                    [MODIFIÉ]
src/Entity/Evenement.php                        [MODIFIÉ]
templates/backoffice/evenement/index.html.twig  [MODIFIÉ]
src/Command/UpdateEvenementsStatusCommand.php   [CRÉÉ]
FIX_STATUT_EVENEMENTS_PASSES.md                 [CRÉÉ]
```

## ✅ TESTS

1. ✅ Événement "Conférence data science" (fin: 20/02 22:00) → Statut: "Passé"
2. ✅ Badge vert affiché correctement
3. ✅ Commande de mise à jour fonctionne
4. ✅ Mise à jour automatique à chaque chargement

## 🎉 CONCLUSION

Le problème est résolu! Les événements passés affichent maintenant correctement le statut "Passé" avec un badge vert. La mise à jour est automatique et fonctionne avec date ET heure.

---

**Date**: 21 Février 2026
**Auteur**: Kiro AI Assistant
**Statut**: ✅ Résolu et testé
