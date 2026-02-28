# 📋 Guide d'Organisation Trello - Module Événements

## 🎯 Structure Recommandée des Listes Trello

Voici les 6 listes que vous devez créer dans votre board Trello :

```
1. 📝 BACKLOG (Product Backlog)
2. 📅 À FAIRE (Sprint Backlog - Prêt à démarrer)
3. 🔄 EN COURS (Work in Progress)
4. ✅ À VÉRIFIER (Code Review / Testing)
5. ✔️ TERMINÉ (Done)
6. 🐛 BUGS / AMÉLIORATIONS (Issues)
```

---

## 📝 FORMULATION DES CARTES : Quelle Approche ?

### ✅ APPROCHE RECOMMANDÉE : Format Hybride Professionnel

**Pour les fonctionnalités utilisateur** → User Story format
```
[US-01] Créer une équipe pour un événement
En tant qu'étudiant, je souhaite créer une équipe pour participer à un événement
```

**Pour les tâches techniques** → Format direct et actionnable
```
[TECH] Créer l'entité Equipe.php avec contraintes de validation
[TECH] Implémenter le service EmailService pour les confirmations
[TECH] Configurer le Workflow Bundle pour les transitions d'état
```

**Pour les bugs** → Format descriptif
```
[BUG] Les emails de confirmation ne s'envoient pas
[BUG] Le QR Code ne s'affiche pas dans l'email
```

### ❌ À ÉVITER

- ❌ Trop vague : "Faire les équipes"
- ❌ Trop technique sans contexte : "Ajouter une méthode dans le contrôleur"
- ❌ Trop long : "En tant qu'utilisateur je veux pouvoir créer une équipe avec mes amis..."

---

## 🗂️ LISTE 1 : BACKLOG (Product Backlog)

**Objectif** : Toutes les fonctionnalités à réaliser (pas encore planifiées)

### Cartes à créer :

```
📌 [US-01] Créer une équipe pour un événement
Description : En tant qu'étudiant, je souhaite créer une équipe pour participer à un événement
Story Points : 5 SP
Priorité : HAUTE
Labels : Frontend, Backend, Équipe

📌 [US-02] Rejoindre une équipe existante
Description : En tant qu'étudiant, je souhaite rejoindre une équipe existante avec places disponibles
Story Points : 8 SP
Priorité : HAUTE
Labels : Frontend, Backend, Équipe

📌 [US-03] Soumettre la participation de mon équipe
Description : En tant qu'étudiant, je souhaite soumettre la participation de mon équipe à un événement
Story Points : 5 SP
Priorité : HAUTE
Labels : Frontend, Backend, Participation

📌 [US-04] Acceptation automatique des participations
Description : Le système accepte automatiquement la participation d'une équipe si le nombre maximal d'équipes n'est pas atteint
Story Points : 13 SP
Priorité : CRITIQUE
Labels : Backend, Validation, Logique Métier

📌 [US-05] Envoi d'email de confirmation
Description : Le système envoie un email de confirmation avec pièces jointes (QR Code, Badge PDF, fichier .ics)
Story Points : 8 SP
Priorité : HAUTE
Labels : Backend, Email, Intégration

📌 [US-06] Refus des participations invalides
Description : Le système refuse la participation si elle ne vérifie pas les contraintes et affiche la raison en message
Story Points : 3 SP
Priorité : MOYENNE
Labels : Backend, Validation, UI

📌 [US-07] Voir mes participations acceptées
Description : En tant qu'étudiant, je souhaite voir la liste de mes participations acceptées
Story Points : 3 SP
Priorité : MOYENNE
Labels : Frontend, Backend

📌 [US-08] Email de démarrage d'événement
Description : Le système envoie des emails lors du démarrage d'un événement
Story Points : 5 SP
Priorité : MOYENNE
Labels : Backend, Email, Workflow

📌 [US-09] Email d'annulation d'événement
Description : Le système envoie des emails lors de l'annulation d'un événement
Story Points : 5 SP
Priorité : MOYENNE
Labels : Backend, Email, Workflow

📌 [US-10] Nettoyage des participations refusées
Description : Le système nettoie automatiquement les participations refusées
Story Points : 5 SP
Priorité : BASSE
Labels : Backend, Maintenance
```

