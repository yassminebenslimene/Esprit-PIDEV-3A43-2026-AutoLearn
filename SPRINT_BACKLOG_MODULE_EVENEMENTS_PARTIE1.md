# 🏃 SPRINT BACKLOG - Module Gestion des Événements (PARTIE 1/3)

## 📋 User Stories US-5.1 à US-5.15

---

## US-5.1: Créer un événement (Admin)

**User Story**: En tant qu'Admin, je souhaite créer un événement afin de permettre aux étudiants d'y participer

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.1.1 | Créer l'entité Evenement avec propriétés (titre, lieu, description, type, dateDebut, dateFin, nbMax, status, isCanceled, workflowStatus) | 30 min | Développeur Backend |
| T5.1.2 | Créer l'enum TypeEvenement (Conference, Hackathon, Workshop) | 10 min | Développeur Backend |
| T5.1.3 | Créer l'enum StatutEvenement (Planifié, En cours, Passé, Annulé) | 10 min | Développeur Backend |
| T5.1.4 | Ajouter les validations Symfony (Assert\NotBlank, Assert\Length, Assert\GreaterThan, Assert\Expression) | 20 min | Développeur Backend |
| T5.1.5 | Créer le repository EvenementRepository | 10 min | Développeur Backend |
| T5.1.6 | Créer la migration Doctrine pour la table evenement | 15 min | Développeur Backend |
| T5.1.7 | Créer le formulaire EvenementType avec tous les champs | 25 min | Développeur Backend |
| T5.1.8 | Créer le contrôleur EvenementController avec route /backoffice/evenement/new | 20 min | Développeur Backend |
| T5.1.9 | Implémenter la méthode new() avec gestion du formulaire et persist | 25 min | Développeur Backend |
| T5.1.10 | Créer le template Twig new.html.twig avec formulaire stylisé | 45 min | Développeur Frontend |
| T5.1.11 | Ajouter les messages flash de succès/erreur | 10 min | Développeur Backend |
| T5.1.12 | Tester la création d'événement avec données valides | 20 min | Testeur |
| T5.1.13 | Tester la validation des contraintes (dates, longueurs, etc.) | 25 min | Testeur |

**Total Estimation**: 4h 25min

---

## US-5.2: Modifier un événement (Admin)

**User Story**: En tant qu'Admin, je souhaite modifier un événement afin de mettre à jour ses informations

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.2.1 | Créer la route /backoffice/evenement/{id}/edit dans EvenementController | 10 min | Développeur Backend |
| T5.2.2 | Implémenter la méthode edit() avec chargement de l'événement existant | 20 min | Développeur Backend |
| T5.2.3 | Gérer la soumission du formulaire et flush des modifications | 15 min | Développeur Backend |
| T5.2.4 | Créer le template edit.html.twig avec formulaire pré-rempli | 35 min | Développeur Frontend |
| T5.2.5 | Ajouter le bouton "Annuler l'événement" (workflow) dans le formulaire | 20 min | Développeur Frontend |
| T5.2.6 | Implémenter la logique de mise à jour automatique du statut (updateStatus()) | 25 min | Développeur Backend |
| T5.2.7 | Ajouter les messages flash de succès | 10 min | Développeur Backend |
| T5.2.8 | Tester la modification avec données valides | 20 min | Testeur |
| T5.2.9 | Tester la validation des contraintes après modification | 20 min | Testeur |

**Total Estimation**: 2h 55min

---

## US-5.3: Supprimer un événement (Admin)

**User Story**: En tant qu'Admin, je souhaite supprimer un événement afin de retirer un événement obsolète de la plateforme

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.3.1 | Créer la route /backoffice/evenement/{id}/delete | 10 min | Développeur Backend |
| T5.3.2 | Implémenter la méthode delete() avec suppression en cascade | 30 min | Développeur Backend |
| T5.3.3 | Gérer l'ordre de suppression (participations → équipes → événement) pour respecter les contraintes FK | 35 min | Développeur Backend |
| T5.3.4 | Ajouter le bouton "Supprimer" dans index.html.twig avec confirmation JavaScript | 20 min | Développeur Frontend |
| T5.3.5 | Styliser le bouton avec gradient et icône | 15 min | Développeur Frontend |
| T5.3.6 | Ajouter message de confirmation détaillé (avertissement cascade) | 15 min | Développeur Frontend |
| T5.3.7 | Ajouter message flash de succès après suppression | 10 min | Développeur Backend |
| T5.3.8 | Tester la suppression d'un événement sans participations | 15 min | Testeur |
| T5.3.9 | Tester la suppression en cascade (événement + équipes + participations) | 25 min | Testeur |

