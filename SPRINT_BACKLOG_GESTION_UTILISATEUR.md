# 📋 Sprint Backlog - Module Gestion Utilisateur

## 🎯 Objectif du Sprint
Implémenter le module complet de gestion des utilisateurs avec authentification, profils, et administration.

---

## 📊 Vue d'ensemble des User Stories

| ID Story | User Story | Priorité | Statut | Sprint |
|----------|-----------|----------|--------|--------|
| 1.1 | Inscription utilisateur | 100 | ✅ Fait | Sprint 1 |
| 1.2 | Connexion utilisateur | 100 | ✅ Fait | Sprint 1 |
| 1.3 | Déconnexion utilisateur | 90 | ✅ Fait | Sprint 1 |
| 1.4 | Réinitialisation mot de passe | 80 | 🔄 En cours | Sprint 2 |
| 1.5 | Consulter profil | 85 | ✅ Fait | Sprint 1 |
| 1.6 | Modifier profil | 80 | ✅ Fait | Sprint 1 |
| 1.7 | Rechercher étudiant (Admin) | 70 | ✅ Fait | Sprint 2 |
| 1.8 | Consulter profil utilisateur (Admin) | 75 | ✅ Fait | Sprint 2 |
| 1.9 | Ajouter étudiant (Admin) | 80 | ✅ Fait | Sprint 2 |
| 1.10 | Désactiver compte étudiant (Admin) | 60 | ✅ Fait | Sprint 2 |

---


## 📝 Détail des Tâches par User Story

### 🔹 US 1.1 - Inscription Utilisateur (Priorité: 100)

**En tant qu'utilisateur, je souhaite m'inscrire (créer un compte)**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.1.1 | Créer l'entité User avec héritage (Admin/Etudiant) | 3h | Dev | ✅ |
| 1.1.2 | Créer le formulaire d'inscription (RegistrationFormType) | 2h | Dev | ✅ |
| 1.1.3 | Implémenter la validation des champs (email unique, mot de passe fort) | 2h | Dev | ✅ |
| 1.1.4 | Créer le contrôleur d'inscription (RegistrationController) | 2h | Dev | ✅ |
| 1.1.5 | Hasher le mot de passe avec Symfony Security | 1h | Dev | ✅ |
| 1.1.6 | Créer la vue Twig pour le formulaire d'inscription | 2h | Dev | ✅ |
| 1.1.7 | Ajouter les validations côté client (JavaScript) | 2h | Dev | ✅ |
| 1.1.8 | Créer la migration de base de données | 1h | Dev | ✅ |
| 1.1.9 | Tester l'inscription avec différents scénarios | 2h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Formulaire avec champs: nom, prénom, email, mot de passe, confirmation mot de passe, niveau (pour étudiant)
- ✅ Validation email unique
- ✅ Mot de passe: min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial
- ✅ Message de confirmation après inscription
- ✅ Redirection vers page de connexion

**Total: 17h**

---

### 🔹 US 1.2 - Connexion Utilisateur (Priorité: 100)

**En tant qu'utilisateur, je souhaite me connecter avec mes identifiants**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.2.1 | Configurer Symfony Security (security.yaml) | 2h | Dev | ✅ |
| 1.2.2 | Créer le formulaire de connexion (LoginFormType) | 1h | Dev | ✅ |
| 1.2.3 | Implémenter l'authentification avec email/password | 2h | Dev | ✅ |
| 1.2.4 | Créer le contrôleur de connexion (SecurityController) | 2h | Dev | ✅ |
| 1.2.5 | Gérer les rôles (ROLE_ADMIN, ROLE_ETUDIANT) | 2h | Dev | ✅ |
| 1.2.6 | Créer la vue Twig pour le formulaire de connexion | 2h | Dev | ✅ |
| 1.2.7 | Implémenter "Se souvenir de moi" (remember_me) | 1h | Dev | ✅ |
| 1.2.8 | Redirection selon le rôle (Admin → backoffice, Etudiant → frontoffice) | 2h | Dev | ✅ |
| 1.2.9 | Gérer les erreurs de connexion (identifiants incorrects) | 1h | Dev | ✅ |
| 1.2.10 | Mettre à jour lastLoginAt lors de la connexion | 1h | Dev | ✅ |
| 1.2.11 | Tester la connexion avec différents rôles | 2h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Connexion avec email et mot de passe
- ✅ Message d'erreur si identifiants incorrects
- ✅ Redirection selon le rôle utilisateur
- ✅ Option "Se souvenir de moi"
- ✅ Mise à jour de la date de dernière connexion

