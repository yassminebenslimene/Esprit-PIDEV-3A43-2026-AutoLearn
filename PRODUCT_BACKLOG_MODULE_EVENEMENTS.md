# 📋 PRODUCT BACKLOG - Module Gestion des Événements

## 🎯 Vue d'Ensemble du Module

**Module**: Gestion des Événements  
**Objectif**: Permettre aux administrateurs de créer et gérer des événements (Conférences, Hackathons, Workshops) et aux étudiants de participer en équipes avec système de feedback et analyse AI.

**Acteurs**:
- **Admin**: Gère les événements, participations, génère des rapports AI
- **Étudiant**: Consulte les événements, crée/rejoint des équipes, participe, donne des feedbacks
- **Système**: Valide automatiquement les participations, envoie des emails, gère les workflows, génère des certificats

---

## 📊 PRODUCT BACKLOG COMPLET

| ID | User Story | Acteur | Priorité | Points |
|----|------------|--------|----------|--------|
| **US-5.1** | En tant qu'Admin, je souhaite créer un événement afin de permettre aux étudiants d'y participer | Admin | HAUTE | 75 |
| **US-5.2** | En tant qu'Admin, je souhaite modifier un événement afin de mettre à jour ses informations | Admin | HAUTE | 75 |
| **US-5.3** | En tant qu'Admin, je souhaite supprimer un événement afin de retirer un événement obsolète de la plateforme | Admin | HAUTE | 75 |
| **US-5.4** | En tant qu'Admin, je souhaite changer le statut d'un événement (Planifié, En cours, Annulé) afin de gérer son cycle de vie | Admin | HAUTE | 75 |
| **US-5.5** | En tant qu'Admin, je souhaite consulter la liste des événements afin d'avoir une vue globale des événements créés | Admin | HAUTE | 75 |
| **US-5.6** | En tant qu'Admin, je souhaite définir un nombre maximum d'équipes pour un événement afin de limiter le nombre de participants | Admin | HAUTE | 75 |
| **US-5.7** | En tant qu'Étudiant, je souhaite consulter les événements disponibles afin de choisir celui auquel participer | Étudiant | HAUTE | 60 |
| **US-5.8** | En tant qu'Étudiant, je souhaite créer une équipe afin de participer à un événement | Étudiant | HAUTE | 75 |
| **US-5.9** | En tant qu'Étudiant, je souhaite ajouter des membres à mon équipe afin de constituer un groupe valide | Étudiant | HAUTE | 75 |
| **US-5.10** | En tant qu'Étudiant, je souhaite modifier les informations de mon équipe afin de les mettre à jour | Étudiant | MOYENNE | 75 |
| **US-5.11** | En tant qu'Étudiant, je souhaite supprimer mon équipe afin de ne plus participer avec celle-ci | Étudiant | MOYENNE | 75 |
| **US-5.12** | En tant qu'Étudiant, je souhaite inscrire mon équipe à un événement afin de participer à la compétition | Étudiant | HAUTE | 75 |
| **US-5.13** | Le système accepte automatiquement la participation d'une équipe si le nombre maximal d'équipes n'est pas atteint afin d'assurer une gestion équitable | Système | HAUTE | 75 |
| **US-5.14** | Le système refuse la participation d'une équipe automatiquement si un étudiant est un membre dans deux équipes différentes pour le même événement | Système | HAUTE | 75 |
| **US-5.15** | En tant qu'Étudiant, je souhaite consulter le statut de ma participation afin de savoir si mon équipe est acceptée ou refusée | Étudiant | HAUTE | 75 |
| **US-5.16** | Le système empêche un étudiant de participer sans équipe afin de respecter les règles de la plateforme | Système | HAUTE | 75 |
| **US-5.17** | Le Système empêche toute nouvelle participation à un événement annulé afin de garantir la cohérence des données | Système | HAUTE | 75 |
| **US-5.18** | Le Système limite le nombre d'équipes acceptées à la capacité maximale définie afin de respecter les contraintes de l'événement | Système | HAUTE | 75 |
| **US-5.19** | Le Système vérifie que chaque équipe respecte le nombre minimum et maximum de membres afin d'assurer une participation valide | Système | HAUTE | 75 |
| **US-5.20** | En tant qu'Admin, je souhaite consulter les participations en attente afin de les valider ou refuser manuellement | Admin | MOYENNE | 60 |
| **US-5.21** | En tant qu'Admin, je souhaite accepter une participation afin de confirmer l'inscription d'une équipe | Admin | MOYENNE | 60 |
| **US-5.22** | En tant qu'Admin, je souhaite refuser une participation afin de rejeter l'inscription d'une équipe | Admin | MOYENNE | 60 |
| **US-5.23** | Le système envoie automatiquement un email de confirmation avec QR code et badge PDF lorsqu'une participation est acceptée | Système | HAUTE | 90 |
| **US-5.24** | Le système envoie automatiquement un email d'annulation à tous les participants lorsqu'un événement est annulé | Système | HAUTE | 90 |
| **US-5.25** | Le système envoie automatiquement un email de démarrage à tous les participants lorsqu'un événement commence | Système | HAUTE | 90 |
| **US-5.26** | Le système envoie automatiquement un email de rappel 3 jours avant l'événement | Système | MOYENNE | 75 |
| **US-5.27** | Le système génère et envoie automatiquement des certificats PDF après la fin d'un événement | Système | HAUTE | 90 |
| **US-5.28** | En tant qu'Étudiant, je souhaite consulter un calendrier visuel des événements afin de voir les dates importantes | Étudiant | MOYENNE | 75 |
| **US-5.29** | En tant qu'Étudiant, je souhaite voir la météo prévue pour l'événement afin de me préparer en conséquence | Étudiant | BASSE | 60 |
| **US-5.30** | En tant qu'Étudiant, je souhaite donner mon feedback après un événement afin de partager mon expérience | Étudiant | HAUTE | 90 |
| **US-5.31** | En tant qu'Étudiant, je souhaite évaluer l'événement par catégories (organisation, contenu, lieu, animation) afin de donner un feedback détaillé | Étudiant | HAUTE | 75 |
| **US-5.32** | En tant qu'Admin, je souhaite consulter les statistiques de feedbacks par type d'événement afin d'analyser la satisfaction | Admin | HAUTE | 75 |
| **US-5.33** | En tant qu'Admin, je souhaite générer un rapport d'analyse AI basé sur les feedbacks afin d'obtenir des insights | Admin | HAUTE | 90 |
| **US-5.34** | En tant qu'Admin, je souhaite générer des recommandations d'événements via AI afin de planifier de futurs événements | Admin | HAUTE | 90 |
| **US-5.35** | En tant qu'Admin, je souhaite générer des suggestions d'amélioration via AI afin d'optimiser les événements futurs | Admin | HAUTE | 90 |
| **US-5.36** | Le système gère automatiquement les transitions d'états via Workflow (planifié → en cours → terminé) | Système | HAUTE | 90 |
| **US-5.37** | Le système empêche les participations aux événements en cours ou terminés afin de respecter les règles métier | Système | HAUTE | 75 |
| **US-5.38** | Le système log toutes les transitions d'états avec traçabilité (qui, quand, quoi) afin d'assurer un audit complet | Système | HAUTE | 75 |
| **US-5.39** | En tant qu'Étudiant, je souhaite rejoindre une équipe existante afin de participer sans créer une nouvelle équipe | Étudiant | MOYENNE | 60 |
| **US-5.40** | En tant qu'Étudiant, je souhaite voir les équipes participantes à un événement afin de connaître la compétition | Étudiant | BASSE | 45 |
| **US-5.41** | En tant qu'Admin, je souhaite voir les détails d'un événement (participations, équipes, statistiques) afin d'avoir une vue complète | Admin | HAUTE | 60 |
| **US-5.42** | Le système supprime automatiquement les participations refusées afin de garder une base de données propre | Système | MOYENNE | 45 |
| **US-5.43** | Le système génère automatiquement un fichier .ics pour ajouter l'événement au calendrier personnel | Système | BASSE | 45 |
| **US-5.44** | Le système valide automatiquement les contraintes de dates (date fin >= date début) | Système | HAUTE | 30 |
| **US-5.45** | Le système valide automatiquement les contraintes de taille d'équipe (4-6 membres) | Système | HAUTE | 30 |

