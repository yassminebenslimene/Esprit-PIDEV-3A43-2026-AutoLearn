# 📘 Guide Complet - Symfony Workflow Component pour le Module Événements

## 📋 Table des Matières
1. [Introduction au Workflow Component](#introduction)
2. [Pourquoi utiliser le Workflow Component?](#pourquoi)
3. [Architecture et Concepts](#architecture)
4. [Configuration Détaillée](#configuration)
5. [Implémentation dans le Module Événements](#implementation)
6. [Mécanisme de Fonctionnement](#mecanisme)
7. [Traçabilité et Audit Trail](#tracabilite)
8. [Envoi d'Emails Automatiques](#emails)
9. [Transitions Automatiques vs Manuelles](#transitions)
10. [Avantages et Valeur Ajoutée](#avantages)

---

## 🎯 Introduction au Workflow Component {#introduction}

Le **Symfony Workflow Component** est un bundle officiel Symfony qui permet de gérer des **machines à états** (state machines) et des **workflows** pour vos entités.

### Qu'est-ce qu'une State Machine?

Une state machine (machine à états) est un modèle qui définit:
- **Places (États)**: Les différents états possibles d'une entité
- **Transitions**: Les actions qui permettent de passer d'un état à un autre
- **Guards**: Les conditions qui autorisent ou bloquent une transition

### Installation

```bash
composer require symfony/workflow
```

---

## 🤔 Pourquoi utiliser le Workflow Component? {#pourquoi}

### Sans Workflow Component

Avant l'intégration du workflow, la gestion des statuts d'événements était **manuelle et dispersée**:

```php
// Dans le contrôleur
if ($evenement->getIsCanceled()) {
    $evenement->setStatus(StatutEvenement::ANNULE);
}

// Dans l'entité
public function updateStatus(): void {
    $now = new \DateTime();
    if ($now > $this->getDateFin()) {
        $this->setStatus(StatutEvenement::PASSE);
    }
    // ... logique dispersée
}
```

**Problèmes:**
- ❌ Logique métier dispersée dans plusieurs fichiers
- ❌ Pas d'historique des changements d'état
- ❌ Difficile de savoir qui a fait quoi et quand
- ❌ Pas de validation des transitions (on peut passer de n'importe quel état à n'importe quel état)
- ❌ Pas d'événements déclenchés automatiquement (emails, notifications)
- ❌ Code difficile à maintenir et à tester

### Avec Workflow Component

Avec le workflow, tout est **centralisé, tracé et automatisé**:

```php
// Appliquer une transition
$workflow->apply($evenement, 'demarrer');

// Le workflow s'occupe de:
// ✅ Vérifier que la transition est autorisée
// ✅ Changer l'état de l'entité
// ✅ Logger qui a fait la transition et quand
// ✅ Déclencher des événements (envoi d'emails, notifications)
// ✅ Valider les conditions (guards)
```

**Avantages:**
- ✅ Logique métier centralisée dans un fichier de configuration
- ✅ Historique complet des transitions (audit trail)
- ✅ Validation automatique des transitions
- ✅ Événements déclenchés automatiquement
- ✅ Code propre, maintenable et testable
- ✅ Visualisation graphique possible du workflow

---

## 🏗️ Architecture et Concepts {#architecture}

### Les 4 Composants Principaux

#### 1. **Places (États)**

Les places représentent les différents états possibles d'un événement:

```yaml
places:
    - planifie      # Événement créé, pas encore commencé
    - en_cours      # Événement en cours d'exécution
    - termine       # Événement terminé
    - annule        # Événement annulé
```

#### 2. **Transitions**

Les transitions définissent comment passer d'un état à un autre:

```yaml
transitions:
    demarrer:       # Nom de la transition
        from: planifie
        to: en_cours
    terminer:
        from: en_cours
        to: termine
    annuler:
        from: [planifie, en_cours]  # Peut annuler depuis plusieurs états
        to: annule
```

#### 3. **Marking Store**

Le marking store définit où stocker l'état actuel dans l'entité:

```yaml
marking_store:
    type: 'method'
    property: 'workflowStatus'  # Nom de la propriété dans l'entité
```

Cela signifie que le workflow utilisera les méthodes `getWorkflowStatus()` et `setWorkflowStatus()` de l'entité.

#### 4. **Event Subscribers**

Les event subscribers écoutent les événements du workflow et exécutent des actions:

```php
public static function getSubscribedEvents(): array {
    return [
        'workflow.evenement_publishing.entered.en_cours' => 'onEnCours',
        'workflow.evenement_publishing.entered.annule' => 'onAnnule',
    ];
}
```

---

## ⚙️ Configuration Détaillée {#configuration}

### Fichier: `config/packages/workflow.yaml`

```yaml
framework:
    workflows:
        evenement_publishing:           # Nom du workflow
            type: 'state_machine'       # Type: state_machine ou workflow
            audit_trail:
                enabled: true           # Active l'audit trail (historique)
            marking_store:
                type: 'method'
                property: 'workflowStatus'
            supports:
                - App\Entity\Evenement  # Entité supportée
            initial_marking: planifie   # État initial par défaut
            places:
                - planifie
                - en_cours
                - termine
                - annule
            transitions:
                demarrer:
                    from: planifie
                    to: en_cours
                    metadata:           # Métadonnées optionnelles
                        title: "Démarrer l'événement"
                        color: 'success'
                        icon: 'play'
                terminer:
                    from: en_cours
                    to: termine
                    metadata:
                        title: "Terminer l'événement"
                        color: 'info'
                        icon: 'check'
                annuler:
                    from: [planifie, en_cours]
                    to: annule
                    metadata:
                        title: "Annuler l'événement"
                        color: 'danger'
                        icon: 'times'
```

### Différence entre `state_machine` et `workflow`

- **state_machine**: Une entité ne peut être que dans UN SEUL état à la fois
- **workflow**: Une entité peut être dans PLUSIEURS états en même temps

Pour les événements, on utilise `state_machine` car un événement ne peut être que dans un seul état (planifié OU en cours OU terminé OU annulé).

---

## 🔧 Implémentation dans le Module Événements {#implementation}

### 1. Modification de l'Entité `Evenement`

Ajout de la propriété `workflowStatus`:

```php
#[ORM\Column(type:"string", length: 50)]
private string $workflowStatus = 'planifie';

public function getWorkflowStatus(): string { 
    return $this->workflowStatus; 
}

public function setWorkflowStatus(string $workflowStatus): self { 
    $this->workflowStatus = $workflowStatus;
    $this->syncStatusFromWorkflow();
    return $this;
}

// Synchronise le status (enum) avec le workflowStatus (string)
private function syncStatusFromWorkflow(): void {
    match($this->workflowStatus) {
        'planifie' => $this->status = StatutEvenement::PLANIFIE,
        'en_cours' => $this->status = StatutEvenement::EN_COURS,
        'termine' => $this->status = StatutEvenement::PASSE,
        'annule' => $this->status = StatutEvenement::ANNULE,
        default => $this->status = StatutEvenement::PLANIFIE,
    };
}
```

**Pourquoi une nouvelle colonne `workflowStatus`?**

- Le workflow a besoin d'une propriété de type `string` pour stocker l'état actuel
- On garde l'enum `StatutEvenement` existant pour la compatibilité avec le reste du code
- La méthode `syncStatusFromWorkflow()` synchronise automatiquement les deux

### 2. Migration de Base de Données

```php
// migrations/Version20260222013402.php
public function up(Schema $schema): void {
    $this->addSql('ALTER TABLE evenement ADD workflow_status VARCHAR(50) NOT NULL DEFAULT \'planifie\'');
}
```

### 3. Injection du Workflow dans le Contrôleur

```php
public function __construct(
    private WorkflowInterface $evenementPublishingStateMachine
) {}
```

Le nom du service injecté est automatiquement généré: `{nom_workflow}StateMachine`

### 4. Utilisation dans le Contrôleur

```php
// Vérifier si une transition est possible
if ($this->evenementPublishingStateMachine->can($evenement, 'annuler')) {
    // Appliquer la transition
    $this->evenementPublishingStateMachine->apply($evenement, 'annuler');
    $entityManager->flush();
}
```

---

## 🔄 Mécanisme de Fonctionnement {#mecanisme}

### Cycle de Vie d'une Transition

Quand on appelle `$workflow->apply($evenement, 'demarrer')`, voici ce qui se passe:

```
1. GUARD EVENT
   ├─ Vérifier les conditions (guards)
   ├─ Si bloqué → Exception
   └─ Si autorisé → Continuer

2. LEAVE EVENT
   ├─ Événement déclenché: workflow.leave.{place}
   └─ Ex: workflow.leave.planifie

3. TRANSITION EVENT
   ├─ Événement déclenché: workflow.transition.{transition}
   ├─ Ex: workflow.transition.demarrer
   └─ Logger l'historique complet

4. ENTER EVENT
   ├─ Événement déclenché: workflow.enter.{place}
   ├─ Ex: workflow.enter.en_cours
   └─ Changer l'état de l'entité

5. ENTERED EVENT
   ├─ Événement déclenché: workflow.entered.{place}
   ├─ Ex: workflow.entered.en_cours
   └─ Exécuter les actions (emails, notifications)

6. COMPLETED EVENT
   ├─ Événement déclenché: workflow.completed
   └─ Transition terminée avec succès
```

### Exemple Concret

```php
// État initial: planifie
$evenement->getWorkflowStatus(); // 'planifie'

// Appliquer la transition
$workflow->apply($evenement, 'demarrer');

// État final: en_cours
$evenement->getWorkflowStatus(); // 'en_cours'

// Événements déclenchés automatiquement:
// 1. Guard vérifie que dateDebut <= now
// 2. Leave planifie
// 3. Transition demarrer (logger l'historique)
// 4. Enter en_cours
// 5. Entered en_cours (envoyer emails aux participants)
// 6. Completed
```

---

## 📊 Traçabilité et Audit Trail {#tracabilite}

### Configuration de l'Audit Trail

```yaml
audit_trail:
    enabled: true
```

### Qu'est-ce qui est enregistré?

Le workflow enregistre **TOUT** dans les logs (`var/log/dev.log`):

1. **Qui** a fait la transition?
   - Utilisateur connecté (admin)
   - Ou SYSTEM (si transition automatique)

2. **Quand?**
   - Timestamp exact de la transition

3. **Quelle transition?**
   - Nom de la transition (demarrer, terminer, annuler)

4. **De quel état vers quel état?**
   - État de départ (from)
   - État d'arrivée (to)

### Exemple de Log

```json
{
    "message": "Transition d'événement",
    "context": {
        "evenement_id": 5,
        "evenement_titre": "Conférence IA",
        "transition": "demarrer",
        "from": ["planifie"],
        "to": ["en_cours"],
        "user": "admin@autolearn.com",
        "timestamp": "2026-02-22 14:30:00",
        "workflow": "evenement_publishing"
    }
}
```

### Implémentation dans l'EventSubscriber

```php
public function onTransition(Event $event): void {
    $evenement = $event->getSubject();
    $transition = $event->getTransition();
    
    // Récupérer l'utilisateur actuel
    $user = $this->security?->getUser();
    $username = $user ? $user->getUserIdentifier() : 'SYSTEM';
    
    // Logger l'historique complet
    $this->logger->info('Transition d\'événement', [
        'evenement_id' => $evenement->getId(),
        'evenement_titre' => $evenement->getTitre(),
        'transition' => $transition->getName(),
        'from' => $transition->getFroms(),
        'to' => $transition->getTos(),
        'user' => $username,
        'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
    ]);
}
```

---

## 📧 Envoi d'Emails Automatiques {#emails}

### Configuration dans l'EventSubscriber

```php
public static function getSubscribedEvents(): array {
    return [
        'workflow.evenement_publishing.entered.en_cours' => 'onEnCours',
        'workflow.evenement_publishing.entered.annule' => 'onAnnule',
    ];
}
```

### Quand un Événement Démarre

```php
public function onEnCours(Event $event): void {
    $evenement = $event->getSubject();
    
    // Logger
    $this->logger->info('🚀 Événement démarré', [
        'evenement_id' => $evenement->getId(),
        'titre' => $evenement->getTitre(),
    ]);
    
    // Envoyer emails
    $this->sendEmailsToParticipants($evenement, 'started');
}
```

### Quand un Événement est Annulé

```php
public function onAnnule(Event $event): void {
    $evenement = $event->getSubject();
    
    // Logger
    $this->logger->warning('❌ Événement annulé', [
        'evenement_id' => $evenement->getId(),
        'titre' => $evenement->getTitre(),
    ]);
    
    // Envoyer emails
    $this->sendEmailsToParticipants($evenement, 'cancelled');
}
```

### Envoi aux Participants

La méthode `sendEmailsToParticipants()` envoie un email à **TOUS les membres de TOUTES les équipes participantes** (avec participations acceptées):

```php
private function sendEmailsToParticipants(Evenement $evenement, string $type): void {
    foreach ($evenement->getParticipations() as $participation) {
        // Vérifier que la participation est acceptée
        if ($participation->getStatut()->value !== 'Accepté') {
            continue;
        }
        
        $equipe = $participation->getEquipe();
        
        // Envoyer un email à chaque étudiant de l'équipe
        foreach ($equipe->getEtudiants() as $etudiant) {
            if ($type === 'started') {
                $this->emailService->sendEventStarted(
                    $etudiant->getEmail(),
                    $etudiant->getPrenom() . ' ' . $etudiant->getNom(),
                    $equipe->getNom(),
                    $evenement->getTitre(),
                    $evenement->getDateDebut(),
                    $evenement->getLieu()
                );
            } elseif ($type === 'cancelled') {
                $this->emailService->sendEventCancellation(
                    $etudiant->getEmail(),
                    $etudiant->getPrenom() . ' ' . $etudiant->getNom(),
                    $equipe->getNom(),
                    $evenement->getTitre(),
                    $evenement->getDateDebut(),
                    $evenement->getLieu()
                );
            }
        }
    }
}
```

---

## 🔀 Transitions Automatiques vs Manuelles {#transitions}

### Approche Hybride

Le module utilise une **approche hybride** combinant transitions automatiques et manuelles:

#### 1. Transitions Automatiques (Command + Cron)

**Fichier**: `src/Command/UpdateEvenementWorkflowCommand.php`

```php
php bin/console app:update-evenement-workflow
```

Cette commande vérifie tous les événements et applique automatiquement les transitions:

- `planifie → en_cours` quand `dateDebut <= now`
- `en_cours → termine` quand `dateFin < now`

**Configuration Cron** (optionnelle):

```bash
# Exécuter toutes les 5 minutes
*/5 * * * * cd /path/to/project && php bin/console app:update-evenement-workflow
```

#### 2. Transition Manuelle (Bouton Backoffice)

**Route**: `/backoffice/evenement/{id}/annuler`

L'administrateur peut annuler manuellement un événement via un bouton dans le backoffice:

```php
#[Route('/{id}/annuler', name: 'backoffice_evenement_annuler', methods: ['POST'])]
public function annuler(Evenement $evenement, EntityManagerInterface $entityManager): Response {
    if (!$this->evenementPublishingStateMachine->can($evenement, 'annuler')) {
        $this->addFlash('error', 'Impossible d\'annuler cet événement');
        return $this->redirectToRoute('backoffice_evenements');
    }
    
    $this->evenementPublishingStateMachine->apply($evenement, 'annuler');
    $evenement->setIsCanceled(true);
    $entityManager->flush();
    
    $this->addFlash('success', 'Événement annulé avec succès');
    return $this->redirectToRoute('backoffice_evenements');
}
```

### Guards (Conditions de Validation)

Les guards empêchent les transitions invalides:

```php
public function onGuard(GuardEvent $event): void {
    $evenement = $event->getSubject();
    $transition = $event->getTransition()->getName();
    
    // Empêcher de démarrer si la date n'est pas arrivée
    if ($transition === 'demarrer') {
        $now = new \DateTime();
        if ($evenement->getDateDebut() > $now) {
            $event->setBlocked(true, 'La date de début n\'est pas encore arrivée');
        }
    }
    
    // Empêcher de terminer si la date de fin n'est pas passée
    if ($transition === 'terminer') {
        $now = new \DateTime();
        if ($evenement->getDateFin() >= $now) {
            $event->setBlocked(true, 'La date de fin n\'est pas encore passée');
        }
    }
}
```

---

## ✨ Avantages et Valeur Ajoutée {#avantages}

### Comparaison Avant/Après

| Aspect | Sans Workflow | Avec Workflow |
|--------|--------------|---------------|
| **Gestion des états** | Manuelle, dispersée | Centralisée, automatisée |
| **Validation** | Aucune | Guards automatiques |
| **Historique** | Aucun | Audit trail complet |
| **Traçabilité** | Impossible | Qui, quand, quoi |
| **Événements** | Manuels | Automatiques |
| **Emails** | Code dispersé | EventSubscriber |
| **Maintenabilité** | Difficile | Facile |
| **Testabilité** | Complexe | Simple |
| **Visualisation** | Impossible | Graphique possible |

### Valeur Ajoutée pour le Module Événements

1. **Professionnalisme**
   - Architecture propre et maintenable
   - Respect des best practices Symfony
   - Code facilement testable

2. **Traçabilité Complète**
   - Historique de toutes les transitions
   - Identification de qui a fait quoi
   - Timestamps précis

3. **Automatisation**
   - Transitions automatiques basées sur les dates
   - Envoi d'emails automatique
   - Génération de certificats (future fonctionnalité)

4. **Sécurité**
   - Validation des transitions via guards
   - Impossible de passer dans un état invalide
   - Conditions métier respectées

5. **Extensibilité**
   - Facile d'ajouter de nouveaux états
   - Facile d'ajouter de nouvelles transitions
   - Facile d'ajouter de nouvelles actions

6. **Monitoring**
   - Logs détaillés de toutes les transitions
   - Statistiques possibles sur les événements
   - Détection d'anomalies

### Cas d'Usage Futurs

Le workflow permet facilement d'ajouter:

- **Validation par un responsable** avant de démarrer un événement
- **Rappels automatiques** 3 jours avant l'événement
- **Génération de certificats** automatique à la fin
- **Archivage automatique** des événements terminés
- **Notifications push** aux participants
- **Intégration avec des calendriers externes** (Google Calendar, Outlook)

---

## 📝 Résumé

Le **Symfony Workflow Component** transforme la gestion des événements d'un système manuel et dispersé en un système **professionnel, automatisé et tracé**.

### Points Clés

✅ **Centralisation**: Toute la logique métier dans un fichier de configuration
✅ **Traçabilité**: Historique complet de qui a fait quoi et quand
✅ **Automatisation**: Transitions automatiques + envoi d'emails
✅ **Validation**: Guards pour empêcher les transitions invalides
✅ **Extensibilité**: Facile d'ajouter de nouvelles fonctionnalités
✅ **Professionnalisme**: Architecture propre et maintenable

### Fichiers Modifiés/Créés

1. `config/packages/workflow.yaml` - Configuration du workflow
2. `src/Entity/Evenement.php` - Ajout de workflowStatus
3. `migrations/Version20260222013402.php` - Migration BDD
4. `src/EventSubscriber/EvenementWorkflowSubscriber.php` - Écoute des événements
5. `src/Command/UpdateEvenementWorkflowCommand.php` - Transitions automatiques
6. `src/Controller/EvenementController.php` - Route d'annulation manuelle
7. `src/Service/EmailService.php` - Méthodes sendEventStarted() et sendEventCancellation()
8. `templates/emails/event_started.html.twig` - Template email démarrage
9. `templates/emails/event_cancelled.html.twig` - Template email annulation

---

**Auteur**: Kiro AI Assistant  
**Date**: 22 Février 2026  
**Version**: 1.0