**Total: 18h**

---

### 🔹 US 1.3 - Déconnexion Utilisateur (Priorité: 90)

**En tant qu'utilisateur, je souhaite me déconnecter**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.3.1 | Configurer la route de déconnexion dans security.yaml | 0.5h | Dev | ✅ |
| 1.3.2 | Créer le lien de déconnexion dans le menu | 0.5h | Dev | ✅ |
| 1.3.3 | Redirection vers page de connexion après déconnexion | 0.5h | Dev | ✅ |
| 1.3.4 | Invalider la session utilisateur | 0.5h | Dev | ✅ |
| 1.3.5 | Tester la déconnexion | 1h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Bouton/lien de déconnexion visible dans le menu
- ✅ Session invalidée après déconnexion
- ✅ Redirection vers page de connexion
- ✅ Impossible d'accéder aux pages protégées après déconnexion

**Total: 3h**

---


### 🔹 US 1.4 - Réinitialisation Mot de Passe (Priorité: 80)

**En tant qu'utilisateur, je souhaite réinitialiser mon mot de passe via email**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.4.1 | Créer l'entité PasswordResetToken | 2h | Dev | 🔄 |
| 1.4.2 | Créer le formulaire "Mot de passe oublié" | 1h | Dev | 🔄 |
| 1.4.3 | Générer un token unique de réinitialisation | 2h | Dev | 🔄 |
| 1.4.4 | Configurer le service d'envoi d'email (Mailer) | 2h | Dev | 🔄 |
| 1.4.5 | Créer le template email avec lien de réinitialisation | 2h | Dev | 🔄 |
| 1.4.6 | Implémenter l'envoi d'email avec token | 2h | Dev | 🔄 |
| 1.4.7 | Créer la page de réinitialisation avec nouveau mot de passe | 2h | Dev | 🔄 |
| 1.4.8 | Valider le token (expiration 1h, usage unique) | 2h | Dev | 🔄 |
| 1.4.9 | Mettre à jour le mot de passe dans la base de données | 1h | Dev | 🔄 |
| 1.4.10 | Invalider le token après utilisation | 1h | Dev | 🔄 |
| 1.4.11 | Créer les vues Twig (demande + réinitialisation) | 2h | Dev | 🔄 |
| 1.4.12 | Tester le processus complet de réinitialisation | 3h | QA | ⏳ |

**Critères d'acceptation:**
- ⏳ Lien "Mot de passe oublié" sur la page de connexion
- ⏳ Email envoyé avec lien de réinitialisation
- ⏳ Token valide pendant 1 heure
- ⏳ Token utilisable une seule fois
- ⏳ Nouveau mot de passe respecte les règles de validation
- ⏳ Message de confirmation après réinitialisation

**Total: 22h**

---

### 🔹 US 1.5 - Consulter Profil (Priorité: 85)

**En tant qu'utilisateur, je souhaite consulter mon profil**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.5.1 | Créer la route /profile | 0.5h | Dev | ✅ |
| 1.5.2 | Créer le contrôleur ProfileController | 1h | Dev | ✅ |
| 1.5.3 | Récupérer les informations de l'utilisateur connecté | 1h | Dev | ✅ |
| 1.5.4 | Créer la vue Twig pour afficher le profil | 3h | Dev | ✅ |
| 1.5.5 | Afficher: nom, prénom, email, rôle, date création, niveau (si étudiant) | 2h | Dev | ✅ |
| 1.5.6 | Ajouter la photo de profil (si disponible) | 2h | Dev | ✅ |
| 1.5.7 | Styliser la page profil (CSS) | 2h | Dev | ✅ |
| 1.5.8 | Tester l'affichage du profil | 1h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Page profil accessible depuis le menu
- ✅ Affichage de toutes les informations utilisateur
- ✅ Design responsive et professionnel
- ✅ Bouton "Modifier le profil" visible

**Total: 12.5h**

---

### 🔹 US 1.6 - Modifier Profil (Priorité: 80)