**Total Estimation**: 2h 55min

---

## US-5.4: Changer le statut d'un événement (Admin)

**User Story**: En tant qu'Admin, je souhaite changer le statut d'un événement (Planifié, En cours, Annulé) afin de gérer son cycle de vie

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.4.1 | Installer symfony/workflow via composer | 10 min | Développeur Backend |
| T5.4.2 | Créer le fichier de configuration config/packages/workflow.yaml | 25 min | Développeur Backend |
| T5.4.3 | Définir les places (planifie, en_cours, termine, annule) | 15 min | Développeur Backend |
| T5.4.4 | Définir les transitions (demarrer, terminer, annuler) avec métadonnées | 20 min | Développeur Backend |
| T5.4.5 | Ajouter la propriété workflowStatus dans l'entité Evenement | 15 min | Développeur Backend |
| T5.4.6 | Créer la migration pour ajouter la colonne workflow_status | 10 min | Développeur Backend |
| T5.4.7 | Implémenter la méthode syncStatusFromWorkflow() pour synchroniser les enums | 20 min | Développeur Backend |
| T5.4.8 | Créer la route /backoffice/evenement/{id}/annuler (POST) | 15 min | Développeur Backend |
| T5.4.9 | Implémenter la méthode annuler() avec vérification workflow->can() | 25 min | Développeur Backend |
| T5.4.10 | Injecter WorkflowInterface dans le constructeur du contrôleur | 10 min | Développeur Backend |
| T5.4.11 | Ajouter le bouton "Annuler" dans index.html.twig avec formulaire POST | 20 min | Développeur Frontend |
| T5.4.12 | Styliser le bouton avec gradient rouge et icône ❌ | 15 min | Développeur Frontend |
| T5.4.13 | Afficher conditionnellement le bouton (seulement si can_annuler) | 15 min | Développeur Frontend |
| T5.4.14 | Tester l'annulation manuelle d'un événement planifié | 20 min | Testeur |
| T5.4.15 | Tester que le bouton disparaît après annulation | 15 min | Testeur |

**Total Estimation**: 4h 10min

---

## US-5.5: Consulter la liste des événements (Admin)

**User Story**: En tant qu'Admin, je souhaite consulter la liste des événements afin d'avoir une vue globale des événements créés

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.5.1 | Créer la route /backoffice/evenement (GET) | 10 min | Développeur Backend |
| T5.5.2 | Implémenter la méthode index() avec findAll() | 15 min | Développeur Backend |
| T5.5.3 | Ajouter la mise à jour automatique des statuts dans index() | 20 min | Développeur Backend |
| T5.5.4 | Récupérer les statistiques de feedbacks via FeedbackAnalyticsService | 15 min | Développeur Backend |
| T5.5.5 | Créer le template index.html.twig avec tableau des événements | 45 min | Développeur Frontend |
| T5.5.6 | Afficher les colonnes (titre, type, dates, statut, places, actions) | 30 min | Développeur Frontend |
| T5.5.7 | Styliser les badges de statut avec couleurs (planifié=bleu, en cours=vert, etc.) | 25 min | Développeur Frontend |
| T5.5.8 | Ajouter les boutons d'action (Voir, Modifier, Annuler, Supprimer) | 30 min | Développeur Frontend |
| T5.5.9 | Ajouter la section "Statistiques & Rapports AI" en haut | 35 min | Développeur Frontend |
| T5.5.10 | Implémenter les boutons de génération de rapports AI (AJAX) | 40 min | Développeur Frontend |
| T5.5.11 | Ajouter le loading spinner pendant génération AI | 20 min | Développeur Frontend |
| T5.5.12 | Tester l'affichage de la liste avec plusieurs événements | 20 min | Testeur |
| T5.5.13 | Tester l'affichage des différents statuts | 15 min | Testeur |

**Total Estimation**: 5h 20min

---

## US-5.6: Définir un nombre maximum d'équipes (Admin)

