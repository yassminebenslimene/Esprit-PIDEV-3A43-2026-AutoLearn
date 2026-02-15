# 📚 Explication Architecture du Projet - Module Événements

## 🎯 Vue d'ensemble du projet

Ce projet est une plateforme d'apprentissage en ligne (AutoLearn) développée avec **Symfony 6.4** qui permet aux étudiants de participer à des événements (hackathons, workshops) en équipe.

---

## 📁 Architecture MVC (Model-View-Controller)

### **1. MODEL (Entités) - `src/Entity/`**

Les entités représentent les tables de la base de données et leurs relations.

#### **Evenement.php**
```php
- Représente un événement (hackathon, workshop, conférence)
- Attributs principaux:
  * titre, description, lieu
  * dateDebut, dateFin
  * type (enum: HACKATHON, WORKSHOP, CONFERENCE)
  * status (enum: PLANIFIE, EN_COURS, TERMINE, ANNULE)
  * nbMax (nombre maximum d'équipes)
  * isCanceled (booléen pour annulation)
  
- Relations:
  * OneToMany avec Equipe (un événement a plusieurs équipes)
  * OneToMany avec Participation (un événement a plusieurs participations)
  
- Méthode importante:
  * updateStatus(): Met à jour automatiquement le statut selon la date
```

#### **Equipe.php**
```php
- Représente une équipe d'étudiants
- Attributs:
  * nom (nom de l'équipe)
  
- Relations:
  * ManyToOne avec Evenement (une équipe appartient à un événement)
  * ManyToMany avec Etudiant (une équipe a 4-6 étudiants)
  
- Validation:
  * @Assert\Count(min: 4, max: 6) sur les étudiants
```

#### **Participation.php**
```php
- Représente l'inscription d'une équipe à un événement
- Attributs:
  * statut (enum: EN_ATTENTE, ACCEPTE, REFUSE)
  
- Relations:
  * ManyToOne avec Equipe
  * ManyToOne avec Evenement
  
- Méthode clé:
  * validateParticipation(): Valide automatiquement selon:
    - Capacité de l'événement (nbMax)
    - Pas de doublon d'étudiants dans le même événement
```

#### **Enums - `src/Enum/`**
```php
TypeEvenement: HACKATHON, WORKSHOP, CONFERENCE
StatutEvenement: PLANIFIE, EN_COURS, TERMINE, ANNULE
StatutParticipation: EN_ATTENTE, ACCEPTE, REFUSE
```

---

### **2. CONTROLLER (Logique métier) - `src/Controller/`**

Les contrôleurs gèrent la logique métier et font le lien entre les entités et les vues.

#### **EvenementController.php (Backoffice)**
```php
Routes:
- /backoffice/evenement/ → Liste des événements (admin)
- /backoffice/evenement/new → Créer un événement
- /backoffice/evenement/{id}/edit → Modifier un événement
- /backoffice/evenement/{id}/delete → Supprimer un événement

Fonctionnalités clés:
- Gestion CRUD complète des événements
- Suppression en cascade (participations → équipes → événement)
- Mise à jour automatique du statut
```

#### **FrontofficeEvenementController.php**
```php
Routes:
- /events → Liste des événements (étudiants)
- /events/{id}/participate → Page de participation
- /events/{equipeId}/join/{eventId} → Rejoindre une équipe

Fonctionnalités:
- Affichage des événements avec countdown
- Calcul des places disponibles
- Liste des équipes participantes
- Flux de participation (rejoindre ou créer équipe)
```

#### **FrontofficeEquipeController.php**
```php
Routes:
- /equipe/mes-equipes → Mes équipes
- /equipe/new → Créer une équipe
- /equipe/new-for-event/{eventId} → Créer équipe pour événement
- /equipe/{id}/edit → Modifier équipe
- /equipe/{id}/delete → Supprimer équipe
- /equipe/{id} → Détails équipe

Fonctionnalités:
- CRUD complet des équipes
- Validation 4-6 étudiants
- Sélection d'étudiants par checkboxes
- Suppression en cascade des participations
```

#### **FrontofficeParticipationController.php**
```php
Routes:
- /participation/mes-participations → Mes participations
- /participation/new → Créer participation
- /participation/new-for-team/{equipeId}/event/{eventId} → Participation automatique
- /participation/{id}/edit → Modifier participation
- /participation/{id}/delete → Supprimer participation

Fonctionnalités:
- CRUD complet des participations
- Validation automatique (nbMax, doublons)
- Affichage du statut (accepté/refusé)
```