**En tant qu'utilisateur, je souhaite modifier mes informations personnelles**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.6.1 | Créer le formulaire ProfileEditType | 2h | Dev | ✅ |
| 1.6.2 | Créer la route /profile/edit | 0.5h | Dev | ✅ |
| 1.6.3 | Implémenter la modification de nom, prénom | 1h | Dev | ✅ |
| 1.6.4 | Implémenter la modification d'email (avec vérification unicité) | 2h | Dev | ✅ |
| 1.6.5 | Implémenter l'upload de photo de profil | 3h | Dev | ✅ |
| 1.6.6 | Valider les formats d'image (jpg, png, max 2MB) | 1h | Dev | ✅ |
| 1.6.7 | Stocker l'image dans /public/uploads/profiles/ | 1h | Dev | ✅ |
| 1.6.8 | Créer la vue Twig pour le formulaire d'édition | 2h | Dev | ✅ |
| 1.6.9 | Ajouter la validation côté serveur | 1h | Dev | ✅ |
| 1.6.10 | Message de confirmation après modification | 0.5h | Dev | ✅ |
| 1.6.11 | Tester la modification avec différents scénarios | 2h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Formulaire pré-rempli avec les données actuelles
- ✅ Modification de nom, prénom, email, photo
- ✅ Validation des données
- ✅ Upload de photo avec prévisualisation
- ✅ Message de succès après modification

**Total: 16h**

---


### 🔹 US 1.7 - Rechercher Étudiant (Admin) (Priorité: 70)

**En tant qu'administrateur, je souhaite rechercher un étudiant par nom ou email**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.7.1 | Créer la route /backoffice/users | 0.5h | Dev | ✅ |
| 1.7.2 | Créer le contrôleur UserManagementController | 1h | Dev | ✅ |
| 1.7.3 | Implémenter la recherche par nom (LIKE query) | 2h | Dev | ✅ |
| 1.7.4 | Implémenter la recherche par email (LIKE query) | 1h | Dev | ✅ |
| 1.7.5 | Créer le formulaire de recherche | 1h | Dev | ✅ |
| 1.7.6 | Afficher les résultats dans un tableau | 2h | Dev | ✅ |
| 1.7.7 | Ajouter la pagination (10 résultats par page) | 2h | Dev | ✅ |
| 1.7.8 | Ajouter des filtres (niveau, statut actif/suspendu) | 2h | Dev | ✅ |
| 1.7.9 | Créer la vue Twig pour la liste des utilisateurs | 3h | Dev | ✅ |
| 1.7.10 | Styliser le tableau et les filtres | 2h | Dev | ✅ |
| 1.7.11 | Tester la recherche avec différents critères | 2h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Barre de recherche avec champ nom/email
- ✅ Résultats affichés en temps réel ou après clic sur "Rechercher"
- ✅ Tableau avec: nom, prénom, email, niveau, statut, actions
- ✅ Pagination fonctionnelle
- ✅ Filtres par niveau et statut

**Total: 18.5h**

---

### 🔹 US 1.8 - Consulter Profil Utilisateur (Admin) (Priorité: 75)

**En tant qu'administrateur, je souhaite consulter le profil détaillé d'un utilisateur**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.8.1 | Créer la route /backoffice/users/{id} | 0.5h | Dev | ✅ |
| 1.8.2 | Créer la méthode show() dans UserManagementController | 1h | Dev | ✅ |
| 1.8.3 | Récupérer toutes les informations de l'utilisateur | 1h | Dev | ✅ |
| 1.8.4 | Afficher l'historique d'activité (via UserActivity bundle) | 2h | Dev | ✅ |
| 1.8.5 | Afficher les statistiques (dernière connexion, nombre de connexions) | 2h | Dev | ✅ |
| 1.8.6 | Afficher l'historique des modifications (via Audit bundle) | 2h | Dev | ✅ |
| 1.8.7 | Créer la vue Twig pour le profil détaillé | 3h | Dev | ✅ |
| 1.8.8 | Ajouter des boutons d'action (Modifier, Suspendre, Supprimer) | 1h | Dev | ✅ |
| 1.8.9 | Styliser la page de profil admin | 2h | Dev | ✅ |
| 1.8.10 | Tester l'affichage du profil avec différents utilisateurs | 1.5h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Affichage complet des informations utilisateur
- ✅ Historique d'activité visible
- ✅ Statistiques de connexion
- ✅ Historique des modifications (audit)
- ✅ Boutons d'action fonctionnels