**User Story**: En tant qu'Admin, je souhaite définir un nombre maximum d'équipes pour un événement afin de limiter le nombre de participants

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.6.1 | Ajouter la propriété nbMax dans l'entité Evenement | 10 min | Développeur Backend |
| T5.6.2 | Ajouter les validations (NotBlank, Positive, Range 1-100) | 15 min | Développeur Backend |
| T5.6.3 | Ajouter le champ nbMax dans EvenementType (IntegerType) | 10 min | Développeur Backend |
| T5.6.4 | Afficher le champ dans les formulaires new.html.twig et edit.html.twig | 15 min | Développeur Frontend |
| T5.6.5 | Afficher nbMax dans la colonne "Places Max" du tableau index.html.twig | 10 min | Développeur Frontend |
| T5.6.6 | Tester la création avec nbMax valide (ex: 10) | 10 min | Testeur |
| T5.6.7 | Tester la validation (nbMax doit être entre 1 et 100) | 15 min | Testeur |

**Total Estimation**: 1h 25min

---

## US-5.7: Consulter les événements disponibles (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite consulter les événements disponibles afin de choisir celui auquel participer

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.7.1 | Créer FrontofficeEvenementController avec route /events | 15 min | Développeur Backend |
| T5.7.2 | Implémenter la méthode index() avec récupération de tous les événements | 20 min | Développeur Backend |
| T5.7.3 | Calculer les places disponibles pour chaque événement | 25 min | Développeur Backend |
| T5.7.4 | Récupérer les équipes participantes pour chaque événement | 20 min | Développeur Backend |
| T5.7.5 | Intégrer WeatherService pour afficher la météo | 30 min | Développeur Backend |
| T5.7.6 | Créer le template frontoffice/evenement/index.html.twig | 60 min | Développeur Frontend |
| T5.7.7 | Styliser les cartes d'événements avec design moderne (gradients, ombres) | 45 min | Développeur Frontend |
| T5.7.8 | Implémenter l'accordéon expand/collapse pour les détails | 35 min | Développeur Frontend |
| T5.7.9 | Afficher le countdown (jours restants) pour chaque événement | 25 min | Développeur Frontend |
| T5.7.10 | Afficher la météo prévue avec émojis et couleurs | 30 min | Développeur Frontend |
| T5.7.11 | Afficher les équipes participantes avec mini-cartes | 35 min | Développeur Frontend |
| T5.7.12 | Afficher le bouton "Participer" conditionnellement (si places disponibles) | 20 min | Développeur Frontend |
| T5.7.13 | Afficher le badge "CANCELLED" pour événements annulés | 15 min | Développeur Frontend |
| T5.7.14 | Ajouter le lien vers le calendrier en haut de page | 10 min | Développeur Frontend |
| T5.7.15 | Tester l'affichage avec plusieurs événements | 20 min | Testeur |
| T5.7.16 | Tester l'affichage responsive (mobile, tablette) | 25 min | Testeur |

**Total Estimation**: 7h 10min

---

## US-5.8: Créer une équipe (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite créer une équipe afin de participer à un événement

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.8.1 | Créer l'entité Equipe avec propriétés (nom, evenement, etudiants) | 20 min | Développeur Backend |
| T5.8.2 | Ajouter la relation ManyToOne avec Evenement | 15 min | Développeur Backend |
| T5.8.3 | Ajouter la relation ManyToMany avec Etudiant (table de jointure) | 25 min | Développeur Backend |
| T5.8.4 | Ajouter la validation Count (min: 4, max: 6 membres) | 15 min | Développeur Backend |
| T5.8.5 | Créer le repository EquipeRepository | 10 min | Développeur Backend |
| T5.8.6 | Créer la migration pour la table equipe et equipe_etudiant | 20 min | Développeur Backend |
| T5.8.7 | Créer le formulaire EquipeType avec sélection d'étudiants | 30 min | Développeur Backend |
| T5.8.8 | Créer FrontofficeEquipeController avec route /equipe/new | 15 min | Développeur Backend |
| T5.8.9 | Implémenter la méthode new() avec création d'équipe | 25 min | Développeur Backend |
| T5.8.10 | Créer automatiquement une participation lors de la création d'équipe | 30 min | Développeur Backend |
| T5.8.11 | Créer le template frontoffice/equipe/new.html.twig | 45 min | Développeur Frontend |
| T5.8.12 | Styliser le formulaire avec design moderne | 35 min | Développeur Frontend |
| T5.8.13 | Ajouter la sélection multiple d'étudiants avec recherche | 40 min | Développeur Frontend |
| T5.8.14 | Tester la création d'équipe avec 4 membres | 15 min | Testeur |
| T5.8.15 | Tester la validation (minimum 4, maximum 6 membres) | 20 min | Testeur |