---

### **3. VIEW (Templates) - `templates/`**

Les templates Twig génèrent le HTML affiché à l'utilisateur.

#### **Structure des templates:**

```
templates/
├── backoffice/           # Interface admin
│   ├── evenement/
│   │   ├── index.html.twig    # Liste événements
│   │   ├── new.html.twig      # Créer événement
│   │   ├── edit.html.twig     # Modifier événement
│   │   └── show.html.twig     # Détails événement
│   └── ...
│
└── frontoffice/          # Interface étudiants
    ├── base.html.twig         # Template de base (navbar, footer)
    ├── index.html.twig        # Page d'accueil
    │
    ├── evenement/
    │   ├── index.html.twig         # Liste événements avec countdown
    │   └── participate.html.twig   # Page de participation
    │
    ├── equipe/
    │   ├── mes_equipes.html.twig   # Liste mes équipes
    │   ├── new.html.twig           # Créer équipe
    │   ├── edit.html.twig          # Modifier équipe
    │   └── show.html.twig          # Détails équipe
    │
    └── participation/
        ├── mes_participations.html.twig  # Liste participations
        ├── new.html.twig                 # Créer participation
        ├── edit.html.twig                # Modifier participation
        └── show.html.twig                # Détails participation
```

#### **Héritage des templates:**
```twig
{% extends 'frontoffice/base.html.twig' %}
- Tous les templates héritent de base.html.twig
- base.html.twig contient la navbar, footer, et assets CSS/JS
```

---

### **4. FORM (Formulaires) - `src/Form/`**

Les formulaires gèrent la validation et l'affichage des champs.

#### **EvenementType.php**
```php
Champs:
- titre, description, lieu
- type (choix: HACKATHON, WORKSHOP, CONFERENCE)
- dateDebut, dateFin
- nbMax
- isCanceled (uniquement en mode édition)

Option is_edit:
- Permet d'afficher isCanceled seulement en modification
```

#### **EquipeFrontType.php**
```php
Champs:
- nom (texte)
- etudiants (EntityType avec checkboxes)
  * expanded: true → Affiche des checkboxes
  * multiple: true → Sélection multiple
  * Validation: 4-6 étudiants

Pas de champ événement:
- L'événement est défini automatiquement par le contrôleur
```

#### **ParticipationFrontType.php**
```php
Champs:
- equipe (sélection)
- evenement (sélection)

Filtrage:
- Affiche uniquement les équipes de l'utilisateur connecté
```

---

## 🔄 Flux de Participation (Cas d'usage principal)

### **Scénario: Un étudiant veut participer à un hackathon**

```
1. Étudiant → /events
   ↓
2. Clique sur "Participer" pour un événement
   ↓
3. Page /events/{id}/participate
   - Message d'introduction (objectifs: networking, compétences)
   - 2 choix:
     A) Rejoindre une équipe existante (< 6 membres)
     B) Créer une nouvelle équipe
   ↓
4A. Si "Rejoindre équipe":
    - Clique sur "Join This Team"
    - POST /events/{equipeId}/join/{eventId}
    - Ajout à l'équipe
    - Redirection vers "Mes équipes"
   
4B. Si "Créer équipe":
    - Clique sur "Create New Team"
    - GET /equipe/new-for-event/{eventId}
    - Formulaire: nom + sélection 4-6 étudiants (checkboxes)
    - POST → Équipe créée
    ↓
5. Affichage détails équipe avec bouton "Participer"
   ↓
6. Clique "Participate in [Événement]"
   - POST /participation/new-for-team/{equipeId}/event/{eventId}
   - Création participation
   - Validation automatique:
     * Vérification nbMax
     * Vérification doublons étudiants
   ↓
7. Redirection "Mes Participations"
   - Statut: ACCEPTE ou REFUSE
```

---

## 🗄️ Base de Données (Relations)

```sql
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│  Evenement  │────────>│   Equipe    │────────>│ Participation│
│             │ 1     * │             │ 1     * │             │
│ - id        │         │ - id        │         │ - id        │
│ - titre     │         │ - nom       │         │ - statut    │
│ - nbMax     │         │ - evenement │         │ - equipe    │
│ - ...       │         │             │         │ - evenement │
└─────────────┘         └─────────────┘         └─────────────┘
                              │
                              │ ManyToMany
                              ↓
                        ┌─────────────┐
                        │  Etudiant   │
                        │             │
                        │ - id        │
                        │ - nom       │
                        │ - prenom    │
                        └─────────────┘
```

