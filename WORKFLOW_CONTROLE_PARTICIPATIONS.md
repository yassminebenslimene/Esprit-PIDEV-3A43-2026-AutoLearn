# 🔐 Workflow - Contrôle des Participations par État

## 📋 Vue d'Ensemble

Le Workflow Component gère maintenant automatiquement l'accès aux participations en fonction de l'état de l'événement. Cette fonctionnalité empêche les étudiants de s'inscrire à des événements qui sont déjà commencés, terminés ou annulés.

---

## 🎯 Règles de Gestion

### États du Workflow et Participations

| État Workflow | Participations Ouvertes? | Bouton "Participer" | Accès Page Participation |
|---------------|-------------------------|---------------------|-------------------------|
| **planifie** | ✅ OUI | ✅ Visible | ✅ Autorisé |
| **en_cours** | ❌ NON | ❌ Caché | ❌ Bloqué |
| **termine** | ❌ NON | ❌ Caché | ❌ Bloqué |
| **annule** | ❌ NON | ❌ Caché | ❌ Bloqué |

### Logique Métier

**Participations OUVERTES** uniquement si:
- ✅ L'événement est dans l'état `planifie`
- ✅ L'événement n'est PAS annulé (`isCanceled = false`)

**Participations FERMÉES** si:
- ❌ L'événement est `en_cours` (déjà commencé)
- ❌ L'événement est `termine` (déjà fini)
- ❌ L'événement est `annule` (annulé par l'admin)

---

## 🔧 Implémentation Technique

### 1. Méthodes dans l'Entité `Evenement`

#### Méthode `areParticipationsOpen()`

```php
/**
 * Vérifie si les participations sont ouvertes pour cet événement
 * Les participations sont ouvertes uniquement si:
 * - L'événement est planifié (pas encore commencé)
 * - L'événement n'est pas annulé
 * - L'événement n'est pas terminé
 */
public function areParticipationsOpen(): bool
{
    // Vérifier le workflow status
    return $this->workflowStatus === 'planifie' && !$this->isCanceled;
}
```

**Retourne**:
- `true` si l'événement accepte de nouvelles participations
- `false` sinon

#### Méthode `canAcceptParticipations()`

```php
/**
 * Vérifie si l'événement peut accepter de nouvelles participations
 * (alias de areParticipationsOpen pour plus de clarté)
 */
public function canAcceptParticipations(): bool
{
    return $this->areParticipationsOpen();
}
```

**Alias** de `areParticipationsOpen()` pour plus de clarté dans le code.

---

### 2. Contrôle dans le Contrôleur

#### Vérification dans `participate()`

```php
#[Route('/{id}/participate', name: 'app_event_participate', methods: ['GET'])]
public function participate(Evenement $evenement, ...): Response
{
    // Vérifier si les participations sont ouvertes (workflow)
    if (!$evenement->canAcceptParticipations()) {
        $message = match($evenement->getWorkflowStatus()) {
            'en_cours' => 'This event is currently in progress. New registrations are not accepted.',
            'termine' => 'This event has ended. Registrations are now closed.',
            'annule' => 'This event has been cancelled. No registrations are accepted.',
            default => 'Registrations are not available for this event.',
        };
        
        $this->addFlash('error', $message);
        return $this->redirectToRoute('app_events');
    }
    
    // ... reste du code
}
```

**Comportement**:
1. Vérifie si les participations sont ouvertes
2. Si NON → Message d'erreur personnalisé selon l'état
3. Redirection vers la liste des événements
4. Si OUI → Affiche la page de participation normalement

---

### 3. Affichage Conditionnel dans le Template

#### Template `index.html.twig`