---

## 📅 LISTE 2 : À FAIRE (Sprint Backlog)

**Objectif** : Tâches planifiées pour le sprint en cours (prêtes à être prises)

### Comment décomposer une User Story en tâches ?

**Exemple : US-01 (Créer une équipe)**

```
📋 [US-01] Créer une équipe pour un événement (5 SP)
├── ✏️ [TASK] Créer l'entité Equipe.php avec contraintes (4-6 membres)
│   Assigné à : Développeur Backend
│   Temps estimé : 2h
│
├── ✏️ [TASK] Créer le formulaire EquipeFrontType.php
│   Assigné à : Développeur Backend
│   Temps estimé : 1h
│
├── ✏️ [TASK] Créer le contrôleur FrontofficeEquipeController
│   Assigné à : Développeur Backend
│   Temps estimé : 2h
│
├── ✏️ [TASK] Créer les routes /equipe/new et /equipe/new-for-event/{eventId}
│   Assigné à : Développeur Backend
│   Temps estimé : 1h
│
├── ✏️ [TASK] Créer les templates Twig (new.html.twig, show.html.twig)
│   Assigné à : Développeur Frontend
│   Temps estimé : 3h
│
└── ✏️ [TASK] Tester la création d'équipe avec différents scénarios
    Assigné à : Testeur / QA
    Temps estimé : 1h
```


---

## 🔄 LISTE 3 : EN COURS (Work in Progress)

**Objectif** : Tâches actuellement en développement

**Règle importante** : Limiter le nombre de cartes (max 3 par personne) pour éviter le multitasking

### Format des cartes en cours :

```
🔨 [TASK] Créer l'entité Equipe.php avec contraintes
Assigné à : Ahmed
Statut : En développement (50%)
Bloqué par : Rien
Date de début : 20/02/2026
Date estimée de fin : 21/02/2026

Checklist :
☑️ Créer la classe Equipe
☑️ Ajouter les propriétés (id, nom, evenement, etudiants)
☐ Ajouter les contraintes de validation (4-6 membres)
☐ Créer le repository EquipeRepository
☐ Tester l'entité
```

---

## ✅ LISTE 4 : À VÉRIFIER (Code Review / Testing)

**Objectif** : Tâches terminées mais nécessitant une vérification

### Format des cartes à vérifier :

```
🔍 [REVIEW] Créer l'entité Equipe.php avec contraintes
Développé par : Ahmed
À vérifier par : Fatima (Code Review)
Date de soumission : 21/02/2026

Points à vérifier :
☐ Code respecte les standards PSR-12
☐ Contraintes de validation fonctionnent (4-6 membres)
☐ Tests unitaires passent
☐ Documentation du code présente
☐ Pas de code dupliqué

Lien PR : https://github.com/...
```

---

## ✔️ LISTE 5 : TERMINÉ (Done)

**Objectif** : Tâches complétées et validées

### Format des cartes terminées :

```
✅ [DONE] Créer l'entité Equipe.php avec contraintes
Développé par : Ahmed
Vérifié par : Fatima
Date de complétion : 22/02/2026
Sprint : Sprint 1

Résultat :
✅ Entité créée avec toutes les contraintes
✅ Tests unitaires passent (100% coverage)
✅ Code review approuvé
✅ Merged dans la branche main
```

---

## 🐛 LISTE 6 : BUGS / AMÉLIORATIONS

**Objectif** : Problèmes découverts et améliorations futures

### Format des bugs :

```
🐛 [BUG] Les emails de confirmation ne s'envoient pas
Priorité : HAUTE
Découvert par : Youssef
Date : 23/02/2026
Environnement : Production

Description :
Lors de la soumission d'une participation, l'email de confirmation n'est pas envoyé aux membres de l'équipe.

Étapes pour reproduire :
1. Créer une équipe
2. Soumettre une participation
3. Vérifier la boîte email

Résultat attendu : Email reçu
Résultat actuel : Aucun email reçu

Logs d'erreur :
[ERROR] Failed to send email: Connection refused

Assigné à : Sara
```

---