### **Contraintes de clés étrangères:**
```
Participation.equipe_id → Equipe.id
Participation.evenement_id → Evenement.id
Equipe.evenement_id → Evenement.id
equipe_etudiant.equipe_id → Equipe.id
equipe_etudiant.etudiant_id → Etudiant.userId
```

---

## 🔐 Sécurité et Validations

### **Validations métier:**

1. **Équipe:**
   - 4 à 6 étudiants obligatoires
   - Nom obligatoire

2. **Participation:**
   - Validation automatique dans `validateParticipation()`:
     ```php
     - Si nbMax atteint → REFUSE
     - Si étudiant déjà dans une autre équipe du même événement → REFUSE
     - Sinon → ACCEPTE
     ```

3. **Suppression en cascade:**
   ```php
   Événement:
   1. Supprimer participations (via repository)
   2. Supprimer équipes
   3. Supprimer événement
   
   Équipe:
   1. Supprimer participations
   2. Supprimer équipe
   ```

### **Contrôle d'accès:**
```php
#[IsGranted('ROLE_USER')] → Étudiants connectés
#[IsGranted('ROLE_ADMIN')] → Administrateurs

Vérifications dans les contrôleurs:
- L'utilisateur est membre de l'équipe avant modification/suppression
```

---

## 🎨 Design Pattern utilisés

### **1. Repository Pattern**
```php
EvenementRepository, EquipeRepository, ParticipationRepository
- Encapsulent les requêtes à la base de données
- Méthodes personnalisées pour requêtes complexes
```

### **2. Form Type Pattern**
```php
EvenementType, EquipeFrontType, ParticipationFrontType
- Séparent la logique de formulaire du contrôleur
- Réutilisables et testables
```

### **3. Enum Pattern**
```php
TypeEvenement, StatutEvenement, StatutParticipation
- Valeurs prédéfinies et typées
- Évite les erreurs de saisie
```

### **4. Service Layer (implicite)**
```php
validateParticipation() dans Participation
updateStatus() dans Evenement
- Logique métier dans les entités
```

---

## 🚀 Technologies utilisées

- **Framework**: Symfony 6.4
- **ORM**: Doctrine
- **Template Engine**: Twig
- **Base de données**: MySQL
- **Frontend**: Bootstrap 5 + CSS personnalisé
- **JavaScript**: Vanilla JS (compteurs, animations)

---

## 📊 Points techniques à mentionner

### **1. Gestion des relations Doctrine:**
```php
ManyToOne, OneToMany, ManyToMany
- Cascade operations
- Lazy loading
- Bidirectional relations
```

### **2. Validation Symfony:**
```php
@Assert\NotBlank
@Assert\Count(min: 4, max: 6)
@Assert\Expression
- Validation côté serveur
```

### **3. Routing:**
```php
#[Route('/events/{id}/participate')]
- Annotations PHP 8
- Paramètres dynamiques
- Méthodes HTTP (GET, POST)
```

### **4. Twig:**
```php
{% extends %}, {% block %}
{{ form_start(form) }}
{% for item in items %}
- Héritage de templates
- Helpers de formulaires
- Boucles et conditions
```

---

## 💡 Questions probables des profs

**Q: Pourquoi ManyToMany entre Equipe et Etudiant?**
R: Une équipe a plusieurs étudiants, et un étudiant peut être dans plusieurs équipes (pour différents événements).

**Q: Pourquoi validateParticipation() dans l'entité?**
R: C'est de la logique métier liée à l'entité. Alternative: créer un service dédié.

**Q: Comment gérez-vous les contraintes de clés étrangères?**
R: Suppression en cascade avec flush() intermédiaires pour respecter l'ordre des dépendances.

**Q: Pourquoi 2 contrôleurs (Backoffice et Frontoffice)?**
R: Séparation des responsabilités: admin vs étudiants. Différentes interfaces et permissions.

**Q: Comment empêcher les doublons d'étudiants?**
R: Dans validateParticipation(), on vérifie si un étudiant de l'équipe est déjà dans une autre équipe acceptée du même événement.

---

## 📝 Améliorations possibles (à mentionner)

1. **Services dédiés**: Extraire la logique métier dans des services
2. **Events Symfony**: Utiliser des événements pour la validation
3. **API REST**: Exposer une API pour mobile
4. **Tests unitaires**: Tester la logique de validation
5. **Notifications**: Email lors de l'acceptation/refus
6. **Cache**: Mettre en cache la liste des événements

---

**Bonne chance pour ta soutenance! 🎓**