**Total: 16h**

---

### 🔹 US 1.9 - Ajouter Étudiant (Admin) (Priorité: 80)

**En tant qu'administrateur, je souhaite ajouter manuellement un nouvel étudiant**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.9.1 | Créer la route /backoffice/users/new | 0.5h | Dev | ✅ |
| 1.9.2 | Créer le formulaire AdminUserCreateType | 2h | Dev | ✅ |
| 1.9.3 | Implémenter la création d'étudiant | 2h | Dev | ✅ |
| 1.9.4 | Générer un mot de passe temporaire | 1h | Dev | ✅ |
| 1.9.5 | Envoyer un email avec les identifiants | 2h | Dev | ✅ |
| 1.9.6 | Valider l'unicité de l'email | 1h | Dev | ✅ |
| 1.9.7 | Créer la vue Twig pour le formulaire de création | 2h | Dev | ✅ |
| 1.9.8 | Ajouter la validation côté serveur | 1h | Dev | ✅ |
| 1.9.9 | Message de confirmation après création | 0.5h | Dev | ✅ |
| 1.9.10 | Logger l'action dans UserActivity | 1h | Dev | ✅ |
| 1.9.11 | Tester la création d'étudiant | 2h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Formulaire avec: nom, prénom, email, niveau
- ✅ Mot de passe généré automatiquement
- ✅ Email envoyé à l'étudiant avec ses identifiants
- ✅ Validation des données
- ✅ Redirection vers la liste des utilisateurs après création

**Total: 15h**

---


### 🔹 US 1.10 - Désactiver Compte Étudiant (Admin) (Priorité: 60)

**En tant qu'administrateur, je souhaite désactiver un compte étudiant**

#### Tâches Techniques

| ID Tâche | Description | Estimation | Assigné | Statut |
|----------|-------------|------------|---------|--------|
| 1.10.1 | Créer la route /backoffice/users/{id}/suspend | 0.5h | Dev | ✅ |
| 1.10.2 | Implémenter la méthode suspend() dans le contrôleur | 1h | Dev | ✅ |
| 1.10.3 | Mettre à jour isSuspended = true | 0.5h | Dev | ✅ |
| 1.10.4 | Enregistrer suspendedAt, suspendedBy, suspensionReason | 1h | Dev | ✅ |
| 1.10.5 | Créer un formulaire modal pour la raison de suspension | 2h | Dev | ✅ |
| 1.10.6 | Bloquer la connexion des comptes suspendus | 2h | Dev | ✅ |
| 1.10.7 | Afficher un message d'erreur lors de la tentative de connexion | 1h | Dev | ✅ |
| 1.10.8 | Créer la route /backoffice/users/{id}/unsuspend pour réactiver | 0.5h | Dev | ✅ |
| 1.10.9 | Implémenter la réactivation du compte | 1h | Dev | ✅ |
| 1.10.10 | Logger les actions de suspension/réactivation | 1h | Dev | ✅ |
| 1.10.11 | Créer une commande Symfony pour suspension automatique (inactivité) | 3h | Dev | ✅ |
| 1.10.12 | Tester la suspension et réactivation | 2h | QA | ✅ |

**Critères d'acceptation:**
- ✅ Bouton "Suspendre" sur le profil utilisateur
- ✅ Modal pour saisir la raison de suspension
- ✅ Compte suspendu ne peut plus se connecter
- ✅ Message d'erreur explicite lors de la connexion
- ✅ Possibilité de réactiver le compte
- ✅ Historique des suspensions visible

**Total: 15.5h**

---

## 📊 Récapitulatif des Estimations

| User Story | Priorité | Estimation Totale | Statut |
|------------|----------|-------------------|--------|
| US 1.1 - Inscription | 100 | 17h | ✅ Fait |
| US 1.2 - Connexion | 100 | 18h | ✅ Fait |
| US 1.3 - Déconnexion | 90 | 3h | ✅ Fait |
| US 1.4 - Réinitialisation MDP | 80 | 22h | 🔄 En cours |
| US 1.5 - Consulter profil | 85 | 12.5h | ✅ Fait |
| US 1.6 - Modifier profil | 80 | 16h | ✅ Fait |
| US 1.7 - Rechercher étudiant | 70 | 18.5h | ✅ Fait |
| US 1.8 - Consulter profil (Admin) | 75 | 16h | ✅ Fait |
| US 1.9 - Ajouter étudiant | 80 | 15h | ✅ Fait |
| US 1.10 - Désactiver compte | 60 | 15.5h | ✅ Fait |