```twig
{% if evenement.isCanceled %}
    {# Message: Événement annulé #}
    <div style="...">
        <h3>Event Cancelled</h3>
        <p>This event has been cancelled. No registrations are accepted.</p>
    </div>

{% elseif evenement.workflowStatus == 'termine' %}
    {# Message: Événement terminé #}
    <div style="...">
        <h3>Event Completed</h3>
        <p>This event has ended. Registrations are now closed.</p>
    </div>

{% elseif evenement.workflowStatus == 'en_cours' %}
    {# Message: Événement en cours #}
    <div style="...">
        <h3>Event In Progress</h3>
        <p>This event is currently happening. New registrations are not accepted.</p>
    </div>

{% elseif data.placesDisponibles > 0 %}
    {# Bouton Participer (visible uniquement si planifié) #}
    <a href="{{ path('app_event_participate', {'id': evenement.id}) }}" class="participate-btn">
        🎯 Participate in This Event
    </a>

{% else %}
    {# Message: Événement complet #}
    <p>❌ Event is full - No spots available</p>
{% endif %}
```

**Logique d'Affichage**:
1. **Annulé** → Badge rouge "Event Cancelled"
2. **Terminé** → Badge gris "Event Completed"
3. **En cours** → Badge jaune "Event In Progress"
4. **Planifié + Places disponibles** → Bouton "Participate"
5. **Planifié + Complet** → Message "Event is full"

---

## 🎨 Design des Messages

### Message "Event Cancelled" (Annulé)

```css
background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
border: 2px solid #e53e3e;
color: #991b1b;
icon: ❌
```

### Message "Event Completed" (Terminé)

```css
background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
border: 2px solid #718096;
color: #2d3748;
icon: 🏁
```

### Message "Event In Progress" (En cours)

```css
background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
border: 2px solid #f59e0b;
color: #92400e;
icon: ⏳
```

---

## 🔄 Flux de Travail Complet

### Scénario 1: Événement Planifié

```
1. Étudiant visite /events
2. Voit le bouton "🎯 Participate in This Event"
3. Clique sur le bouton
4. Contrôleur vérifie: canAcceptParticipations() → true
5. Affiche la page de participation
6. Étudiant peut créer/rejoindre une équipe
```

### Scénario 2: Événement Démarre (Transition Automatique)

```
1. Commande cron exécute: app:update-evenement-workflow
2. Workflow applique transition: planifie → en_cours
3. workflowStatus = 'en_cours'
4. canAcceptParticipations() → false
5. Bouton "Participer" disparaît automatiquement
6. Message "Event In Progress" s'affiche
```

### Scénario 3: Étudiant Essaie d'Accéder Directement

```
1. Étudiant tape URL: /events/5/participate
2. Contrôleur vérifie: canAcceptParticipations() → false
3. Flash message: "This event is currently in progress..."
4. Redirection vers /events
5. Étudiant voit le message d'erreur
```

### Scénario 4: Événement Annulé Manuellement

```
1. Admin clique "❌ Annuler" dans le backoffice
2. Workflow applique transition: planifie → annule
3. workflowStatus = 'annule'
4. isCanceled = true
5. canAcceptParticipations() → false
6. Badge "Event Cancelled" s'affiche
7. Emails envoyés aux participants
```

---

## 🧪 Tests à Effectuer

### Test 1: Événement Planifié

1. Créer un événement avec date future
2. Vérifier que `workflowStatus = 'planifie'`
3. Aller sur `/events`
4. **Résultat attendu**: Bouton "Participate" visible

### Test 2: Événement Démarre

1. Créer un événement avec date début dans le passé
2. Exécuter: `php bin/console app:update-evenement-workflow`
3. Vérifier que `workflowStatus = 'en_cours'`
4. Rafraîchir `/events`
5. **Résultat attendu**: 
   - Bouton "Participate" caché
   - Message "Event In Progress" affiché

### Test 3: Événement Terminé

1. Créer un événement avec date fin dans le passé
2. Exécuter: `php bin/console app:update-evenement-workflow`
3. Vérifier que `workflowStatus = 'termine'`
4. Rafraîchir `/events`
5. **Résultat attendu**:
   - Bouton "Participate" caché
   - Message "Event Completed" affiché

### Test 4: Événement Annulé

1. Aller sur `/backoffice/evenement`
2. Cliquer sur "❌ Annuler" pour un événement
3. Vérifier que `workflowStatus = 'annule'`
4. Aller sur `/events`
5. **Résultat attendu**:
   - Bouton "Participate" caché
   - Badge "Event Cancelled" affiché

