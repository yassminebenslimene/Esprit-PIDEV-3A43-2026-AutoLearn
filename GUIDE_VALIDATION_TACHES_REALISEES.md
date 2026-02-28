# 📚 GUIDE DE VALIDATION - Tâches Réalisées

## 🎯 Document pour Présentation à la Professeure

**Date**: 23 Février 2026  
**Étudiante**: Amira NEFZI  
**Module**: Gestion des Événements

---

## 📋 TABLE DES MATIÈRES

1. [US-5.1: Créer un événement](#us-51-créer-un-événement)
2. [US-5.2: Modifier un événement](#us-52-modifier-un-événement)
3. [US-5.3: Supprimer un événement](#us-53-supprimer-un-événement)
4. [US-5.4: Workflow automatique](#us-54-workflow-automatique)
5. [US-5.5: Annuler un événement](#us-55-annuler-un-événement)
6. [Tâches Cron et Configuration Système](#tâches-cron-et-configuration-système)

---

## US-5.1: Créer un événement

### ✅ T-5.1.1: Créer l'entité Evenement

**Fichier**: `src/Entity/Evenement.php`  
**Lignes**: 1-300 (fichier complet)

**Explication**:
- L'entité `Evenement` est la classe principale qui représente un événement dans la base de données
- Elle contient tous les attributs nécessaires: titre, description, dateDebut, dateFin, lieu, type, status, nbMax, etc.
- Utilise Doctrine ORM pour mapper la classe PHP vers une table SQL

**Attributs principaux**:
```
- id (int): Identifiant unique auto-incrémenté
- titre (string, 255): Nom de l'événement
- description (text): Description détaillée
- dateDebut (DateTime): Date et heure de début
- dateFin (DateTime): Date et heure de fin
- lieu (string, 255): Lieu de l'événement
- type (TypeEvenement enum): Workshop, Hackathon, Conference, etc.
- status (StatutEvenement enum): Planifié, En cours, Passé, Annulé
- nbMax (int): Nombre maximum d'équipes
- isCanceled (bool): Indicateur d'annulation
- workflowStatus (string): État du workflow
```

**Relations**:
- OneToMany avec Participation: Un événement peut avoir plusieurs participations
- Cascade remove: Si l'événement est supprimé, toutes les participations sont supprimées

---

### ✅ T-5.1.2: Créer les enums

**Fichier 1**: `src/Enum/TypeEvenement.php`  
**Lignes**: 1-20

**Explication**:
- Enum PHP 8.1+ qui définit les types d'événements possibles
- Valeurs: Workshop, Conference, Hackathon, Seminar, Meetup, Training
- Utilisé dans l'entité Evenement pour garantir des valeurs valides

**Fichier 2**: `src/Enum/StatutEvenement.php`  
**Lignes**: 1-20

**Explication**:
- Enum qui définit les statuts d'événement
- Valeurs: Planifié, En cours, Passé, Annulé
- Mis à jour automatiquement par la méthode `updateStatus()` dans l'entité

---

### ✅ T-5.1.3: Générer et exécuter la migration

**Fichier**: `migrations/Version20260220211749.php`  
**Lignes**: 1-50

**Explication**:
- Migration Doctrine qui crée la table `evenement` dans la base de données
- Commande utilisée: `php bin/console make:migration`
- Exécution: `php bin/console doctrine:migrations:migrate`
- Crée toutes les colonnes avec les bons types SQL

**Structure SQL créée**:
```sql
CREATE TABLE evenement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    lieu VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    nb_max INT NOT NULL,
    is_canceled TINYINT(1) DEFAULT 0,
    workflow_status VARCHAR(50) DEFAULT 'planifie'
);
```

---

### ✅ T-5.1.4: Créer le formulaire de création

**Fichier**: `src/Form/EvenementType.php`  
**Lignes**: 1-80

**Explication**:
- Formulaire Symfony qui génère automatiquement les champs HTML
- Utilise FormBuilder pour définir chaque champ
- Configure les widgets (input, textarea, select, datetime)
- Ajoute les labels et placeholders en français

**Champs du formulaire**:
```
- titre: TextType
- description: TextareaType
- dateDebut: DateTimeType
- dateFin: DateTimeType
- lieu: TextType
- type: EnumType (liste déroulante)
- nbMax: IntegerType
```

---

### ✅ T-5.1.5: Créer le contrôleur avec action new

**Fichier**: `src/Controller/EvenementController.php`  
**Lignes**: Méthode `new()` (environ lignes 50-80)

**Explication**:
- Contrôleur qui gère toutes les actions liées aux événements
- Méthode `new()`: Affiche le formulaire et traite la soumission
- Route: `/backoffice/evenement/new`
- Méthode HTTP: GET (affichage) et POST (soumission)

**Fonctionnement**:
1. Crée une nouvelle instance d'Evenement
2. Crée le formulaire avec `createForm()`
3. Traite la requête avec `handleRequest()`
4. Si formulaire valide: sauvegarde en base avec EntityManager
5. Redirige vers la liste avec message de succès
6. Sinon: réaffiche le formulaire avec erreurs

---

### ✅ T-5.1.6: Créer le template de création

**Fichier**: `templates/backoffice/evenement/new.html.twig`  
**Lignes**: 1-100

**Explication**:
- Template Twig qui affiche le formulaire HTML
- Hérite de `base.html.twig` pour la structure commune
- Utilise `form_start()`, `form_widget()`, `form_end()` pour générer le HTML
- Design moderne avec Bootstrap et styles personnalisés

**Structure**:
```twig
{% extends 'backoffice/base.html.twig' %}

{% block title %}Créer un événement{% endblock %}

{% block body %}
    <h1>Créer un nouvel événement</h1>
    
    {{ form_start(form) }}
        {{ form_widget(form) }}
        <button type="submit">Créer</button>
    {{ form_end(form) }}
{% endblock %}
```

---

### ✅ T-5.1.7: Ajouter les validations

**Fichier**: `src/Entity/Evenement.php`  
**Lignes**: Annotations au-dessus des propriétés

**Explication**:
- Utilise les contraintes Symfony Validator
- Validations côté serveur avant sauvegarde
- Messages d'erreur personnalisés en français

**Contraintes appliquées**:
```php
#[Assert\NotBlank(message: "Le titre est obligatoire")]
#[Assert\Length(max: 255)]
private string $titre;

#[Assert\NotBlank(message: "La description est obligatoire")]
private string $description;

#[Assert\NotBlank]
#[Assert\GreaterThan('today', message: "La date doit être future")]
private \DateTime $dateDebut;

#[Assert\GreaterThan(propertyPath: 'dateDebut')]
private \DateTime $dateFin;

#[Assert\Positive]
#[Assert\Range(min: 1, max: 100)]
private int $nbMax;
```

---

### ✅ T-5.1.TEST: Tests pour US-5.1

**Fichier**: Tests manuels effectués

**Tests réalisés**:
1. ✅ Créer un événement avec données valides → Succès
2. ✅ Créer un événement sans titre → Erreur affichée
3. ✅ Créer un événement avec date fin < date début → Erreur
4. ✅ Créer un événement avec nbMax = 0 → Erreur
5. ✅ Vérifier que l'événement apparaît dans la liste
6. ✅ Vérifier que l'événement est bien en base de données

---

## US-5.2: Modifier un événement

### ✅ T-5.2.1: Créer l'action edit

**Fichier**: `src/Controller/EvenementController.php`  
**Lignes**: Méthode `edit()` (environ lignes 100-130)

**Explication**:
- Méthode qui permet de modifier un événement existant
- Route: `/backoffice/evenement/{id}/edit`
- Paramètre: ID de l'événement à modifier
- Réutilise le même formulaire que la création

**Fonctionnement**:
1. Récupère l'événement depuis la base via son ID
2. Si événement non trouvé: erreur 404
3. Crée le formulaire pré-rempli avec les données existantes
4. Traite la soumission
5. Si valide: met à jour en base
6. Redirige vers la liste

---

### ✅ T-5.2.2: Créer le template de modification

**Fichier**: `templates/backoffice/evenement/edit.html.twig`  
**Lignes**: 1-120

**Explication**:
- Template similaire à `new.html.twig`
- Affiche "Modifier l'événement" au lieu de "Créer"
- Formulaire pré-rempli avec les valeurs actuelles
- Bouton "Mettre à jour" au lieu de "Créer"

---

### ✅ T-5.2.3: Ajouter vérifications de sécurité

**Fichier**: `src/Controller/EvenementController.php`  
**Lignes**: Dans la méthode `edit()`

**Explication**:
- Vérifie que l'utilisateur est authentifié
- Vérifie que l'utilisateur a le rôle ADMIN
- Utilise `#[IsGranted('ROLE_ADMIN')]` au-dessus de la méthode
- Empêche les modifications non autorisées

---

### ✅ T-5.2.TEST: Tests pour US-5.2

**Tests réalisés**:
1. ✅ Modifier le titre d'un événement → Succès
2. ✅ Modifier les dates → Succès
3. ✅ Modifier avec données invalides → Erreurs affichées
4. ✅ Accéder à /edit sans être admin → Accès refusé
5. ✅ Modifier un événement inexistant → 404

---

## US-5.3: Supprimer un événement

### ✅ T-5.3.1: Créer l'action delete

**Fichier**: `src/Controller/EvenementController.php`  
**Lignes**: Méthode `delete()` (environ lignes 150-170)

**Explication**:
- Méthode qui supprime un événement
- Route: `/backoffice/evenement/{id}/delete`
- Méthode HTTP: POST (pour sécurité CSRF)
- Supprime l'événement et toutes ses relations en cascade

**Fonctionnement**:
1. Récupère l'événement
2. Vérifie le token CSRF pour sécurité
3. Supprime avec `$entityManager->remove()`
4. Flush pour appliquer en base
5. Message de succès
6. Redirige vers la liste

---

### ✅ T-5.3.2: Confirmation JavaScript

**Fichier**: `templates/backoffice/evenement/index.html.twig`  
**Lignes**: Section JavaScript en bas du fichier

**Explication**:
- Ajoute une confirmation avant suppression
- Utilise `confirm()` JavaScript
- Empêche la suppression accidentelle

**Code JavaScript**:
```javascript
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
            e.preventDefault();
        }
    });
});
```

---

### ✅ T-5.3.3: Suppression en cascade

**Fichier**: `src/Entity/Evenement.php`  
**Lignes**: Annotation sur la relation `participations`

**Explication**:
- Configure Doctrine pour supprimer automatiquement les participations
- Utilise `cascade: ['remove']` dans l'annotation OneToMany
- Quand un événement est supprimé, toutes ses participations le sont aussi
- Maintient l'intégrité référentielle

**Annotation**:
```php
#[ORM\OneToMany(
    mappedBy: 'evenement',
    targetEntity: Participation::class,
    cascade: ['remove'],
    orphanRemoval: true
)]
private Collection $participations;
```

---

### ✅ T-5.3.TEST: Tests pour US-5.3

**Tests réalisés**:
1. ✅ Supprimer un événement sans participations → Succès
2. ✅ Supprimer un événement avec participations → Succès + participations supprimées
3. ✅ Annuler la confirmation → Événement non supprimé
4. ✅ Vérifier que l'événement n'est plus en base
5. ✅ Vérifier que les participations sont supprimées

---

## US-5.4: Workflow automatique

### ✅ T-5.4.1: Installer symfony/workflow

**Fichier**: `composer.json`  
**Lignes**: Section `require`

**Explication**:
- Commande exécutée: `composer require symfony/workflow`
- Ajoute le composant Workflow de Symfony
- Permet de gérer les états et transitions automatiquement

---

### ✅ T-5.4.2: Configurer le workflow

**Fichier**: `config/packages/workflow.yaml`  
**Lignes**: 1-50 (fichier complet)

**Explication**:
- Définit le workflow `evenement_publishing`
- Type: `state_machine` (un seul état à la fois)
- Places (états): planifie, en_cours, termine, annule
- Transitions: demarrer, terminer, annuler

**Configuration**:
```yaml
framework:
    workflows:
        evenement_publishing:
            type: 'state_machine'
            marking_store:
                type: 'method'
                property: 'workflowStatus'
            supports:
                - App\Entity\Evenement
            initial_marking: planifie
            places:
                - planifie
                - en_cours
                - termine
                - annule
            transitions:
                demarrer:
                    from: planifie
                    to: en_cours
                terminer:
                    from: en_cours
                    to: termine
                annuler:
                    from: [planifie, en_cours]
                    to: annule
```

---

### ✅ T-5.4.3: Ajouter workflowStatus

**Fichier**: `src/Entity/Evenement.php`  
**Lignes**: Propriété `workflowStatus` (environ ligne 80)

**Explication**:
- Propriété qui stocke l'état actuel du workflow
- Type: string (50 caractères max)
- Valeur par défaut: 'planifie'
- Utilisé par le workflow pour savoir dans quel état est l'événement

---

### ✅ T-5.4.4: Créer la commande de mise à jour

**Fichier**: `src/Command/UpdateEvenementWorkflowCommand.php`  
**Lignes**: 1-150 (fichier complet)

**Explication**:
- Commande Symfony qui s'exécute automatiquement (cron)
- Parcourt tous les événements
- Applique les transitions automatiques selon les dates
- Commande: `php bin/console app:update-evenement-workflow`

**Logique**:
```
Pour chaque événement:
    Si workflowStatus = 'planifie' ET dateDebut <= maintenant:
        → Appliquer transition 'demarrer'
        → workflowStatus devient 'en_cours'
    
    Si workflowStatus = 'en_cours' ET dateFin < maintenant:
        → Appliquer transition 'terminer'
        → workflowStatus devient 'termine'
```

---

### ✅ T-5.4.5: Créer EventSubscriber

**Fichier**: `src/EventSubscriber/EvenementWorkflowSubscriber.php`  
**Lignes**: 1-250 (fichier complet)

**Explication**:
- Écoute tous les événements du workflow
- Exécute des actions lors des transitions
- Log toutes les transitions pour traçabilité
- Envoie des emails automatiquement

**Événements écoutés**:
```php
'workflow.evenement_publishing.transition' => 'onTransition'
'workflow.evenement_publishing.entered.en_cours' => 'onEnCours'
'workflow.evenement_publishing.entered.termine' => 'onTermine'
'workflow.evenement_publishing.entered.annule' => 'onAnnule'
```

**Actions automatiques**:
- `onEnCours()`: Envoie email de démarrage à tous les participants
- `onTermine()`: Déclenche génération des certificats
- `onAnnule()`: Envoie email d'annulation
- `onTransition()`: Log qui/quand/quoi pour audit

---

### ✅ T-5.4.TEST: Tests pour US-5.4

**Tests réalisés**:
1. ✅ Créer événement avec dateDebut = demain → workflowStatus = 'planifie'
2. ✅ Exécuter commande workflow → Pas de changement (date future)
3. ✅ Modifier dateDebut = maintenant → Exécuter commande → Passe à 'en_cours'
4. ✅ Vérifier emails envoyés aux participants
5. ✅ Modifier dateFin = hier → Exécuter commande → Passe à 'termine'
6. ✅ Vérifier logs dans var/log/dev.log

---

## Tâches Cron et Configuration Système

### 📌 US-5.20: Email de rappel 3 jours avant

#### ❌ T-5.20.3: Configurer tâche cron (NON RÉALISÉE)

**Où configurer**:
- **Sur serveur Linux**: Fichier crontab
- **Sur serveur Windows**: Planificateur de tâches
- **En développement**: Exécution manuelle

**Comment configurer sur Linux**:
```bash
# Ouvrir crontab
crontab -e

# Ajouter cette ligne (exécute tous les jours à 9h)
0 9 * * * cd /chemin/vers/projet && php bin/console app:send-event-reminders
```

**Comment configurer sur Windows**:
1. Ouvrir "Planificateur de tâches"
2. Créer une tâche de base
3. Déclencheur: Quotidien à 9h00
4. Action: Démarrer un programme
5. Programme: `C:\php\php.exe`
6. Arguments: `bin/console app:send-event-reminders`
7. Répertoire: `C:\chemin\vers\projet`

**Dans ton projet**:
- Commande créée: `src/Command/SendEventRemindersCommand.php`
- Pour tester: `php bin/console app:send-event-reminders`
- La commande cherche les événements dans 3 jours et envoie les emails

**Fonctionnement de la commande**:
```php
// Récupère événements dans 3 jours
$targetDate = (new \DateTime())->modify('+3 days');

$evenements = $repository->createQueryBuilder('e')
    ->where('DATE(e.dateDebut) = :targetDate')
    ->setParameter('targetDate', $targetDate->format('Y-m-d'))
    ->getQuery()
    ->getResult();

// Pour chaque événement, envoie email à tous les participants
foreach ($evenements as $evenement) {
    foreach ($evenement->getParticipations() as $participation) {
        // Envoyer email de rappel
    }
}
```

---

### 📌 US-5.34: Nettoyage participations refusées

#### Configuration cron similaire:
```bash
# Exécute tous les jours à minuit
0 0 * * * cd /chemin/vers/projet && php bin/console app:cleanup-cancelled-events
```

**Fichier**: `src/Command/CleanupCancelledEventsCommand.php`

---

## 🎯 Résumé pour la Validation

### Tâches Complétées: 182/188 (96.8%)

### Tâches Restantes (6):
1. ❌ US-5.8 T-5.8.3: Ajouter filtres par type et statut
2. ❌ US-5.14 T-5.14.2: Afficher message d'erreur approprié
3. ❌ US-5.20 T-5.20.3: Configurer tâche cron
4. ❌ US-5.21 T-5.21.3: Ajouter champ certificate_sent
5. ❌ US-5.30 T-5.30.1: Vérification statut participation
6. ❌ US-5.30 T-5.30.2: Masquer bouton si en cours/terminé

### Points Forts à Présenter:
1. ✅ Architecture MVC complète et bien structurée
2. ✅ Workflow automatique avec Symfony Workflow
3. ✅ Système de validation robuste
4. ✅ Envoi d'emails automatiques
5. ✅ Génération de PDF (badges, certificats)
6. ✅ Intégration IA (Mistral-7B)
7. ✅ Calendrier interactif (FullCalendar)
8. ✅ Système de feedback complet

### Démonstration Suggérée:
1. Montrer la création d'un événement
2. Montrer le workflow automatique
3. Montrer la participation d'une équipe
4. Montrer la validation automatique
5. Montrer l'email de confirmation
6. Montrer le dashboard AI

---

**Bonne chance pour ta validation! 🎓**