## 🎨 LABELS RECOMMANDÉS DANS TRELLO

Créez ces labels pour catégoriser vos cartes :

```
🔴 CRITIQUE       - Fonctionnalité bloquante
🟠 HAUTE          - Très important
🟡 MOYENNE        - Important
🟢 BASSE          - Nice to have

🔵 Backend        - Code PHP/Symfony
🟣 Frontend       - Templates Twig/CSS/JS
🟤 Base de données - Entités/Migrations
⚫ Email          - Service d'envoi d'emails
🟥 Validation     - Règles métier
🟦 Workflow       - Transitions d'état
🟧 Tests          - Tests unitaires/fonctionnels
🟨 Documentation  - Guides/README
```

---

## 👥 ASSIGNATION DES MEMBRES

### Rôles recommandés :

**Développeur Backend (Ahmed)** :
- Entités (Equipe, Participation, Evenement)
- Contrôleurs (FrontofficeEquipeController, FrontofficeParticipationController)
- Services (EmailService, CertificateService, BadgeService)
- Validation automatique (méthode validateParticipation)

**Développeur Frontend (Fatima)** :
- Templates Twig (new.html.twig, show.html.twig, mes_equipes.html.twig)
- CSS/Styling (Bootstrap, design responsive)
- JavaScript (interactions, validations côté client)

**Développeur Full-Stack (Youssef)** :
- Workflow Bundle (EvenementWorkflowSubscriber)
- Intégration email (configuration Brevo/SendGrid)
- Génération de QR Code et Badge PDF
- Fichier .ics pour calendrier

**Testeur / QA (Sara)** :
- Tests unitaires (PHPUnit)
- Tests fonctionnels (Behat/Symfony Panther)
- Tests d'intégration (API, emails)
- Validation des User Stories

---

## 📊 EXEMPLE COMPLET : US-04 (Validation Automatique)

### Dans BACKLOG :

```
📌 [US-04] Acceptation automatique des participations
Description : Le système accepte automatiquement la participation d'une équipe si le nombre maximal d'équipes n'est pas atteint
Story Points : 13 SP
Priorité : CRITIQUE
Labels : Backend, Validation, Logique Métier
```

### Décomposition en tâches (À FAIRE) :

```
📋 [US-04] Acceptation automatique des participations (13 SP)

├── ✏️ [TASK-04.1] Implémenter la règle 1 : Vérifier si événement annulé
│   Description : Vérifier $evenement->getIsCanceled() et refuser si true
│   Assigné à : Ahmed
│   Temps estimé : 1h
│   Story Points : 2 SP
│
├── ✏️ [TASK-04.2] Implémenter la règle 2 : Vérifier capacité maximale
│   Description : Compter les participations acceptées et comparer avec nbMax
│   Assigné à : Ahmed
│   Temps estimé : 2h
│   Story Points : 4 SP
│
├── ✏️ [TASK-04.3] Implémenter la règle 3 : Vérifier doublon d'étudiant
│   Description : Parcourir les participations et détecter les doublons par ID
│   Assigné à : Ahmed
│   Temps estimé : 3h
│   Story Points : 6 SP
│
├── ✏️ [TASK-04.4] Créer les messages d'erreur personnalisés
│   Description : Retourner des messages clairs pour chaque cas de refus
│   Assigné à : Fatima
│   Temps estimé : 1h
│   Story Points : 1 SP
│
└── ✏️ [TASK-04.5] Tester tous les scénarios de validation
    Description : Tester acceptation, refus (annulé, capacité, doublon)
    Assigné à : Sara
    Temps estimé : 2h
    Story Points : 0 SP (inclus dans les tâches)
```

---

## 🔄 WORKFLOW QUOTIDIEN DANS TRELLO

### Chaque matin (Daily Standup) :

1. **Chaque membre** déplace ses cartes selon l'avancement :
   - "EN COURS" → "À VÉRIFIER" (si terminé)
   - "À FAIRE" → "EN COURS" (si démarré)

2. **Chaque membre** commente sur ses cartes :
   ```
   💬 Ahmed : "J'ai terminé la règle 1 et 2, je commence la règle 3 aujourd'hui"
   💬 Fatima : "Les templates sont prêts, en attente de review"
   💬 Youssef : "Bloqué sur l'intégration email, besoin d'aide"
   ```