### Test 5: Accès Direct Bloqué

1. Noter l'ID d'un événement en cours (ex: 5)
2. Taper URL: `http://localhost:8000/events/5/participate`
3. **Résultat attendu**:
   - Redirection vers `/events`
   - Flash message: "This event is currently in progress..."

---

## 📊 Comparaison Avant/Après

### Avant (Sans Workflow)

| Aspect | Comportement |
|--------|-------------|
| **Événement en cours** | ❌ Bouton "Participer" toujours visible |
| **Événement terminé** | ❌ Bouton "Participer" toujours visible |
| **Accès direct URL** | ❌ Aucune vérification |
| **Logique métier** | ❌ Dispersée dans plusieurs fichiers |
| **Maintenance** | ❌ Difficile à modifier |

### Après (Avec Workflow)

| Aspect | Comportement |
|--------|-------------|
| **Événement en cours** | ✅ Bouton caché + Message "In Progress" |
| **Événement terminé** | ✅ Bouton caché + Message "Completed" |
| **Accès direct URL** | ✅ Bloqué avec message d'erreur |
| **Logique métier** | ✅ Centralisée dans l'entité |
| **Maintenance** | ✅ Facile à modifier |

---

## 🎯 Avantages de Cette Approche

### 1. Sécurité

✅ **Impossible de s'inscrire à un événement terminé**
- Même en tapant l'URL directement
- Vérification côté serveur (pas seulement UI)

### 2. Expérience Utilisateur

✅ **Messages clairs et informatifs**
- "Event In Progress" → L'étudiant comprend pourquoi il ne peut pas s'inscrire
- "Event Completed" → L'étudiant sait que c'est trop tard
- "Event Cancelled" → L'étudiant sait que l'événement est annulé

### 3. Cohérence

✅ **Une seule source de vérité: le workflow**
- `workflowStatus` détermine tout
- Pas de logique dupliquée
- Facile à maintenir

### 4. Automatisation

✅ **Transitions automatiques**
- Événement démarre → Participations fermées automatiquement
- Événement termine → Participations fermées automatiquement
- Pas d'intervention manuelle nécessaire

### 5. Extensibilité

✅ **Facile d'ajouter de nouvelles règles**
- Exemple: Fermer les participations 24h avant l'événement
- Exemple: Rouvrir les participations si un événement est reporté
- Tout se fait via le workflow

---

## 🔮 Évolutions Futures Possibles

### 1. Fermeture Anticipée des Participations

```php
public function areParticipationsOpen(): bool
{
    // Fermer 24h avant l'événement
    $deadline = (clone $this->dateDebut)->modify('-24 hours');
    $now = new \DateTime();
    
    return $this->workflowStatus === 'planifie' 
        && !$this->isCanceled
        && $now < $deadline;
}
```

### 2. Réouverture des Participations

Si un événement est reporté, on pourrait ajouter une transition:
```yaml
transitions:
    reporter:
        from: [en_cours, termine]
        to: planifie
```

### 3. Liste d'Attente

Ajouter un état `liste_attente` pour les événements complets:
```yaml
places:
    - planifie
    - en_cours
    - termine
    - annule
    - liste_attente  # Nouveau
```

---

## 📝 Résumé

### Fichiers Modifiés

1. **src/Entity/Evenement.php**
   - Ajout méthode `areParticipationsOpen()`
   - Ajout méthode `canAcceptParticipations()`

2. **src/Controller/FrontofficeEvenementController.php**
   - Vérification dans `participate()`
   - Messages d'erreur personnalisés

3. **templates/frontoffice/evenement/index.html.twig**
   - Affichage conditionnel du bouton "Participer"
   - Messages pour événements en cours/terminés

### Règle Principale

**Les participations sont ouvertes UNIQUEMENT si:**
```php
workflowStatus === 'planifie' && !isCanceled
```

### Bénéfices

✅ Sécurité renforcée  
✅ Expérience utilisateur améliorée  
✅ Logique métier centralisée  
✅ Automatisation complète  
✅ Facile à maintenir et étendre  

---

**Date**: 22 Février 2026  
**Statut**: ✅ Implémenté et Documenté