**Total Estimation**: 6h 00min

---

## US-5.9: Ajouter des membres à mon équipe (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite ajouter des membres à mon équipe afin de constituer un groupe valide

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.9.1 | Créer la route /equipe/{id}/edit | 10 min | Développeur Backend |
| T5.9.2 | Implémenter la méthode edit() avec modification des membres | 25 min | Développeur Backend |
| T5.9.3 | Implémenter addEtudiant() et removeEtudiant() dans l'entité Equipe | 20 min | Développeur Backend |
| T5.9.4 | Créer le template frontoffice/equipe/edit.html.twig | 35 min | Développeur Frontend |
| T5.9.5 | Afficher la liste actuelle des membres avec bouton "Retirer" | 30 min | Développeur Frontend |
| T5.9.6 | Ajouter un formulaire pour ajouter de nouveaux membres | 30 min | Développeur Frontend |
| T5.9.7 | Valider que l'équipe reste entre 4 et 6 membres | 20 min | Développeur Backend |
| T5.9.8 | Tester l'ajout d'un membre à une équipe de 4 | 15 min | Testeur |
| T5.9.9 | Tester qu'on ne peut pas dépasser 6 membres | 15 min | Testeur |

**Total Estimation**: 3h 20min

---

## US-5.10: Modifier les informations de mon équipe (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite modifier les informations de mon équipe afin de les mettre à jour

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.10.1 | Réutiliser la route /equipe/{id}/edit existante | 5 min | Développeur Backend |
| T5.10.2 | Permettre la modification du nom de l'équipe | 15 min | Développeur Backend |
| T5.10.3 | Afficher le champ "nom" dans edit.html.twig | 10 min | Développeur Frontend |
| T5.10.4 | Ajouter la validation du nom (NotBlank) | 10 min | Développeur Backend |
| T5.10.5 | Tester la modification du nom d'équipe | 15 min | Testeur |

**Total Estimation**: 55min

---

## US-5.11: Supprimer mon équipe (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite supprimer mon équipe afin de ne plus participer avec celle-ci

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.11.1 | Créer la route /equipe/{id}/delete | 10 min | Développeur Backend |
| T5.11.2 | Implémenter la méthode delete() avec suppression de la participation associée | 25 min | Développeur Backend |
| T5.11.3 | Ajouter le bouton "Supprimer mon équipe" dans edit.html.twig | 15 min | Développeur Frontend |
| T5.11.4 | Ajouter une confirmation JavaScript avant suppression | 15 min | Développeur Frontend |
| T5.11.5 | Tester la suppression d'une équipe | 15 min | Testeur |
| T5.11.6 | Vérifier que la participation est aussi supprimée | 15 min | Testeur |

**Total Estimation**: 1h 35min

---

## US-5.12: Inscrire mon équipe à un événement (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite inscrire mon équipe à un événement afin de participer à la compétition

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.12.1 | Créer l'entité Participation avec relations (equipe, evenement, statut) | 20 min | Développeur Backend |
| T5.12.2 | Créer l'enum StatutParticipation (En attente, Accepté, Refusé) | 10 min | Développeur Backend |
| T5.12.3 | Créer le repository ParticipationRepository | 10 min | Développeur Backend |
| T5.12.4 | Créer la migration pour la table participation | 15 min | Développeur Backend |
| T5.12.5 | Créer la route /events/{id}/participate | 10 min | Développeur Backend |
| T5.12.6 | Implémenter la méthode participate() avec affichage des options | 30 min | Développeur Backend |
| T5.12.7 | Créer le template frontoffice/evenement/participate.html.twig | 50 min | Développeur Frontend |
| T5.12.8 | Afficher les 2 options (créer équipe / rejoindre équipe) | 35 min | Développeur Frontend |
| T5.12.9 | Styliser les cartes d'options avec design moderne | 30 min | Développeur Frontend |
| T5.12.10 | Afficher la liste des équipes disponibles (< 6 membres) | 30 min | Développeur Frontend |
| T5.12.11 | Implémenter la création automatique de participation lors de création d'équipe | 25 min | Développeur Backend |
| T5.12.12 | Tester l'inscription d'une équipe à un événement | 20 min | Testeur |

