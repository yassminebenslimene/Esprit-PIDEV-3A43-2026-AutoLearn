# Modifications du Module Événement - En cours

## ✅ Modifications terminées (Commit effectué)

### 1. Navbar frontoffice corrigée
- ✅ Suppression des marqueurs de conflit Git (`<<<<<<< HEAD`, `=======`, `>>>>>>>`)
- ✅ Menu propre avec liens Login/Register ou Profile/Logout selon l'état de connexion
- ✅ Lien vers Challenges fonctionnel

### 2. Entité Evenement améliorée
- ✅ Ajout de l'attribut `lieu` (VARCHAR 255, NOT NULL)
- ✅ Getters et setters pour `lieu`
- ✅ Migration créée et exécutée (Version20260212015821)

### 3. Formulaire EvenementType amélioré
- ✅ Ajout du champ `lieu` (visible en création et édition)
- ✅ Ajout du champ `isCanceled` (visible UNIQUEMENT en édition)
- ✅ Option `is_edit` ajoutée au formulaire
- ✅ Contrôleur modifié pour passer `is_edit => true` en mode édition
- ✅ Help text pour expliquer que cocher `isCanceled` annule l'événement

## 🔄 Modifications à faire (Prochaine étape)

### 4. Déplacement d'Equipe vers le frontoffice
**Objectif** : Les étudiants créent leurs équipes depuis le frontoffice

**Changements nécessaires** :
- [ ] Créer `FrontofficeEquipeController` avec routes `/equipe/new`, `/equipe/edit/{id}`
- [ ] Modifier l'entité `Equipe` pour remplacer la relation ManyToMany avec Etudiant par des champs texte simples
  - Remplacer `$etudiants` (Collection) par des champs individuels : `membre1`, `membre2`, `membre3`, `membre4`, `membre5`, `membre6`
  - Chaque champ sera un simple VARCHAR pour le nom de l'étudiant
- [ ] Créer `EquipeFrontType` (nouveau formulaire pour le frontoffice)
  - Champ `nom` (nom de l'équipe)
  - Champs `membre1` à `membre6` (TextType, membres 1-4 requis, 5-6 optionnels)
  - Sélection de l'événement (EntityType)
- [ ] Créer templates frontoffice : `templates/frontoffice/equipe/new.html.twig`, `edit.html.twig`
- [ ] Garder la validation : entre 4 et 6 membres
- [ ] Supprimer les routes backoffice pour Equipe

### 5. Déplacement de Participation vers le frontoffice
**Objectif** : Les étudiants créent leurs participations depuis le frontoffice

**Changements nécessaires** :
- [ ] Créer `FrontofficeParticipationController` avec route `/participation/new`
- [ ] Créer `ParticipationFrontType` (nouveau formulaire)
  - Sélection de l'événement (EntityType)
  - Sélection de l'équipe (EntityType - seulement les équipes de l'utilisateur connecté)
- [ ] Créer template frontoffice : `templates/frontoffice/participation/new.html.twig`
- [ ] Garder la logique de validation automatique :
  - Vérifier la capacité de l'événement (nbMax)
  - Vérifier qu'un étudiant ne participe pas deux fois au même événement
- [ ] Supprimer les routes backoffice pour Participation

### 6. Mise à jour du menu backoffice
- [ ] Supprimer les liens "Équipes" et "Participations" de la sidebar backoffice
- [ ] Garder uniquement "Événements" dans la section Gestion

### 7. Mise à jour du frontoffice
- [ ] Ajouter des liens dans la navbar ou une section pour :
  - "Créer une équipe"
  - "Participer à un événement"
- [ ] Afficher les équipes de l'utilisateur connecté
- [ ] Afficher les participations de l'utilisateur

## ⚠️ Points d'attention

1. **Ne pas toucher aux autres modules** : Challenge, Quiz, User, etc.
2. **Garder la logique métier** : Validation des équipes (4-6 membres), validation des participations
3. **Sécurité** : Seuls les étudiants connectés peuvent créer des équipes et participations
4. **Isolation** : Les modifications ne doivent affecter que le module Événement

## 📝 Commits effectués

1. "Ajout liens Evenements/Equipes/Participations dans sidebar backoffice + guides frontoffice"
2. "Ajout attribut lieu + champ isCanceled (edit only) pour Evenement + correction navbar frontoffice"

## 🎯 Prochaine action

Continuer avec le point 4 : Déplacement d'Equipe vers le frontoffice avec modification de la structure de l'entité.
