# Correction des templates - Migration vers durée

## Problème résolu

Erreur: `Neither the property "dateDebut" nor one of the methods "dateDebut()", "getdateDebut()"... exist`

Les templates utilisaient encore les anciennes propriétés `dateDebut` et `dateFin` qui ont été remplacées par `duree` dans l'entité Challenge.

## Templates corrigés

### 1. `templates/frontoffice/index.html.twig`
**Avant:**
```twig
<li>
    <span>Début:</span>
    <h6>{{ challenge.dateDebut|date('d M Y') }}</h6>
</li>
<li>
    <span>Fin:</span>
    <h6>{{ challenge.dateFin|date('d M Y') }}</h6>
</li>
```

**Après:**
```twig
<li>
    <span>Durée:</span>
    <h6>{{ challenge.duree }} min</h6>
</li>
```

### 2. `templates/frontoffice/challenge_show.html.twig`
**Avant:**
```twig
<div class="info-item">
    <div class="info-label">Date de début</div>
    <div class="info-value">
        <i class="fa fa-calendar"></i>
        {{ challenge.dateDebut|date('d M Y') }}
    </div>
</div>
<div class="info-item">
    <div class="info-label">Date de fin</div>
    <div class="info-value">
        <i class="fa fa-calendar-check-o"></i>
        {{ challenge.dateFin|date('d M Y') }}
    </div>
</div>
```

**Après:**
```twig
<div class="info-item">
    <div class="info-label">Durée</div>
    <div class="info-value">
        <i class="fa fa-clock-o"></i>
        {{ challenge.duree }} minutes
    </div>
</div>
```

### 3. `templates/backoffice/challenge.html.twig`
**Avant:**
```html
<th>Date début</th>
<th>Date fin</th>
...
<td>{{ challenge.dateDebut|date('Y-m-d') }}</td>
<td>{{ challenge.dateFin|date('Y-m-d') }}</td>
```

**Après:**
```html
<th>Durée</th>
...
<td>{{ challenge.duree }} min</td>
```

**JavaScript également corrigé:**
```javascript
// Avant
<td>${challenge.dateDebut}</td>
<td>${challenge.dateFin}</td>

// Après
<td>${challenge.duree} min</td>
```

### 4. `templates/frontoffice/challenges.html.twig`
**Avant:**
```twig
<p class="mt-3">
    <small>Du {{ challenge.dateDebut|date('d/m/Y') }} au {{ challenge.dateFin|date('d/m/Y') }}</small>
</p>
```

**Après:**
```twig
<p class="mt-3">
    <small><i class="fa fa-clock-o"></i> Durée: {{ challenge.duree }} minutes</small>
</p>
```

## Résultat

✅ Tous les templates ont été mis à jour
✅ L'application fonctionne sans erreur
✅ L'affichage est cohérent partout (durée en minutes)
✅ Cache nettoyé

## Commits

1. `060eef8` - feat: Add AI exercise generation, rating system, and challenge duration
2. `458caf7` - fix: Update all templates to use duree instead of dateDebut/dateFin

---

**Date:** 1er mars 2026
