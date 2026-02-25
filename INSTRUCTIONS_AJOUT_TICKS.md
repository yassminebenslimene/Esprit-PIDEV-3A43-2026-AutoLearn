# Instructions pour Ajouter les Ticks ✓

## Tâches à NE PAS cocher (8 tâches):

1. **US-5.8 T-5.8.3**: Ajouter filtres par type et statut
2. **US-5.14 T-5.14.2**: Afficher message d'erreur approprié
3. **US-5.20 T-5.20.3**: Configurer tâche cron
4. **US-5.21 T-5.21.3**: Ajouter champ certificate_sent dans Participation
5. **US-5.26 T-5.26.2**: Créer le contrôleur FeedbackStatsController
6. **US-5.26 T-5.26.3**: Créer le template statistiques avec Chart.js
7. **US-5.30 T-5.30.1**: Ajouter vérification du statut
8. **US-5.30 T-5.30.2**: Masquer bouton Participer si en cours/terminé

## Format du Tick:

```html
<td><span class="done">✓</span>Description de la tâche</td>
```

## Style CSS (déjà ajouté):

```css
.done { 
    color: #2ed573; 
    font-weight: bold; 
    margin-right: 8px; 
}
```

## Modification Manuelle Rapide:

Dans le fichier `SPRINT_BACKLOG_COMPLET_FINAL.html`, chercher et remplacer:

**REMPLACER**:
```html
<td>Créer
```

**PAR**:
```html
<td><span class="done">✓</span>Créer
```

**PUIS REMPLACER**:
```html
<td>Ajouter
```

**PAR**:
```html
<td><span class="done">✓</span>Ajouter
```

**PUIS REMPLACER**:
```html
<td>Générer
```

**PAR**:
```html
<td><span class="done">✓</span>Générer
```

**PUIS REMPLACER**:
```html
<td>Installer
```

**PAR**:
```html
<td><span class="done">✓</span>Installer
```

**PUIS REMPLACER**:
```html
<td>Configurer
```

**PAR**:
```html
<td><span class="done">✓</span>Configurer
```

**PUIS REMPLACER**:
```html
<td>Implémenter
```

**PAR**:
```html
<td><span class="done">✓</span>Implémenter
```

**PUIS REMPLACER**:
```html
<td>Appeler
```

**PAR**:
```html
<td><span class="done">✓</span>Appeler
```

**PUIS REMPLACER**:
```html
<td>Afficher
```

**PAR**:
```html
<td><span class="done">✓</span>Afficher
```

**PUIS REMPLACER**:
```html
<td>Attacher
```

**PAR**:
```html
<td><span class="done">✓</span>Attacher
```

**PUIS REMPLACER**:
```html
<td>Masquer
```

**PAR**:
```html
<td><span class="done">✓</span>Masquer
```

**PUIS REMPLACER**:
```html
<td>Vérifier
```

**PAR**:
```html
<td><span class="done">✓</span>Vérifier
```

**PUIS REMPLACER**:
```html
<td>Tester
```

**PAR**:
```html
<td><span class="done">✓</span>Tester
```

## Ensuite, RETIRER les ticks des 8 tâches non réalisées:

1. Chercher: `<td><span class="done">✓</span>Ajouter filtres par type et statut`
   Remplacer par: `<td>Ajouter filtres par type et statut`

2. Chercher: `<td><span class="done">✓</span>Afficher message d'erreur approprié`
   Remplacer par: `<td>Afficher message d'erreur approprié`

3. Chercher: `<td><span class="done">✓</span>Configurer tâche cron`
   Remplacer par: `<td>Configurer tâche cron`

4. Chercher: `<td><span class="done">✓</span>Ajouter champ certificate_sent`
   Remplacer par: `<td>Ajouter champ certificate_sent`

5. Chercher: `<td><span class="done">✓</span>Créer le contrôleur FeedbackStatsController`
   Remplacer par: `<td>Créer le contrôleur FeedbackStatsController`

6. Chercher: `<td><span class="done">✓</span>Créer le template statistiques avec Chart.js`
   Remplacer par: `<td>Créer le template statistiques avec Chart.js`

7. Chercher: `<td><span class="done">✓</span>Ajouter vérification du statut`
   Remplacer par: `<td>Ajouter vérification du statut`

8. Chercher: `<td><span class="done">✓</span>Masquer bouton Participer si en cours/terminé`
   Remplacer par: `<td>Masquer bouton Participer si en cours/terminé`

## Résultat Final:

- 180 tâches avec ✓ vert
- 8 tâches sans ✓ (non réalisées)
- Total: 188 tâches

---

**Note**: Utilise la fonction "Rechercher et Remplacer" de ton éditeur de texte (Ctrl+H) pour faire ces modifications rapidement.
