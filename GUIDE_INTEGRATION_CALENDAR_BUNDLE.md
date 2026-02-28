# 📅 GUIDE COMPLET - INTÉGRATION CALENDAR BUNDLE

**Date**: 21 Février 2026  
**Module**: Événement  
**Bundle**: FullCalendar (JavaScript) + Configuration Symfony  
**Objectif**: Afficher un calendrier interactif des événements

---

## 📋 TABLE DES MATIÈRES

1. [Vue d'ensemble](#vue-densemble)
2. [Choix du bundle](#choix-du-bundle)
3. [Installation](#installation)
4. [Configuration backend](#configuration-backend)
5. [Configuration frontend](#configuration-frontend)
6. [Intégration dans le module](#intégration)
7. [Fonctionnalités avancées](#fonctionnalités-avancées)
8. [Tests](#tests)

---

## 🎯 VUE D'ENSEMBLE {#vue-densemble}

### Objectif
Ajouter une vue calendrier interactive pour visualiser les événements:
- 📅 Vue mensuelle, hebdomadaire, journalière
- 🎨 Couleurs par type d'événement
- 🖱️ Clic sur événement → détails
- ➕ Création rapide depuis le calendrier (admin)
- 📱 Responsive (mobile-friendly)

### Résultat attendu
```
┌─────────────────────────────────────────────────┐
│  Février 2026                    [Mois] [Semaine]│
├─────────────────────────────────────────────────┤
│ Lun  Mar  Mer  Jeu  Ven  Sam  Dim              │
│                                                  │
│  17   18   19   20   21   22   23              │
│                [Workshop]                        │
│                 Python                           │
│                                                  │
│  24   25   26   27   28                         │
│      [Hackathon]                                 │
│       24h Code                                   │
└─────────────────────────────────────────────────┘
```

---

## 🔍 CHOIX DU BUNDLE {#choix-du-bundle}

### Option 1: FullCalendar.io (RECOMMANDÉ) ⭐⭐⭐⭐⭐

**Pourquoi FullCalendar?**
- ✅ **100% gratuit** (licence MIT)
- ✅ **Très populaire** (15k+ stars GitHub)
- ✅ **Riche en fonctionnalités**
- ✅ **Responsive** (mobile-friendly)
- ✅ **Bien documenté**
- ✅ **Facile à intégrer** avec Symfony
- ✅ **Personnalisable** (couleurs, vues, etc.)

**Fonctionnalités**:
- Vues: Mois, Semaine, Jour, Liste
- Drag & Drop (déplacer événements)
- Resize (redimensionner événements)
- Événements récurrents
- Filtres par catégorie
- Export iCal
- Multilingue (français inclus)

**Site officiel**: https://fullcalendar.io/

### Option 2: CalendarBundle Symfony (Alternative)

**Avantages**:
- Intégration Symfony native
- Configuration YAML

**Inconvénients**:
- Moins de fonctionnalités
- Moins populaire
- Documentation limitée

**Verdict**: FullCalendar est le meilleur choix! 🏆

---

## 📦 INSTALLATION {#installation}

### Étape 1: Installer FullCalendar via NPM

```bash
# Si tu n'as pas encore npm/node installé
# Télécharge Node.js depuis https://nodejs.org/

# Initialiser npm dans le projet (si pas déjà fait)
npm init -y

# Installer FullCalendar
npm install @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction @fullcalendar/list
```

**Packages installés**:
- `@fullcalendar/core` - Core du calendrier
- `@fullcalendar/daygrid` - Vue mois/jour
- `@fullcalendar/timegrid` - Vue semaine avec heures
- `@fullcalendar/interaction` - Drag & drop, clic
- `@fullcalendar/list` - Vue liste

### Étape 2: Installer le plugin français

```bash
npm install @fullcalendar/core/locales/fr
```

### Étape 3: Vérifier l'installation

```bash
# Vérifier que les packages sont dans package.json
cat package.json
```

Tu devrais voir:
```json
{
  "dependencies": {
    "@fullcalendar/core": "^6.x.x",
    "@fullcalendar/daygrid": "^6.x.x",
    "@fullcalendar/timegrid": "^6.x.x",
    "@fullcalendar/interaction": "^6.x.x",
    "@fullcalendar/list": "^6.x.x"
  }
}
```

---

## ⚙️ CONFIGURATION BACKEND {#configuration-backend}

### Étape 1: Créer l'API endpoint pour les événements

**Fichier**: `src/Controller/EvenementController.php`

Ajouter cette route:

```php
#[Route('/api/calendar/events', name: 'api_calendar_events', methods: ['GET'])]
public function getCalendarEvents(EvenementRepository $evenementRepository): JsonResponse
{
    $evenements = $evenementRepository->findAll();
    
    $events = [];
    foreach ($evenements as $evenement) {
        // Mettre à jour le statut
        $evenement->updateStatus();
        
        // Couleur selon le type
        $color = match($evenement->getType()->value) {
            'Workshop' => '#667eea',      // Violet
            'Conference' => '#f093fb',    // Rose
            'Hackathon' => '#4facfe',     // Bleu
            'Seminar' => '#43e97b',       // Vert
            default => '#667eea',
        };
        
        // Couleur selon le statut
        if ($evenement->getStatus()->value === 'Annulé') {
            $color = '#95a5a6'; // Gris
        } elseif ($evenement->getStatus()->value === 'Passé') {
            $color = '#7fb77e'; // Vert pâle
        }
        
        $events[] = [
            'id' => $evenement->getId(),
            'title' => $evenement->getTitre(),
            'start' => $evenement->getDateDebut()->format('Y-m-d\TH:i:s'),
            'end' => $evenement->getDateFin()->format('Y-m-d\TH:i:s'),
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'type' => $evenement->getType()->value,
                'lieu' => $evenement->getLieu(),
                'description' => substr($evenement->getDescription(), 0, 100) . '...',
                'status' => $evenement->getStatus()->value,
                'nbMax' => $evenement->getNbMax(),
                'nbParticipations' => count($evenement->getParticipations()),
            ],
        ];
    }
    
    return new JsonResponse($events);
}
```

### Étape 2: Créer la route pour la vue calendrier

```php
#[Route('/calendar', name: 'backoffice_evenement_calendar', methods: ['GET'])]
public function calendar(): Response
{
    return $this->render('backoffice/evenement/calendar.html.twig');
}
```

---

## 🎨 CONFIGURATION FRONTEND {#configuration-frontend}

### Étape 1: Créer le fichier JavaScript

**Fichier**: `public/backoffice/js/calendar.js`

```javascript
// Importer FullCalendar
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import frLocale from '@fullcalendar/core/locales/fr';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) return;
    
    const calendar = new Calendar(calendarEl, {
        // Plugins
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        
        // Configuration de base
        initialView: 'dayGridMonth',
        locale: frLocale,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        
        // Boutons personnalisés
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        
        // Hauteur
        height: 'auto',
        
        // Charger les événements depuis l'API
        events: '/backoffice/evenement/api/calendar/events',
        
        // Clic sur un événement
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        
        // Clic sur une date (pour créer un événement)
        dateClick: function(info) {
            // Rediriger vers le formulaire de création avec la date pré-remplie
            window.location.href = `/backoffice/evenement/new?date=${info.dateStr}`;
        },
        
        // Personnalisation de l'affichage
        eventContent: function(arg) {
            let html = '<div class="fc-event-main-frame">';
            html += '<div class="fc-event-time">' + arg.timeText + '</div>';
            html += '<div class="fc-event-title-container">';
            html += '<div class="fc-event-title">' + arg.event.title + '</div>';
            html += '</div>';
            html += '</div>';
            
            return { html: html };
        },
        
        // Tooltip au survol
        eventMouseEnter: function(info) {
            const props = info.event.extendedProps;
            const tooltip = `
                <div class="calendar-tooltip">
                    <strong>${info.event.title}</strong><br>
                    <small>${props.type} - ${props.lieu}</small><br>
                    <small>Statut: ${props.status}</small><br>
                    <small>Places: ${props.nbParticipations}/${props.nbMax}</small>
                </div>
            `;
            
            // Créer et afficher le tooltip
            const tooltipEl = document.createElement('div');
            tooltipEl.innerHTML = tooltip;
            tooltipEl.className = 'fc-tooltip';
            tooltipEl.style.position = 'absolute';
            tooltipEl.style.zIndex = '9999';
            document.body.appendChild(tooltipEl);
            
            // Positionner le tooltip
            const rect = info.el.getBoundingClientRect();
            tooltipEl.style.top = (rect.top - tooltipEl.offsetHeight - 10) + 'px';
            tooltipEl.style.left = rect.left + 'px';
            
            // Stocker la référence pour le supprimer plus tard
            info.el._tooltip = tooltipEl;
        },
        
        eventMouseLeave: function(info) {
            if (info.el._tooltip) {
                info.el._tooltip.remove();
                delete info.el._tooltip;
            }
        },
        
        // Chargement
        loading: function(isLoading) {
            if (isLoading) {
                document.getElementById('calendar-loading').style.display = 'block';
            } else {
                document.getElementById('calendar-loading').style.display = 'none';
            }
        }
    });
    
    calendar.render();
});

// Fonction pour afficher les détails d'un événement
function showEventDetails(event) {
    const props = event.extendedProps;
    
    const modal = `
        <div class="modal-overlay" id="event-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>${event.title}</h2>
                    <button onclick="closeModal()" class="close-btn">×</button>
                </div>
                <div class="modal-body">
                    <p><strong>Type:</strong> ${props.type}</p>
                    <p><strong>Lieu:</strong> ${props.lieu}</p>
                    <p><strong>Début:</strong> ${event.start.toLocaleString('fr-FR')}</p>
                    <p><strong>Fin:</strong> ${event.end.toLocaleString('fr-FR')}</p>
                    <p><strong>Statut:</strong> <span class="badge">${props.status}</span></p>
                    <p><strong>Participations:</strong> ${props.nbParticipations} / ${props.nbMax}</p>
                    <p><strong>Description:</strong> ${props.description}</p>
                </div>
                <div class="modal-footer">
                    <a href="/backoffice/evenement/${event.id}" class="btn btn-primary">Voir détails</a>
                    <a href="/backoffice/evenement/${event.id}/edit" class="btn btn-warning">Modifier</a>
                    <button onclick="closeModal()" class="btn btn-secondary">Fermer</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
}

// Fonction pour fermer la modal
function closeModal() {
    const modal = document.getElementById('event-modal');
    if (modal) {
        modal.remove();
    }
}

// Exporter pour utilisation globale
window.closeModal = closeModal;
```

### Étape 2: Créer le template Twig

**Fichier**: `templates/backoffice/evenement/calendar.html.twig`

```twig
{% extends 'backoffice/base.html.twig' %}

{% block title %}Calendrier des Événements{% endblock %}
{% block page_title %}Calendrier des Événements{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <style>
        /* Styles pour le calendrier */
        #calendar {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Loading spinner */
        #calendar-loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        /* Tooltip */
        .fc-tooltip {
            background: rgba(0,0,0,0.9);
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            max-width: 250px;
        }
        
        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            margin: 0;
            color: #667eea;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 32px;
            cursor: pointer;
            color: #999;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-body p {
            margin: 10px 0;
        }
        
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        /* Personnalisation des événements */
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
        }
        
        .fc-event:hover {
            opacity: 0.8;
        }
        
        /* Boutons */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            background: #667eea;
            color: white;
        }
    </style>
{% endblock %}

{% block body %}
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="card-title">📅 Calendrier des Événements</h2>
        <div>
            <a href="{{ path('backoffice_evenements') }}" class="btn btn-secondary">
                📋 Vue Liste
            </a>
            <a href="{{ path('backoffice_evenement_new') }}" class="btn btn-primary">
                ➕ Nouvel Événement
            </a>
        </div>
    </div>
    
    <div id="calendar-loading">
        <div style="display: inline-block; width: 50px; height: 50px; border: 5px solid rgba(102, 126, 234, 0.2); border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p>Chargement du calendrier...</p>
    </div>
    
    <div id="calendar"></div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script type="module" src="{{ asset('backoffice/js/calendar.js') }}"></script>
{% endblock %}
```

---

## 🔗 INTÉGRATION DANS LE MODULE {#intégration}

### Étape 1: Ajouter le lien dans le menu

**Fichier**: `templates/backoffice/evenement/index.html.twig`

Ajouter ce bouton en haut:

```twig
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 class="card-title">Liste des Événements</h2>
    <div style="display: flex; gap: 10px;">
        <a href="{{ path('backoffice_evenement_calendar') }}" class="btn" style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: white;">
            📅 Vue Calendrier
        </a>
        <a href="{{ path('backoffice_evenement_new') }}" class="btn btn-primary">
            ➕ Ajouter un événement
        </a>
    </div>
</div>
```

### Étape 2: Compiler les assets

```bash
# Si tu utilises Webpack Encore
npm run dev

# Ou en mode watch (auto-recompile)
npm run watch
```

---

## 🚀 FONCTIONNALITÉS AVANCÉES {#fonctionnalités-avancées}

### 1. Filtres par Type

Ajouter des boutons de filtre:

```javascript
// Dans calendar.js
const typeFilters = {
    'Workshop': true,
    'Conference': true,
    'Hackathon': true,
    'Seminar': true
};

function toggleFilter(type) {
    typeFilters[type] = !typeFilters[type];
    calendar.refetchEvents();
}

// Modifier la fonction events
events: function(info, successCallback, failureCallback) {
    fetch('/backoffice/evenement/api/calendar/events')
        .then(response => response.json())
        .then(events => {
            // Filtrer selon les types actifs
            const filteredEvents = events.filter(event => 
                typeFilters[event.extendedProps.type]
            );
            successCallback(filteredEvents);
        })
        .catch(error => failureCallback(error));
}
```

### 2. Drag & Drop (Déplacer événements)

```javascript
// Dans la configuration du calendrier
editable: true,
eventDrop: function(info) {
    // Envoyer la mise à jour au serveur
    fetch(`/backoffice/evenement/${info.event.id}/update-dates`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            start: info.event.start.toISOString(),
            end: info.event.end.toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            info.revert(); // Annuler si erreur
            alert('Erreur lors de la mise à jour');
        }
    });
}
```

### 3. Export iCal

```php
// Dans EvenementController.php
#[Route('/api/calendar/export.ics', name: 'api_calendar_export', methods: ['GET'])]
public function exportCalendar(EvenementRepository $evenementRepository): Response
{
    $evenements = $evenementRepository->findAll();
    
    $ical = "BEGIN:VCALENDAR\r\n";
    $ical .= "VERSION:2.0\r\n";
    $ical .= "PRODID:-//AutoLearn//Events//FR\r\n";
    
    foreach ($evenements as $evenement) {
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:" . $evenement->getId() . "@autolearn.com\r\n";
        $ical .= "DTSTAMP:" . (new \DateTime())->format('Ymd\THis\Z') . "\r\n";
        $ical .= "DTSTART:" . $evenement->getDateDebut()->format('Ymd\THis\Z') . "\r\n";
        $ical .= "DTEND:" . $evenement->getDateFin()->format('Ymd\THis\Z') . "\r\n";
        $ical .= "SUMMARY:" . $evenement->getTitre() . "\r\n";
        $ical .= "DESCRIPTION:" . $evenement->getDescription() . "\r\n";
        $ical .= "LOCATION:" . $evenement->getLieu() . "\r\n";
        $ical .= "END:VEVENT\r\n";
    }
    
    $ical .= "END:VCALENDAR\r\n";
    
    return new Response($ical, 200, [
        'Content-Type' => 'text/calendar; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="evenements.ics"'
    ]);
}
```

---

## ✅ TESTS {#tests}

### Test 1: Affichage du calendrier
1. Va sur `/backoffice/evenement/calendar`
2. Vérifie que le calendrier s'affiche
3. Vérifie que les événements apparaissent

### Test 2: Navigation
1. Clique sur "Semaine", "Jour", "Liste"
2. Vérifie que les vues changent
3. Navigue entre les mois

### Test 3: Interaction
1. Clique sur un événement
2. Vérifie que la modal s'affiche
3. Clique sur "Voir détails"

### Test 4: Responsive
1. Réduis la fenêtre du navigateur
2. Vérifie que le calendrier s'adapte
3. Teste sur mobile

---

## 📝 RÉSUMÉ DES COMMANDES

```bash
# Installation
npm install @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction @fullcalendar/list

# Compilation assets
npm run dev

# Mode watch
npm run watch

# Test
php bin/console server:start
# Puis va sur http://localhost:8000/backoffice/evenement/calendar
```

---

## 🎉 RÉSULTAT FINAL

Tu auras:
- ✅ Calendrier interactif avec 4 vues (Mois, Semaine, Jour, Liste)
- ✅ Couleurs par type d'événement
- ✅ Tooltip au survol
- ✅ Modal avec détails au clic
- ✅ Navigation fluide
- ✅ Responsive
- ✅ En français
- ✅ Professionnel et moderne

**Prêt à commencer l'intégration?** 🚀