**Total Estimé: 153.5 heures**

---

## 🎯 Répartition par Sprint

### Sprint 1 (40h) - Fonctionnalités de Base
- ✅ US 1.1 - Inscription (17h)
- ✅ US 1.2 - Connexion (18h)
- ✅ US 1.3 - Déconnexion (3h)
- ✅ US 1.5 - Consulter profil (12.5h) - Partiel

**Total Sprint 1: 50.5h**

### Sprint 2 (40h) - Gestion Profil et Administration
- ✅ US 1.6 - Modifier profil (16h)
- ✅ US 1.7 - Rechercher étudiant (18.5h)
- ✅ US 1.9 - Ajouter étudiant (15h) - Partiel

**Total Sprint 2: 49.5h**

### Sprint 3 (40h) - Fonctionnalités Avancées
- ✅ US 1.8 - Consulter profil Admin (16h)
- ✅ US 1.10 - Désactiver compte (15.5h)
- 🔄 US 1.4 - Réinitialisation MDP (22h) - En cours

**Total Sprint 3: 53.5h**

---

## 📋 Dépendances Techniques

### Technologies Utilisées
- **Framework**: Symfony 6.4
- **ORM**: Doctrine
- **Sécurité**: Symfony Security Component
- **Email**: Symfony Mailer
- **Validation**: Symfony Validator
- **Templates**: Twig
- **Base de données**: MySQL

### Bundles Installés
- ✅ SimpleThings EntityAudit Bundle (Audit des modifications)
- ✅ UserActivity Bundle (Suivi d'activité)
- ✅ Symfony Mailer (Envoi d'emails)

### Services Créés
- ✅ ActivityLogger (Logging des actions utilisateur)
- ✅ AutoSuspendInactiveUsersCommand (Suspension automatique)

---

## 🧪 Tests à Effectuer

### Tests Fonctionnels
- [ ] Test d'inscription avec données valides/invalides
- [ ] Test de connexion avec différents rôles
- [ ] Test de déconnexion et invalidation de session
- [ ] Test de réinitialisation de mot de passe (email, token, expiration)
- [ ] Test de modification de profil
- [ ] Test de recherche d'utilisateurs
- [ ] Test de suspension/réactivation de compte
- [ ] Test de création d'étudiant par admin

### Tests de Sécurité
- [ ] Vérifier que les pages admin ne sont pas accessibles aux étudiants
- [ ] Vérifier que les comptes suspendus ne peuvent pas se connecter
- [ ] Vérifier le hashage des mots de passe
- [ ] Vérifier la validation des tokens de réinitialisation
- [ ] Tester les injections SQL (protection Doctrine)
- [ ] Tester les attaques XSS (protection Twig)

### Tests de Performance
- [ ] Test de charge sur la page de connexion
- [ ] Test de recherche avec grande quantité d'utilisateurs
- [ ] Test d'upload de photos de profil

---

## 📝 Notes Importantes

### Fonctionnalités Déjà Implémentées
✅ Système d'authentification complet
✅ Gestion des rôles (Admin/Etudiant)
✅ Audit des modifications (Etudiant uniquement)
✅ Suivi d'activité utilisateur
✅ Suspension automatique après inactivité
✅ Interface backoffice avec sidebar fixe
✅ Recherche et filtrage des utilisateurs

### Fonctionnalités En Cours
🔄 Réinitialisation de mot de passe par email

### Améliorations Futures
- 📧 Notifications email pour les actions importantes
- 🔐 Authentification à deux facteurs (2FA)
- 📊 Dashboard avec statistiques utilisateurs
- 📱 Version mobile responsive
- 🌐 Internationalisation (FR/EN/AR)

---

## 🚀 Prochaines Étapes

1. **Terminer US 1.4** - Réinitialisation mot de passe
2. **Tests complets** - Tous les scénarios
3. **Documentation** - Guide utilisateur et technique
4. **Déploiement** - Environnement de production

---

**Date de création**: 2026-02-22  
**Dernière mise à jour**: 2026-02-22  
**Version**: 1.0