3. **Scrum Master** vérifie :
   - Cartes bloquées (ajouter label 🔴 BLOQUÉ)
   - Cartes en retard (ajouter label ⏰ EN RETARD)
   - Burndown Chart (progression du sprint)

### Chaque soir :

1. **Mettre à jour les checklists** dans les cartes
2. **Ajouter des commentaires** sur les difficultés rencontrées
3. **Estimer le temps restant** pour chaque tâche

---

## 📈 POWER-UPS TRELLO RECOMMANDÉS

### 1. **Burndown for Trello**
- Génère automatiquement un Burndown Chart
- Suit la progression des Story Points
- Alerte si vous êtes en retard

### 2. **Card Aging**
- Affiche les cartes qui restent trop longtemps dans une liste
- Aide à identifier les blocages

### 3. **Calendar**
- Vue calendrier des dates d'échéance
- Synchronisation avec Google Calendar

### 4. **Custom Fields**
- Ajouter des champs personnalisés :
  - Story Points
  - Temps estimé
  - Temps réel
  - Priorité

---

## 🎯 CHECKLIST DE DÉMARRAGE TRELLO

### Étape 1 : Créer le Board
```
☐ Créer un board "Module Événements"
☐ Inviter tous les membres de l'équipe
☐ Définir les permissions (qui peut modifier quoi)
```

### Étape 2 : Créer les Listes
```
☐ BACKLOG
☐ À FAIRE
☐ EN COURS
☐ À VÉRIFIER
☐ TERMINÉ
☐ BUGS / AMÉLIORATIONS
```

### Étape 3 : Créer les Labels
```
☐ Priorités (CRITIQUE, HAUTE, MOYENNE, BASSE)
☐ Catégories (Backend, Frontend, Email, etc.)
☐ Statuts (BLOQUÉ, EN RETARD, etc.)
```

### Étape 4 : Créer les Cartes du Backlog
```
☐ US-01 : Créer une équipe
☐ US-02 : Rejoindre une équipe
☐ US-03 : Soumettre participation
☐ US-04 : Acceptation automatique
☐ US-05 : Email de confirmation
☐ US-06 : Refus des participations
☐ US-07 : Voir mes participations
☐ US-08 : Email de démarrage
☐ US-09 : Email d'annulation
☐ US-10 : Nettoyage automatique
```

### Étape 5 : Planifier le Sprint 1
```
☐ Sélectionner les User Stories prioritaires
☐ Décomposer en tâches techniques
☐ Assigner les tâches aux membres
☐ Estimer les temps
☐ Déplacer dans "À FAIRE"
```

---

## 💡 BONNES PRATIQUES TRELLO

### ✅ À FAIRE :

1. **Mettre à jour quotidiennement** : Déplacer les cartes, ajouter des commentaires
2. **Utiliser les checklists** : Décomposer les tâches en sous-tâches
3. **Ajouter des dates d'échéance** : Pour suivre les deadlines
4. **Attacher des fichiers** : Screenshots, documents, liens
5. **Mentionner les membres** : @Ahmed pour demander de l'aide
6. **Utiliser les descriptions** : Expliquer clairement la tâche

### ❌ À ÉVITER :

1. ❌ Laisser des cartes dans "EN COURS" trop longtemps
2. ❌ Créer des cartes trop vagues ("Faire le backend")
3. ❌ Ne pas assigner les cartes (qui fait quoi ?)
4. ❌ Oublier de déplacer les cartes terminées
5. ❌ Ne pas communiquer les blocages
6. ❌ Dupliquer les cartes (une tâche = une carte)

---

## 🎓 CONCLUSION

Avec cette organisation Trello, vous aurez :

✅ **Visibilité complète** sur l'avancement du projet
✅ **Collaboration efficace** entre les membres
✅ **Suivi précis** des tâches et des délais
✅ **Détection rapide** des blocages
✅ **Historique complet** du travail effectué

**Prochaine étape** : Créez votre board Trello et commencez à ajouter les cartes selon ce guide!