**Total Estimation**: 4h 45min

---

## US-5.13: Acceptation automatique des participations (Système)

**User Story**: Le système accepte automatiquement la participation d'une équipe si le nombre maximal d'équipes n'est pas atteint afin d'assurer une gestion équitable

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.13.1 | Implémenter la méthode validateParticipation() dans l'entité Participation | 40 min | Développeur Backend |
| T5.13.2 | Vérifier si l'événement est annulé → refuser | 15 min | Développeur Backend |
| T5.13.3 | Compter les participations acceptées (ne pas compter celle en cours) | 25 min | Développeur Backend |
| T5.13.4 | Comparer avec nbMax de l'événement | 15 min | Développeur Backend |
| T5.13.5 | Si < nbMax → accepter automatiquement (setStatut ACCEPTE) | 15 min | Développeur Backend |
| T5.13.6 | Si >= nbMax → refuser automatiquement (setStatut REFUSE) | 15 min | Développeur Backend |
| T5.13.7 | Retourner un tableau avec 'accepted' (bool) et 'message' (string) | 15 min | Développeur Backend |
| T5.13.8 | Appeler validateParticipation() dans ParticipationController->new() | 20 min | Développeur Backend |
| T5.13.9 | Afficher le message de succès/erreur via flash | 10 min | Développeur Backend |
| T5.13.10 | Tester l'acceptation automatique (places disponibles) | 20 min | Testeur |
| T5.13.11 | Tester le refus automatique (capacité atteinte) | 20 min | Testeur |

**Total Estimation**: 3h 30min

---

## US-5.14: Refus automatique si doublon étudiant (Système)

**User Story**: Le système refuse la participation d'une équipe automatiquement si un étudiant est un membre dans deux équipes différentes pour le même événement

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.14.1 | Ajouter la vérification de doublon dans validateParticipation() | 35 min | Développeur Backend |
| T5.14.2 | Parcourir toutes les participations acceptées de l'événement | 20 min | Développeur Backend |
| T5.14.3 | Pour chaque participation, récupérer les étudiants de l'équipe | 15 min | Développeur Backend |
| T5.14.4 | Comparer avec les étudiants de l'équipe actuelle | 25 min | Développeur Backend |
| T5.14.5 | Si doublon trouvé → refuser avec message explicite (nom étudiant + équipe) | 20 min | Développeur Backend |
| T5.14.6 | Tester le refus avec un étudiant dans 2 équipes | 25 min | Testeur |
| T5.14.7 | Vérifier que le message indique quel étudiant et quelle équipe | 15 min | Testeur |

**Total Estimation**: 2h 35min

---

## US-5.15: Consulter le statut de ma participation (Étudiant)

**User Story**: En tant qu'Étudiant, je souhaite consulter le statut de ma participation afin de savoir si mon équipe est acceptée ou refusée

| ID Tâche | Tâches Techniques | Estimation | Responsable |
|----------|-------------------|------------|-------------|
| T5.15.1 | Créer la route /mes-participations | 10 min | Développeur Backend |
| T5.15.2 | Implémenter la méthode mesParticipations() avec filtrage par étudiant | 30 min | Développeur Backend |
| T5.15.3 | Récupérer toutes les équipes de l'étudiant connecté | 20 min | Développeur Backend |
| T5.15.4 | Pour chaque équipe, récupérer la participation associée | 25 min | Développeur Backend |
| T5.15.5 | Créer le template frontoffice/participation/mes_participations.html.twig | 45 min | Développeur Frontend |
| T5.15.6 | Afficher les cartes de participations avec statut (badge coloré) | 35 min | Développeur Frontend |
| T5.15.7 | Afficher les détails (événement, équipe, date, lieu) | 30 min | Développeur Frontend |
| T5.15.8 | Ajouter le bouton "Donner mon feedback" pour participations acceptées | 20 min | Développeur Frontend |
| T5.15.9 | Styliser avec design moderne (gradients, ombres) | 25 min | Développeur Frontend |
| T5.15.10 | Tester l'affichage avec participations acceptées | 15 min | Testeur |
| T5.15.11 | Tester l'affichage avec participations refusées | 15 min | Testeur |

**Total Estimation**: 4h 30min

---

**FIN PARTIE 1/3**

**Total Estimation Partie 1**: 60h 35min  
**User Stories Couvertes**: US-5.1 à US-5.15 (15 US)
