# Module Événement - Guide d'utilisation

## ✅ Configuration terminée

Le module Événement a été intégré avec succès dans votre application. Voici ce qui a été fait:

### 🎯 Entités créées
- **Evenement**: Gestion des événements (Conférence, Hackathon, Workshop)
- **Equipe**: Gestion des équipes (4 à 6 étudiants par équipe)
- **Participation**: Gestion des participations des équipes aux événements

### 🔗 Relations
- Un Événement peut avoir plusieurs Équipes
- Un Événement peut avoir plusieurs Participations
- Une Équipe appartient à un Événement
- Une Équipe contient plusieurs Étudiants (4 à 6)
- Une Participation lie une Équipe à un Événement

### 📋 Fonctionnalités

#### Backoffice (Admin uniquement)
- **Gestion des Événements**: `/backoffice/evenement`
  - Créer, modifier, supprimer des événements
  - Voir les détails avec nombre d'équipes et participations
  - Statut automatique (Planifié, En cours, Annulé)

- **Gestion des Équipes**: `/backoffice/equipe`
  - Créer des équipes avec 4 à 6 étudiants
  - Associer une équipe à un événement
  - Voir les membres de chaque équipe

- **Gestion des Participations**: `/backoffice/participation`
  - Créer des participations
  - Validation automatique selon les règles:
    * Vérification du nombre maximum d'équipes
    * Vérification des doublons d'étudiants
  - Statuts: En attente, Accepté, Refusé

#### Frontoffice (Public)
- **Section Events**: Affichage de tous les événements
  - Titre, type, dates
  - Calcul automatique des places disponibles
  - Affichage: nbMax - participations acceptées

- **Section Team**: Affichage de toutes les équipes
  - Nom de l'équipe
  - Événement associé
  - Nombre de membres

### 🚀 Comment tester

1. **Démarrer le serveur**:
   ```bash
   symfony server:start
   ```
   ou
   ```bash
   php -S localhost:8000 -t public
   ```

2. **Accéder au Backoffice**:
   - URL: `http://localhost:8000/backoffice`
   - Connectez-vous en tant qu'Admin
   - Menu latéral: Gestion > Événements / Équipes / Participations

3. **Créer un événement**:
   - Cliquez sur "Ajouter un événement"
   - Remplissez: Titre, Description, Type, Dates, Nombre max d'équipes
   - Le statut sera automatiquement géré

4. **Créer une équipe**:
   - Cliquez sur "Ajouter une équipe"
   - Sélectionnez un événement
   - Choisissez 4 à 6 étudiants (Ctrl+clic pour sélection multiple)

5. **Créer une participation**:
   - Cliquez sur "Ajouter une participation"
   - Sélectionnez une équipe et un événement
   - Le statut sera validé automatiquement

6. **Voir le Frontoffice**:
   - URL: `http://localhost:8000/`
   - Scrollez vers "Events" pour voir les événements
   - Scrollez vers "Team" pour voir les équipes

### 📊 Règles de validation

#### Événement
- Titre obligatoire
- Description obligatoire
- Date de fin >= Date de début
- Nombre max d'équipes > 0

#### Équipe
- Nom obligatoire
- 4 à 6 étudiants obligatoires
- Événement obligatoire

#### Participation
- Validation automatique:
  * ✅ Accepté si places disponibles ET pas de doublon d'étudiants
  * ❌ Refusé si événement complet OU étudiant déjà inscrit

### 🎨 Templates créés

**Backoffice**:
- `templates/backoffice/evenement/` (index, new, edit, show)
- `templates/backoffice/equipe/` (index, new, edit, show)
- `templates/backoffice/participation/` (index, new, edit, show)

**Frontoffice**:
- Section Events intégrée dans `templates/frontoffice/index.html.twig`
- Section Team intégrée dans `templates/frontoffice/index.html.twig`

### 🔧 Contrôleurs

- `EvenementController`: Routes `/backoffice/evenement/*`
- `EquipeController`: Routes `/backoffice/equipe/*`
- `ParticipationController`: Routes `/backoffice/participation/*`
- `FrontofficeController`: Passe les données aux templates

### ✨ Fonctionnalités automatiques

1. **Statut d'événement**: Mis à jour automatiquement selon la date
2. **Validation de participation**: Vérification automatique des règles
3. **Calcul des places**: Affichage dynamique des places disponibles
4. **Messages flash**: Confirmation des actions (succès/erreur)

### 🐛 Résolution des problèmes

Si vous rencontrez des erreurs:

1. **Vider le cache**:
   ```bash
   php bin/console cache:clear
   ```

2. **Vérifier la base de données**:
   ```bash
   php bin/console doctrine:schema:validate
   ```

3. **Voir les routes**:
   ```bash
   php bin/console debug:router | grep -E "(evenement|equipe|participation)"
   ```

### 📝 Notes importantes

- ✅ Aucune modification des autres modules
- ✅ User.php non modifié
- ✅ Compatibilité avec le système existant
- ✅ Templates utilisant le style backoffice existant
- ✅ Intégration dans le frontoffice existant

### 🎯 Prochaines étapes suggérées

1. Créer quelques événements de test
2. Créer des équipes avec des étudiants
3. Tester les participations et la validation automatique
4. Vérifier l'affichage dans le frontoffice

Bon test! 🚀