---

## 📈 Statistiques du Product Backlog

**Total User Stories**: 45  
**Points Totaux**: 3,240 points

**Répartition par Priorité**:
- HAUTE: 32 US (71%)
- MOYENNE: 10 US (22%)
- BASSE: 3 US (7%)

**Répartition par Acteur**:
- Admin: 13 US (29%)
- Étudiant: 13 US (29%)
- Système: 19 US (42%)

**Répartition par Catégorie**:
- CRUD Événements: 6 US
- CRUD Équipes: 5 US
- Gestion Participations: 10 US
- Emails Automatiques: 5 US
- Feedbacks & AI: 6 US
- Workflow & États: 4 US
- Validations Métier: 6 US
- Fonctionnalités Avancées: 3 US

---

## 🎯 Priorisation Recommandée

### Sprint 1 - Fonctionnalités Core (US-5.1 à US-5.19)
**Objectif**: CRUD événements, équipes, participations + validations métier de base

### Sprint 2 - Gestion Participations & Emails (US-5.20 à US-5.27)
**Objectif**: Validation manuelle, emails automatiques, certificats

### Sprint 3 - Feedbacks & AI (US-5.30 à US-5.35)
**Objectif**: Système de feedback, analyse AI, rapports

### Sprint 4 - Workflow & Avancé (US-5.36 à US-5.45)
**Objectif**: Workflow Component, calendrier, météo, optimisations

---

## 📝 Notes Importantes

### Règles Métier Critiques

1. **Équipes**: 4 à 6 membres obligatoires
2. **Participations**: Un étudiant ne peut pas être dans 2 équipes pour le même événement
3. **Capacité**: Nombre maximum d'équipes respecté automatiquement
4. **États**: Workflow gère automatiquement les transitions (planifié → en cours → terminé)
5. **Emails**: Envoyés automatiquement à tous les membres des équipes participantes
6. **Feedbacks**: Collectés après l'événement, analysés par AI (Mistral-7B)
7. **Certificats**: Générés et envoyés automatiquement après la fin

### Technologies Utilisées

- **Backend**: Symfony 6.4, PHP 8.2
- **Workflow**: symfony/workflow (State Machine)
- **Emails**: SendGrid API
- **AI**: Hugging Face API (Mistral-7B)
- **Météo**: OpenWeatherMap API
- **Calendrier**: tattali/calendar-bundle + FullCalendar
- **PDF**: dompdf (certificats, badges)
- **QR Code**: endroid/qr-code

---

**Date de Création**: 22 Février 2026  
**Version**: 1.0  
**Statut**: ✅ Complet et Validé
