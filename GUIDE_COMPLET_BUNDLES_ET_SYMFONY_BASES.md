# 📚 GUIDE COMPLET : BUNDLES & BASES SYMFONY

## 🎯 TABLE DES MATIÈRES

1. [Installation et Configuration Workflow Bundle](#1-workflow-bundle)
2. [Installation et Configuration Calendar Bundle](#2-calendar-bundle)
3. [Formulaires Symfony en Détail](#3-formulaires-symfony)
4. [EntityManager et Flush](#4-entitymanager-et-flush)
5. [QueryBuilder et DQL](#5-querybuilder-et-dql)
6. [Cycle de Vie d'une Requête](#6-cycle-de-vie-dune-requête)

---

## 1️⃣ WORKFLOW BUNDLE

### Étape 1 : Installation

**Commande** :
```bash
composer require symfony/workflow
```

**Ce qui se passe** :
1. Composer télécharge le bundle
2. Symfony Flex (recette automatique) :
   - Crée `config/packages/workflow.yaml`
   - Enregistre le bundle dans `config/bundles.php`

**Fichier créé automatiquement** : `config/bundles.php`
```php
return [
    // ... autres bundles
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
];
```

---

### Étape 2 : Configuration du Workflow

**Fichier** : `config/packages/workflow.yaml`

**Configuration complète avec explications** :

```yaml
framework:
    workflows:
        # Nom du workflow (tu peux en avoir plusieurs)
        evenement_publishing:
            
            # Type: 'workflow' ou 'state_machine'
            # - workflow: Peut être dans plusieurs états en même temps
            # - state_machine: Un seul état à la fois (notre cas)
            type: 'state_machine'
            
            # Audit trail: Enregistre l'historique des transitions
            audit_trail:
                enabled: true
            
            # Où stocker l'état actuel ?
            marking_store:
                type: 'method'              # Utilise une méthode de l'entité
                property: 'workflowStatus'  # Nom de la propriété dans l'entité
            
            # Quelle entité utilise ce workflow ?
            supports:
                - App\Entity\Evenement
            
            # État initial quand l'objet est créé
            initial_marking: planifie
            
            # Liste des états possibles
            places:
                - planifie    # État initial
                - en_cours    # Événement en cours
                - termine     # Événement terminé
                - annule      # Événement annulé
            
            # Transitions possibles entre les états
            transitions:
                # Transition "demarrer": planifie → en_cours
                demarrer:
                    from: planifie          # État de départ
                    to: en_cours            # État d'arrivée
                    metadata:               # Métadonnées optionnelles
                        title: "Démarrer l'événement"
                        description: "L'événement commence maintenant"
                        color: 'success'
                        icon: 'play'
                
                # Transition "terminer": en_cours → termine
                terminer:
                    from: en_cours
                    to: termine
                    metadata:
                        title: "Terminer l'événement"
                        description: "L'événement est maintenant terminé"
                        color: 'info'
                        icon: 'check'
                
                # Transition "annuler": planifie OU en_cours → annule
                annuler:
                    from: [planifie, en_cours]  # Plusieurs états de départ possibles
                    to: annule
                    metadata:
                        title: "Annuler l'événement"
                        description: "L'événement est annulé"
                        color: 'danger'
                        icon: 'times'
```

**Schéma visuel** :
```
┌──────────┐  demarrer   ┌──────────┐  terminer   ┌──────────┐
│ planifie │ ─────────> │ en_cours │ ─────────> │ termine  │
└────┬─────┘             └────┬─────┘             └──────────┘
     │                        │
     │      annuler           │ annuler
     └────────────────────────┴──────────────────> ┌──────────┐
                                                    │ annule   │
                                                    └──────────┘
```

---

### Étape 3 : Préparer l'Entité

**Fichier** : `src/Entity/Evenement.php`

**Ajouter la propriété pour stocker l'état** :

```php
#[ORM\Column(type:"string", length: 50)]
private string $workflowStatus = 'planifie';  // État initial

public function getWorkflowStatus(): string 
{ 
    return $this->workflowStatus; 
}

public function setWorkflowStatus(string $workflowStatus): self 
{ 
    $this->workflowStatus = $workflowStatus;
    return $this;
}
```

**Créer la migration** :
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

**SQL généré** :
```sql
ALTER TABLE evenement ADD workflow_status VARCHAR(50) DEFAULT 'planifie' NOT NULL;
```

---

### Étape 4 : Utiliser le Workflow dans le Contrôleur

**Fichier** : `src/Controller/EvenementController.php`

**Injection du Workflow** :

```php
use Symfony\Component\Workflow\WorkflowInterface;

class EvenementController extends AbstractController
{
    // Symfony injecte automatiquement le workflow
    public function __construct(
        private WorkflowInterface $evenementPublishingStateMachine
    ) {}
    
    // ...
}
```

**Pourquoi `$evenementPublishingStateMachine` ?**
- Symfony transforme le nom du workflow en camelCase
- `evenement_publishing` → `evenementPublishingStateMachine`
- Ajoute automatiquement "StateMachine" à la fin

---

**Exemple 1 : Vérifier si une transition est possible**

```php
#[Route('/{id}/edit', name: 'backoffice_evenement_edit')]
public function edit(Evenement $evenement): Response
{
    // Vérifier si on peut annuler l'événement
    $canAnnuler = $this->evenementPublishingStateMachine->can($evenement, 'annuler');
    
    return $this->render('backoffice/evenement/edit.html.twig', [
        'evenement' => $evenement,
        'can_annuler' => $canAnnuler,  // true ou false
    ]);
}
```

**Dans le template** :
```twig
{% if can_annuler %}
    <button type="submit" class="btn btn-danger">Annuler l'événement</button>
{% else %}
    <button disabled class="btn btn-secondary">Impossible d'annuler</button>
{% endif %}
```

---

**Exemple 2 : Appliquer une transition**

```php
#[Route('/{id}/annuler', name: 'backoffice_evenement_annuler', methods: ['POST'])]
public function annuler(
    Evenement $evenement, 
    EntityManagerInterface $entityManager
): Response {
    // 1. Vérifier si la transition est possible
    if (!$this->evenementPublishingStateMachine->can($evenement, 'annuler')) {
        $this->addFlash('error', 'Impossible d\'annuler cet événement');
        return $this->redirectToRoute('backoffice_evenements');
    }
    
    try {
        // 2. Appliquer la transition
        $this->evenementPublishingStateMachine->apply($evenement, 'annuler');
        
        // 3. Marquer comme annulé
        $evenement->setIsCanceled(true);
        
        // 4. Sauvegarder en base de données
        $entityManager->flush();
        
        // 5. Message de succès
        $this->addFlash('success', 'Événement annulé avec succès');
        
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur: ' . $e->getMessage());
    }
    
    return $this->redirectToRoute('backoffice_evenements');
}
```

**Ce qui se passe quand on appelle `apply()` :**

1. **Workflow vérifie** si la transition est possible
2. **Change l'état** : `$evenement->setWorkflowStatus('annule')`
3. **Déclenche des événements** :
   - `workflow.evenement_publishing.transition` (avant la transition)
   - `workflow.evenement_publishing.entered.annule` (après la transition)
4. **EventSubscriber écoute** ces événements et exécute du code automatiquement

---

### Étape 5 : Créer un EventSubscriber

**Fichier** : `src/EventSubscriber/EvenementWorkflowSubscriber.php`

**Code complet avec explications** :

```php
<?php

namespace App\EventSubscriber;

use App\Entity\Evenement;
use App\Service\EmailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;

class EvenementWorkflowSubscriber implements EventSubscriberInterface
{
    // Injection des dépendances
    public function __construct(
        private LoggerInterface $logger,
        private EmailService $emailService
    ) {}
    
    // Déclarer les événements qu'on écoute
    public static function getSubscribedEvents(): array
    {
        return [
            // Format: 'workflow.{nom_workflow}.{type_event}.{place}'
            'workflow.evenement_publishing.entered.en_cours' => 'onEnCours',
            'workflow.evenement_publishing.entered.termine' => 'onTermine',
            'workflow.evenement_publishing.entered.annule' => 'onAnnule',
        ];
    }
    
    // Appelé automatiquement quand l'événement démarre
    public function onEnCours(Event $event): void
    {
        // Récupérer l'objet Evenement
        $evenement = $event->getSubject();
        
        // Logger
        $this->logger->info('🚀 Événement démarré', [
            'evenement_id' => $evenement->getId(),
            'titre' => $evenement->getTitre(),
        ]);
        
        // Envoyer des emails automatiquement
        $this->sendEmailsToParticipants($evenement, 'started');
    }
    
    // Appelé automatiquement quand l'événement se termine
    public function onTermine(Event $event): void
    {
        $evenement = $event->getSubject();
        
        $this->logger->info('✅ Événement terminé', [
            'evenement_id' => $evenement->getId(),
        ]);
        
        // Envoyer automatiquement les certificats
        $this->sendCertificatesToParticipants($evenement);
    }
    
    // Appelé automatiquement quand l'événement est annulé
    public function onAnnule(Event $event): void
    {
        $evenement = $event->getSubject();
        
        $this->logger->warning('❌ Événement annulé', [
            'evenement_id' => $evenement->getId(),
        ]);
        
        // Envoyer email d'annulation
        $this->sendEmailsToParticipants($evenement, 'cancelled');
    }
    
    private function sendEmailsToParticipants(Evenement $evenement, string $type): void
    {
        // ... code d'envoi d'emails ...
    }
}
```

**Comment Symfony sait qu'il faut appeler ces méthodes ?**

1. Symfony scanne tous les `EventSubscriber`
2. Il lit `getSubscribedEvents()` pour savoir quels événements écouter
3. Quand un événement est déclenché, Symfony appelle automatiquement la méthode correspondante

---

### Étape 6 : Tester le Workflow

**Test manuel** :

1. Créer un événement (statut = "planifie")
2. Cliquer sur "Démarrer" → Statut passe à "en_cours"
3. Vérifier les logs : `var/log/dev.log`
4. Vérifier que les emails sont envoyés

**Commande pour voir les workflows** :
```bash
php bin/console debug:workflow evenement_publishing
```

**Résultat** :
```
Symfony Workflow "evenement_publishing" (state_machine)

Places
------
- planifie
- en_cours
- termine
- annule

Transitions
-----------
- demarrer (from: planifie, to: en_cours)
- terminer (from: en_cours, to: termine)
- annuler (from: planifie, en_cours, to: annule)
```

---


## 2️⃣ CALENDAR BUNDLE

### Étape 1 : Installation

**Commande** :
```bash
composer require tatarbj/calendar-bundle
```

**Ce qui se passe** :
1. Composer télécharge le bundle
2. Symfony Flex crée `config/packages/calendar.yaml`
3. Le bundle est enregistré automatiquement

---

### Étape 2 : Configuration

**Fichier** : `config/packages/calendar.yaml`

```yaml
# Configuration minimale (le bundle fonctionne avec les défauts)
calendar: ~
```

---

### Étape 3 : Créer un CalendarSubscriber

**Fichier** : `src/EventSubscriber/CalendarSubscriber.php`

**Code complet avec explications** :

```php
<?php

namespace App\EventSubscriber;

use App\Repository\EvenementRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EvenementRepository $evenementRepository,
        private UrlGeneratorInterface $urlGenerator
    ) {}
    
    // Déclarer l'événement qu'on écoute
    public static function getSubscribedEvents(): array
    {
        return [
            // Événement déclenché quand le calendrier charge les données
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }
    
    // Méthode appelée automatiquement par le calendrier
    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        // 1. Récupérer la période affichée dans le calendrier
        $start = $calendar->getStart();  // Date de début (ex: 1er mars 2026)
        $end = $calendar->getEnd();      // Date de fin (ex: 31 mars 2026)
        
        // 2. Récupérer les événements dans cette période
        $evenements = $this->evenementRepository->createQueryBuilder('e')
            ->where('e.dateDebut BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        
        // 3. Pour chaque événement, créer un Event du calendrier
        foreach ($evenements as $evenement) {
            // Créer un Event du calendrier
            $calendarEvent = new Event(
                $evenement->getTitre(),      // Titre affiché
                $evenement->getDateDebut(),  // Date de début
                $evenement->getDateFin()     // Date de fin (optionnel)
            );
            
            // Définir la couleur selon le statut
            $color = match($evenement->getWorkflowStatus()) {
                'planifie' => '#667eea',  // Violet
                'en_cours' => '#28a745',  // Vert
                'termine' => '#6c757d',   // Gris
                'annule' => '#dc3545',    // Rouge
                default => '#667eea',
            };
            
            // Appliquer les options
            $calendarEvent->setOptions([
                'backgroundColor' => $color,
                'borderColor' => $color,
            ]);
            
            // Définir l'URL de redirection au clic
            $calendarEvent->addOption(
                'url',
                $this->urlGenerator->generate('frontoffice_evenement_show', [
                    'id' => $evenement->getId()
                ])
            );
            
            // 4. Ajouter l'événement au calendrier
            $calendar->addEvent($calendarEvent);
        }
    }
}
```

**Comment ça marche ?**

1. **L'utilisateur ouvre la page calendrier**
2. **FullCalendar.js fait une requête AJAX** vers `/calendar/load`
3. **Le CalendarBundle déclenche** l'événement `CalendarEvents::SET_DATA`
4. **Notre CalendarSubscriber écoute** cet événement
5. **La méthode `onCalendarSetData()` est appelée** automatiquement
6. **On récupère les événements** de la base de données
7. **On les ajoute au calendrier** avec `$calendar->addEvent()`
8. **Le CalendarBundle retourne** les données en JSON
9. **FullCalendar.js affiche** les événements

---

### Étape 4 : Créer la Route et le Contrôleur

**Fichier** : `src/Controller/FrontofficeEvenementController.php`

```php
#[Route('/calendar', name: 'frontoffice_evenement_calendar', methods: ['GET'])]
public function calendar(): Response
{
    return $this->render('frontoffice/evenement/calendar.html.twig');
}
```

---

### Étape 5 : Créer le Template

**Fichier** : `templates/frontoffice/evenement/calendar.html.twig`

```twig
{% extends 'frontoffice/base.html.twig' %}

{% block title %}Calendrier des Événements{% endblock %}

{% block body %}
<div class="container mt-5">
    <h1 class="mb-4">📅 Calendrier des Événements</h1>
    
    {# Div où le calendrier sera affiché #}
    <div id="calendar"></div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    
    {# Inclure FullCalendar.js depuis CDN #}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/fr.global.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            // Initialiser FullCalendar
            var calendar = new FullCalendar.Calendar(calendarEl, {
                // Vue initiale
                initialView: 'dayGridMonth',
                
                // Langue française
                locale: 'fr',
                
                // En-têtes des boutons
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                
                // URL pour charger les événements
                // Le CalendarBundle crée automatiquement cette route
                events: '{{ path('calendar_feed') }}',
                
                // Action au clic sur un événement
                eventClick: function(info) {
                    // Rediriger vers la page de détails
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                
                // Personnalisation de l'affichage
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }
            });
            
            // Afficher le calendrier
            calendar.render();
        });
    </script>
{% endblock %}
```

**Explication du code JavaScript** :

1. **`initialView: 'dayGridMonth'`** : Vue mensuelle par défaut
2. **`locale: 'fr'`** : Affichage en français
3. **`events: '{{ path('calendar_feed') }}'`** : URL pour charger les événements
   - Le CalendarBundle crée automatiquement la route `calendar_feed`
   - Cette route appelle notre `CalendarSubscriber`
4. **`eventClick`** : Action au clic sur un événement
5. **`calendar.render()`** : Affiche le calendrier

---

### Étape 6 : Tester le Calendrier

1. Ouvrir `/frontoffice/evenement/calendar`
2. Le calendrier s'affiche avec tous les événements
3. Cliquer sur un événement → Redirection vers la page de détails
4. Changer de mois → Les événements se chargent automatiquement

---

## 3️⃣ FORMULAIRES SYMFONY EN DÉTAIL

### Anatomie d'un Formulaire

**Fichier** : `src/Form/EvenementType.php`

```php
<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Enum\TypeEvenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Tous les formulaires héritent de AbstractType
class EvenementType extends AbstractType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder permet d'ajouter des champs
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',                    // Label affiché
                'attr' => [                            // Attributs HTML
                    'placeholder' => 'Ex: Hackathon',
                    'class' => 'form-control'
                ]
            ])
            ->add('type', EnumType::class, [
                'class' => TypeEvenement::class,       // Enum PHP 8.1+
                'label' => 'Type d\'événement',
                'choice_label' => function($choice) {  // Comment afficher chaque choix
                    return $choice->value;
                }
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',             // Input HTML5 datetime-local
                'label' => 'Date de début'
            ]);
    }
    
    // Configuration du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,  // Entité liée au formulaire
        ]);
    }
}
```

---

### Utilisation dans le Contrôleur

**Fichier** : `src/Controller/FrontofficeParticipationController.php`

**Exemple complet avec explications ligne par ligne** :

```php
#[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,                      // Objet contenant les données GET/POST
    EntityManagerInterface $entityManager, // Pour sauvegarder en base de données
    EmailService $emailService             // Service pour envoyer des emails
): Response {
    // 1. Créer un nouvel objet Participation (vide)
    $participation = new Participation();
    
    // 2. Créer le formulaire lié à l'objet
    $form = $this->createForm(ParticipationFrontType::class, $participation);
    
    // 3. Récupérer les données POST et les lier au formulaire
    $form->handleRequest($request);
    
    // 4. Vérifier si le formulaire est soumis ET valide
    if ($form->isSubmitted() && $form->isValid()) {
        // À ce stade, $participation contient les données du formulaire
        
        // 5. Valider la participation selon les règles métier
        $result = $participation->validateParticipation();
        
        if ($result['accepted']) {
            // 6. Préparer l'insertion en base de données
            $entityManager->persist($participation);
            
            // 7. Exécuter l'insertion
            $entityManager->flush();
            
            // 8. Envoyer un email de confirmation
            $emailService->sendParticipationConfirmation(...);
            
            // 9. Message flash de succès
            $this->addFlash('success', $result['message']);
        } else {
            // Participation refusée
            $this->addFlash('error', $result['message']);
        }
        
        // 10. Redirection
        return $this->redirectToRoute('app_mes_participations');
    }
    
    // 11. Afficher le formulaire (GET ou formulaire invalide)
    return $this->render('frontoffice/participation/new.html.twig', [
        'form' => $form,
        'participation' => $participation
    ]);
}
```

---

### Explication Détaillée : `isSubmitted()` et `isValid()`

#### `$form->isSubmitted()`

**Rôle** : Vérifie si le formulaire a été soumis (POST)

**Comment ça marche ?**

```php
// Symfony vérifie si la requête est POST
// ET si les données POST contiennent le nom du formulaire

// Exemple de données POST:
// participation_front[equipe] = 1
// participation_front[evenement] = 5

// Le préfixe "participation_front" est le nom du formulaire
// Symfony vérifie si ce préfixe existe dans les données POST
```

**Retourne** :
- `true` : Le formulaire a été soumis (requête POST)
- `false` : Le formulaire n'a pas été soumis (requête GET)

---

#### `$form->isValid()`

**Rôle** : Vérifie si les données du formulaire sont valides

**Comment ça marche ?**

1. **Symfony lit les contraintes** dans l'entité :
   ```php
   #[Assert\NotBlank(message: "Le titre est obligatoire")]
   #[Assert\Length(min: 3, max: 255)]
   private string $titre;
   ```

2. **Symfony valide chaque champ** :
   - Titre vide ? → Erreur "Le titre est obligatoire"
   - Titre < 3 caractères ? → Erreur "Le titre doit contenir au moins 3 caractères"
   - Date début < aujourd'hui ? → Erreur "La date doit être dans le futur"

3. **Symfony retourne** :
   - `true` : Toutes les contraintes sont respectées
   - `false` : Au moins une contrainte n'est pas respectée

**Exemple de validation** :

```php
// Données POST:
// evenement[titre] = "AB"  (trop court)
// evenement[lieu] = ""     (vide)

$form->handleRequest($request);

if ($form->isSubmitted()) {  // true (POST)
    if ($form->isValid()) {  // false (erreurs de validation)
        // Ce code ne sera PAS exécuté
    } else {
        // Les erreurs sont automatiquement affichées dans le template
    }
}
```

---

### Affichage des Erreurs dans le Template

**Template** : `templates/frontoffice/participation/new.html.twig`

```twig
{{ form_start(form) }}
    {{ form_row(form.equipe) }}
    {# Si le champ equipe a une erreur, elle s'affiche automatiquement #}
    
    <button type="submit">Participer</button>
{{ form_end(form) }}
```

**HTML généré avec erreur** :

```html
<div class="form-group">
    <label for="participation_equipe">Équipe</label>
    <select id="participation_equipe" name="participation[equipe]" class="form-control is-invalid">
        <option value="">Choisir une équipe</option>
        <option value="1">Team Alpha</option>
    </select>
    <div class="invalid-feedback">
        Veuillez sélectionner une équipe
    </div>
</div>
```

---

## 4️⃣ ENTITYMANAGER ET FLUSH

### Qu'est-ce que l'EntityManager ?

L'**EntityManager** est l'outil principal de Doctrine pour interagir avec la base de données.

**Analogie** : C'est comme un panier d'achat :
- `persist()` : Ajouter un article au panier
- `remove()` : Retirer un article du panier
- `flush()` : Passer à la caisse (exécuter toutes les opérations)

---

### Les 3 Opérations Principales

#### 1. `persist()` - Préparer une insertion

```php
$evenement = new Evenement();
$evenement->setTitre("Hackathon");
$evenement->setLieu("ESPRIT");

// Préparer l'insertion (en mémoire seulement)
$entityManager->persist($evenement);

// À ce stade, RIEN n'est encore en base de données
// $evenement->getId() retourne null
```

**Ce qui se passe** :
- Doctrine ajoute l'objet à sa liste d'objets à insérer
- Aucune requête SQL n'est exécutée
- L'objet est en mémoire uniquement

---

#### 2. `flush()` - Exécuter toutes les opérations

```php
// Exécuter TOUTES les opérations en attente
$entityManager->flush();

// Maintenant, l'INSERT est exécuté
// $evenement->getId() retourne l'ID auto-incrémenté
```

**Ce qui se passe** :
1. Doctrine génère les requêtes SQL
2. Doctrine ouvre une transaction
3. Doctrine exécute toutes les requêtes
4. Si tout réussit : COMMIT
5. Si une erreur : ROLLBACK (annulation)

**SQL généré** :
```sql
BEGIN TRANSACTION;
INSERT INTO evenement (titre, lieu, date_debut, date_fin, ...) 
VALUES ('Hackathon', 'ESPRIT', '2026-03-15', '2026-03-16', ...);
COMMIT;
```

---

#### 3. `remove()` - Préparer une suppression

```php
$evenement = $evenementRepository->find(1);

// Préparer la suppression (en mémoire seulement)
$entityManager->remove($evenement);

// À ce stade, RIEN n'est encore supprimé en base de données

// Exécuter la suppression
$entityManager->flush();
```

**SQL généré** :
```sql
BEGIN TRANSACTION;
DELETE FROM evenement WHERE id = 1;
COMMIT;
```

---

### Modification : Pas besoin de `persist()`

```php
// Récupérer un événement existant
$evenement = $evenementRepository->find(1);

// Modifier
$evenement->setTitre("Nouveau titre");

// Pas besoin de persist() pour une modification !
// Doctrine détecte automatiquement les changements

// Sauvegarder
$entityManager->flush();
```

**Comment Doctrine détecte les changements ?**

1. Quand tu récupères un objet, Doctrine garde une copie en mémoire
2. Avant `flush()`, Doctrine compare l'objet actuel avec la copie
3. Si différent → Doctrine génère un UPDATE

**SQL généré** :
```sql
BEGIN TRANSACTION;
UPDATE evenement SET titre = 'Nouveau titre' WHERE id = 1;
COMMIT;
```

---

### Exemple Complet : Créer une Participation

**Code** :
```php
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $participation = new Participation();
    $form = $this->createForm(ParticipationFrontType::class, $participation);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // 1. Valider selon les règles métier
        $result = $participation->validateParticipation();
        
        if ($result['accepted']) {
            // 2. Préparer l'insertion
            $entityManager->persist($participation);
            
            // 3. Exécuter l'insertion
            $entityManager->flush();
            
            // 4. Maintenant $participation->getId() retourne l'ID
            $this->addFlash('success', 'Participation créée avec ID: ' . $participation->getId());
        }
        
        return $this->redirectToRoute('app_mes_participations');
    }
    
    return $this->render('frontoffice/participation/new.html.twig', ['form' => $form]);
}
```

**Ordre d'exécution** :

```
1. Utilisateur soumet le formulaire (POST)
   ↓
2. $form->handleRequest($request)
   → Récupère les données POST
   → Les lie à l'objet $participation
   ↓
3. $form->isSubmitted() → true
   ↓
4. $form->isValid() → Valide les contraintes
   ↓
5. $participation->validateParticipation()
   → Valide les règles métier
   ↓
6. $entityManager->persist($participation)
   → Prépare l'insertion (en mémoire)
   ↓
7. $entityManager->flush()
   → Exécute l'INSERT en base de données
   → $participation->getId() est maintenant disponible
   ↓
8. Redirection vers la liste
```

---


## 5️⃣ QUERYBUILDER ET DQL

### QueryBuilder : Construction de Requêtes

**Fichier** : `src/Controller/FrontofficeParticipationController.php`

**Exemple réel de ton code** :

```php
#[Route('/mes-participations', name: 'app_mes_participations')]
public function mesParticipations(ParticipationRepository $participationRepository): Response
{
    $user = $this->getUser();  // Utilisateur connecté
    
    // Créer un QueryBuilder
    $participations = $participationRepository->createQueryBuilder('p')
        // JOIN avec la table equipe
        ->join('p.equipe', 'e')
        // JOIN avec la table etudiant
        ->join('e.etudiants', 'et')
        // WHERE: Filtrer par utilisateur
        ->where('et.id = :userId')
        // AND WHERE: Exclure les participations refusées
        ->andWhere('p.statut != :refuse')
        // Définir les paramètres
        ->setParameter('userId', $user->getId())
        ->setParameter('refuse', 'Refusé')
        // Exécuter la requête
        ->getQuery()
        ->getResult();
    
    return $this->render('frontoffice/participation/mes_participations.html.twig', [
        'participations' => $participations,
    ]);
}
```

**SQL généré** :
```sql
SELECT p.* 
FROM participation p
INNER JOIN equipe e ON p.equipe_id = e.id
INNER JOIN equipe_etudiant ee ON e.id = ee.equipe_id
INNER JOIN etudiant et ON ee.etudiant_id = et.id
WHERE et.id = 123
AND p.statut != 'Refusé'
```

---

### Explication Ligne par Ligne

#### 1. `createQueryBuilder('p')`

```php
$participationRepository->createQueryBuilder('p')
```

- Crée un QueryBuilder pour l'entité `Participation`
- `'p'` est l'alias (comme `AS p` en SQL)
- Équivalent SQL : `SELECT p FROM Participation p`

---

#### 2. `join('p.equipe', 'e')`

```php
->join('p.equipe', 'e')
```

- JOIN avec la relation `equipe` de l'entité `Participation`
- `'e'` est l'alias pour `Equipe`
- Doctrine connaît la relation grâce à l'annotation `#[ORM\ManyToOne]`
- Équivalent SQL : `INNER JOIN equipe e ON p.equipe_id = e.id`

---

#### 3. `join('e.etudiants', 'et')`

```php
->join('e.etudiants', 'et')
```

- JOIN avec la relation `etudiants` de l'entité `Equipe`
- `'et'` est l'alias pour `Etudiant`
- Équivalent SQL : `INNER JOIN equipe_etudiant ... INNER JOIN etudiant et ...`

---

#### 4. `where('et.id = :userId')`

```php
->where('et.id = :userId')
```

- Condition WHERE
- `:userId` est un paramètre (protection contre SQL Injection)
- Équivalent SQL : `WHERE et.id = ?`

---

#### 5. `andWhere('p.statut != :refuse')`

```php
->andWhere('p.statut != :refuse')
```

- Ajoute une condition AND
- Équivalent SQL : `AND p.statut != ?`

---

#### 6. `setParameter('userId', $user->getId())`

```php
->setParameter('userId', $user->getId())
->setParameter('refuse', 'Refusé')
```

- Définit les valeurs des paramètres
- Protection contre SQL Injection
- Symfony échappe automatiquement les valeurs

**Mauvaise pratique (SQL Injection)** :
```php
// ❌ NE JAMAIS FAIRE ÇA
->where("et.id = " . $user->getId())
```

**Bonne pratique** :
```php
// ✅ TOUJOURS UTILISER DES PARAMÈTRES
->where('et.id = :userId')
->setParameter('userId', $user->getId())
```

---

#### 7. `getQuery()->getResult()`

```php
->getQuery()      // Transforme le QueryBuilder en Query
->getResult()     // Exécute la requête et retourne un tableau d'objets
```

**Autres méthodes disponibles** :

| Méthode | Retour | Utilisation |
|---------|--------|-------------|
| `getResult()` | `array` | Tous les résultats |
| `getOneOrNullResult()` | `object\|null` | Un seul résultat ou null |
| `getSingleResult()` | `object` | Un seul résultat (exception si 0 ou >1) |
| `getSingleScalarResult()` | `mixed` | Une seule valeur (ex: COUNT) |

**Exemples** :

```php
// Récupérer tous les événements
$evenements = $evenementRepository->createQueryBuilder('e')
    ->getQuery()
    ->getResult();  // array d'objets Evenement

// Récupérer un seul événement
$evenement = $evenementRepository->createQueryBuilder('e')
    ->where('e.id = :id')
    ->setParameter('id', 1)
    ->getQuery()
    ->getOneOrNullResult();  // Evenement ou null

// Compter les événements
$count = $evenementRepository->createQueryBuilder('e')
    ->select('COUNT(e.id)')
    ->getQuery()
    ->getSingleScalarResult();  // int
```

---

### Méthodes QueryBuilder Disponibles

| Méthode | Description | Exemple |
|---------|-------------|---------|
| `select()` | Colonnes à sélectionner | `->select('e.titre, e.lieu')` |
| `from()` | Table source | `->from(Evenement::class, 'e')` |
| `where()` | Condition WHERE | `->where('e.status = :status')` |
| `andWhere()` | Condition AND | `->andWhere('e.lieu = :lieu')` |
| `orWhere()` | Condition OR | `->orWhere('e.type = :type')` |
| `join()` | INNER JOIN | `->join('e.participations', 'p')` |
| `leftJoin()` | LEFT JOIN | `->leftJoin('e.equipes', 'eq')` |
| `orderBy()` | Tri | `->orderBy('e.dateDebut', 'DESC')` |
| `addOrderBy()` | Tri supplémentaire | `->addOrderBy('e.titre', 'ASC')` |
| `groupBy()` | GROUP BY | `->groupBy('e.type')` |
| `having()` | HAVING | `->having('COUNT(p.id) > 5')` |
| `setMaxResults()` | LIMIT | `->setMaxResults(10)` |
| `setFirstResult()` | OFFSET | `->setFirstResult(20)` |

---

### DQL (Doctrine Query Language)

**Alternative au QueryBuilder** : Écrire la requête comme du SQL

**Exemple** :

```php
$dql = "SELECT e FROM App\Entity\Evenement e 
        WHERE e.status = :status 
        AND e.dateDebut > :now
        ORDER BY e.dateDebut DESC";

$query = $entityManager->createQuery($dql);
$query->setParameter('status', 'Planifié');
$query->setParameter('now', new \DateTime());
$query->setMaxResults(10);

$evenements = $query->getResult();
```

**Différences avec SQL** :

| SQL | DQL |
|-----|-----|
| `SELECT * FROM evenement` | `SELECT e FROM App\Entity\Evenement e` |
| `WHERE evenement.status = 'Planifié'` | `WHERE e.status = :status` |
| `INNER JOIN participation ON ...` | `JOIN e.participations p` |
| Noms de tables | Noms de classes |
| Noms de colonnes | Noms de propriétés |

---

### Quand Utiliser Quoi ?

| Situation | Outil |
|-----------|-------|
| Requête simple | `findBy()`, `findOneBy()` |
| Requête avec conditions | QueryBuilder |
| Requête complexe avec plusieurs JOIN | DQL ou QueryBuilder |
| Requête dynamique (filtres optionnels) | QueryBuilder |
| Requête SQL native nécessaire | SQL brut |

**Exemple de requête dynamique** :

```php
public function search(array $filters): array
{
    $qb = $this->createQueryBuilder('e');
    
    // Filtre optionnel par statut
    if (isset($filters['status'])) {
        $qb->andWhere('e.status = :status')
           ->setParameter('status', $filters['status']);
    }
    
    // Filtre optionnel par type
    if (isset($filters['type'])) {
        $qb->andWhere('e.type = :type')
           ->setParameter('type', $filters['type']);
    }
    
    // Filtre optionnel par date
    if (isset($filters['dateMin'])) {
        $qb->andWhere('e.dateDebut >= :dateMin')
           ->setParameter('dateMin', $filters['dateMin']);
    }
    
    return $qb->getQuery()->getResult();
}
```

---

## 6️⃣ CYCLE DE VIE D'UNE REQUÊTE

### Scénario Complet : Créer une Participation

**Étape par étape avec ton code** :

```
1. UTILISATEUR ouvre /participation/new
   ↓
   HTTP GET /participation/new
   ↓
2. SYMFONY trouve la route
   Route: app_participation_new
   Contrôleur: FrontofficeParticipationController::new()
   ↓
3. CONTRÔLEUR crée le formulaire
   $participation = new Participation();
   $form = $this->createForm(ParticipationFrontType::class, $participation);
   ↓
4. TEMPLATE affiche le formulaire vide
   {{ form_start(form) }}
   {{ form_row(form.equipe) }}
   {{ form_row(form.evenement) }}
   <button type="submit">Participer</button>
   {{ form_end(form) }}
   ↓
5. UTILISATEUR remplit et soumet le formulaire
   POST /participation/new
   Données: participation[equipe]=1&participation[evenement]=5
   ↓
6. SYMFONY trouve la route (même route, méthode POST)
   ↓
7. CONTRÔLEUR récupère les données POST
   $form->handleRequest($request);
   ↓
8. SYMFONY lie les données à l'objet
   $participation->setEquipe($equipe);
   $participation->setEvenement($evenement);
   ↓
9. SYMFONY valide les contraintes
   $form->isValid()
   → Vérifie les annotations #[Assert\...]
   ↓
10. VALIDATION MÉTIER
    $result = $participation->validateParticipation();
    → Vérifie les 3 contraintes:
      1. Événement non annulé
      2. Capacité maximale non atteinte
      3. Pas de doublon d'étudiants
    ↓
11. SI ACCEPTÉ: SAUVEGARDER
    $entityManager->persist($participation);
    $entityManager->flush();
    ↓
    SQL: INSERT INTO participation (equipe_id, evenement_id, statut) 
         VALUES (1, 5, 'Accepté')
    ↓
12. ENVOYER EMAIL
    $emailService->sendParticipationConfirmation(...);
    ↓
    API SendGrid: POST https://api.sendgrid.com/v3/mail/send
    ↓
13. MESSAGE FLASH
    $this->addFlash('success', 'Participation acceptée');
    ↓
14. REDIRECTION
    return $this->redirectToRoute('app_mes_participations');
    ↓
    HTTP 302 Redirect → /participation/mes-participations
    ↓
15. AFFICHER LA LISTE
    GET /participation/mes-participations
    ↓
    QueryBuilder récupère les participations
    ↓
    SQL: SELECT p.* FROM participation p
         INNER JOIN equipe e ON p.equipe_id = e.id
         INNER JOIN equipe_etudiant ee ON e.id = ee.equipe_id
         WHERE ee.etudiant_id = 123
    ↓
16. TEMPLATE affiche la liste
    {% for participation in participations %}
        <tr>
            <td>{{ participation.evenement.titre }}</td>
            <td>{{ participation.equipe.nom }}</td>
            <td>{{ participation.statut.value }}</td>
        </tr>
    {% endfor %}
    ↓
17. NAVIGATEUR affiche la page
```

---

### Schéma Visuel

```
┌─────────────┐
│   BROWSER   │
└──────┬──────┘
       │ GET /participation/new
       ▼
┌─────────────────────────────────┐
│         SYMFONY ROUTING         │
│  Route: app_participation_new   │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         CONTROLLER              │
│  new(Request, EntityManager)    │
│  1. createForm()                │
│  2. handleRequest()             │
│  3. isSubmitted() && isValid()  │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         FORM COMPONENT          │
│  1. Lie les données POST        │
│  2. Valide les contraintes      │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         ENTITY                  │
│  validateParticipation()        │
│  Règles métier                  │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│      ENTITY MANAGER             │
│  persist() + flush()            │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         DOCTRINE ORM            │
│  Génère le SQL                  │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         DATABASE                │
│  INSERT INTO participation      │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         SERVICE                 │
│  EmailService::send()           │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         SENDGRID API            │
│  Envoie l'email                 │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         CONTROLLER              │
│  addFlash() + redirectToRoute() │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│         TWIG TEMPLATE           │
│  Affiche la liste               │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────┐
│   BROWSER   │
└─────────────┘
```

---

## 📊 RÉSUMÉ DES CONCEPTS

| Concept | Définition | Exemple |
|---------|------------|---------|
| **Workflow** | Machine à états | Planifié → En cours → Terminé |
| **EventSubscriber** | Écoute des événements | Envoyer emails automatiquement |
| **Calendar** | Affichage calendrier | FullCalendar.js + CalendarBundle |
| **Formulaire** | Lie objet PHP ↔ HTML | `createForm(EvenementType::class)` |
| **isSubmitted()** | Formulaire soumis ? | Vérifie si POST |
| **isValid()** | Données valides ? | Vérifie les contraintes |
| **persist()** | Préparer insertion | En mémoire seulement |
| **flush()** | Exécuter opérations | INSERT/UPDATE/DELETE en BDD |
| **QueryBuilder** | Construire requêtes | `->where()->andWhere()` |
| **DQL** | Doctrine Query Language | Comme SQL mais avec classes |

---

## 🎯 POINTS CLÉS POUR LA VALIDATION

### Workflow Bundle

1. **Configuration** : `config/packages/workflow.yaml`
2. **Propriété entité** : `workflowStatus`
3. **Injection** : `WorkflowInterface $evenementPublishingStateMachine`
4. **Vérifier** : `$workflow->can($evenement, 'demarrer')`
5. **Appliquer** : `$workflow->apply($evenement, 'demarrer')`
6. **EventSubscriber** : Écoute les transitions et exécute du code automatiquement

### Calendar Bundle

1. **Installation** : `composer require tatarbj/calendar-bundle`
2. **CalendarSubscriber** : Écoute `CalendarEvents::SET_DATA`
3. **Méthode** : `onCalendarSetData(CalendarEvent $calendar)`
4. **Ajouter événements** : `$calendar->addEvent($calendarEvent)`
5. **Template** : FullCalendar.js avec `events: '{{ path('calendar_feed') }}'`

### Formulaires

1. **Créer** : `$form = $this->createForm(EvenementType::class, $evenement)`
2. **Récupérer POST** : `$form->handleRequest($request)`
3. **Vérifier soumission** : `$form->isSubmitted()`
4. **Vérifier validité** : `$form->isValid()`
5. **Afficher** : `{{ form_start(form) }} ... {{ form_end(form) }}`

### EntityManager

1. **Créer** : `persist()` + `flush()`
2. **Modifier** : `flush()` seulement
3. **Supprimer** : `remove()` + `flush()`
4. **Ordre** : persist() → flush() → ID disponible

### QueryBuilder

1. **Créer** : `$repo->createQueryBuilder('e')`
2. **Filtrer** : `->where('e.status = :status')`
3. **Joindre** : `->join('e.participations', 'p')`
4. **Paramètres** : `->setParameter('status', 'Planifié')`
5. **Exécuter** : `->getQuery()->getResult()`

---

FIN DU GUIDE COMPLET
