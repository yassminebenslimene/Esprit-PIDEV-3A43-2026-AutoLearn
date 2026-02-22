# 📋 Sprint Backlog - Module Gestion Utilisateur
## Organisation par Sprint

> **Note**: Ce Sprint Backlog reflète le travail réellement effectué sur le projet AutoLearn, incluant le CRUD utilisateur, les bundles (Audit, UserActivity), l'Assistant IA avec Ollama, et les fonctionnalités de sécurité avancées.

---

# 🎯 SPRINT 1 - Fonctionnalités de Base (2 semaines)

## Objectif du Sprint 1
Mettre en place les fonctionnalités essentielles d'authentification et de gestion de profil utilisateur.

## User Stories du Sprint 1
- US-1.1: Inscription utilisateur
- US-1.2: Connexion utilisateur  
- US-1.3: Déconnexion utilisateur
- US-1.5: Consulter profil

## Tableau des Tâches - Sprint 1

| ID Tâche | User Story | Description de la Tâche | Type | Estimation | Priorité | Statut | Assigné | Dépendances |
|----------|-----------|------------------------|------|------------|----------|--------|---------|-------------|
| **T-1.1.1** | US-1.1 | Créer l'entité User (abstract) avec Single Table Inheritance | Backend | 2h | Haute | ✅ Fait | Dev Backend | - |
| **T-1.1.2** | US-1.1 | Créer l'entité Etudiant (extends User) | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.1.1 |
| **T-1.1.3** | US-1.1 | Créer l'entité Admin (extends User) | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.1.1 |
| **T-1.1.4** | US-1.1 | Ajouter les validations (email unique, mot de passe fort) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.1.1 |
| **T-1.1.5** | US-1.1 | Créer la migration de base de données | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.1.4 |
| **T-1.1.6** | US-1.1 | Créer RegistrationFormType | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.1.5 |
| **T-1.1.7** | US-1.1 | Créer RegistrationController avec méthode register() | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.1.6 |
| **T-1.1.8** | US-1.1 | Implémenter le hashage du mot de passe | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.1.7 |
| **T-1.1.9** | US-1.1 | Créer la vue Twig register.html.twig | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.1.7 |
| **T-1.1.10** | US-1.1 | Ajouter validation JavaScript côté client | Frontend | 2h | Moyenne | ✅ Fait | Dev Frontend | T-1.1.9 |
| **T-1.1.11** | US-1.1 | Styliser le formulaire d'inscription (CSS) | Frontend | 1h | Basse | ✅ Fait | Dev Frontend | T-1.1.9 |
| **T-1.1.12** | US-1.1 | Tester l'inscription avec données valides | Test | 1h | Haute | ✅ Fait | QA | T-1.1.11 |
| **T-1.1.13** | US-1.1 | Tester l'inscription avec données invalides | Test | 1h | Haute | ✅ Fait | QA | T-1.1.11 |
| **T-1.2.1** | US-1.2 | Configurer Symfony Security (security.yaml) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.1.5 |
| **T-1.2.2** | US-1.2 | Créer LoginFormType | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.2.3** | US-1.2 | Créer SecurityController avec méthode login() | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.2.2 |
| **T-1.2.4** | US-1.2 | Implémenter l'authentification par email/password | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.2.3 |
| **T-1.2.5** | US-1.2 | Configurer les rôles (ROLE_ADMIN, ROLE_ETUDIANT) | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.2.4 |
| **T-1.2.6** | US-1.2 | Créer la vue Twig login.html.twig | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.2.3 |
| **T-1.2.7** | US-1.2 | Implémenter "Se souvenir de moi" (remember_me) | Backend | 1h | Moyenne | ✅ Fait | Dev Backend | T-1.2.4 |
| **T-1.2.8** | US-1.2 | Redirection selon rôle (Admin→backoffice, Etudiant→frontoffice) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.2.5 |
| **T-1.2.9** | US-1.2 | Gérer les erreurs de connexion (identifiants incorrects) | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.2.8 |
| **T-1.2.10** | US-1.2 | Mettre à jour lastLoginAt lors de la connexion | Backend | 1h | Moyenne | ✅ Fait | Dev Backend | T-1.2.8 |
| **T-1.2.11** | US-1.2 | Styliser le formulaire de connexion | Frontend | 1h | Basse | ✅ Fait | Dev Frontend | T-1.2.6 |
| **T-1.2.12** | US-1.2 | Tester connexion Admin | Test | 1h | Haute | ✅ Fait | QA | T-1.2.11 |
| **T-1.2.13** | US-1.2 | Tester connexion Etudiant | Test | 1h | Haute | ✅ Fait | QA | T-1.2.11 |
| **T-1.3.1** | US-1.3 | Configurer la route de déconnexion dans security.yaml | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.3.2** | US-1.3 | Créer le lien de déconnexion dans le menu | Frontend | 0.5h | Haute | ✅ Fait | Dev Frontend | T-1.3.1 |
| **T-1.3.3** | US-1.3 | Configurer la redirection après déconnexion | Backend | 0.5h | Moyenne | ✅ Fait | Dev Backend | T-1.3.1 |
| **T-1.3.4** | US-1.3 | Invalider la session utilisateur | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.3.3 |
| **T-1.3.5** | US-1.3 | Tester la déconnexion | Test | 1h | Haute | ✅ Fait | QA | T-1.3.4 |
| **T-1.5.1** | US-1.5 | Créer la route /profile | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.5.2** | US-1.5 | Créer ProfileController | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.5.1 |
| **T-1.5.3** | US-1.5 | Récupérer les infos de l'utilisateur connecté | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.5.2 |
| **T-1.5.4** | US-1.5 | Créer la vue profile.html.twig | Frontend | 3h | Haute | ✅ Fait | Dev Frontend | T-1.5.3 |
| **T-1.5.5** | US-1.5 | Afficher toutes les informations utilisateur | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.5.4 |
| **T-1.5.6** | US-1.5 | Ajouter la photo de profil | Frontend | 2h | Moyenne | ✅ Fait | Dev Frontend | T-1.5.5 |
| **T-1.5.7** | US-1.5 | Styliser la page profil | Frontend | 2h | Basse | ✅ Fait | Dev Frontend | T-1.5.6 |
| **T-1.5.8** | US-1.5 | Tester l'affichage du profil | Test | 1h | Haute | ✅ Fait | QA | T-1.5.7 |

## Récapitulatif Sprint 1

| Métrique | Valeur |
|----------|--------|
| **Nombre de tâches** | 38 |
| **Estimation totale** | 50.5h |
| **Tâches Backend** | 22 (31h) |
| **Tâches Frontend** | 11 (16.5h) |
| **Tâches Test** | 5 (3h) |
| **Statut** | ✅ 100% Terminé |

---

# 🎯 SPRINT 2 - Fonctionnalités Avancées (2 semaines)

## Objectif du Sprint 2
Implémenter les fonctionnalités avancées de gestion de profil et l'interface d'administration complète.

## User Stories du Sprint 2
- US-1.4: Réinitialisation de mot de passe
- US-1.6: Modifier informations personnelles
- US-1.7: Rechercher un étudiant (Admin)
- US-1.8: Consulter profil détaillé (Admin)
- US-1.9: Ajouter manuellement un étudiant (Admin)

## Tableau des Tâches - Sprint 2

| ID Tâche | User Story | Description de la Tâche | Type | Estimation | Priorité | Statut | Assigné | Dépendances |
|----------|-----------|------------------------|------|------------|----------|--------|---------|-------------|
| **T-1.4.1** | US-1.4 | Créer l'entité PasswordResetToken | Backend | 2h | Haute | 🔄 En cours | Dev Backend | T-1.1.5 |
| **T-1.4.2** | US-1.4 | Créer la migration pour password_reset_token | Backend | 0.5h | Haute | 🔄 En cours | Dev Backend | T-1.4.1 |
| **T-1.4.3** | US-1.4 | Créer le formulaire "Mot de passe oublié" | Backend | 1h | Haute | 🔄 En cours | Dev Backend | T-1.4.2 |
| **T-1.4.4** | US-1.4 | Générer un token unique de réinitialisation | Backend | 2h | Haute | 🔄 En cours | Dev Backend | T-1.4.3 |
| **T-1.4.5** | US-1.4 | Configurer Symfony Mailer | Backend | 2h | Haute | 🔄 En cours | Dev Backend | - |
| **T-1.4.6** | US-1.4 | Créer le template email de réinitialisation | Frontend | 2h | Haute | 🔄 En cours | Dev Frontend | T-1.4.5 |
| **T-1.4.7** | US-1.4 | Implémenter l'envoi d'email avec token | Backend | 2h | Haute | 🔄 En cours | Dev Backend | T-1.4.6 |
| **T-1.4.8** | US-1.4 | Créer la page de réinitialisation | Frontend | 2h | Haute | 🔄 En cours | Dev Frontend | T-1.4.7 |
| **T-1.4.9** | US-1.4 | Valider le token (expiration 1h, usage unique) | Backend | 2h | Haute | 🔄 En cours | Dev Backend | T-1.4.8 |
| **T-1.4.10** | US-1.4 | Mettre à jour le mot de passe | Backend | 1h | Haute | 🔄 En cours | Dev Backend | T-1.4.9 |
| **T-1.4.11** | US-1.4 | Invalider le token après utilisation | Backend | 1h | Haute | 🔄 En cours | Dev Backend | T-1.4.10 |
| **T-1.4.12** | US-1.4 | Tester le processus complet | Test | 3h | Haute | ⏳ À faire | QA | T-1.4.11 |
| **T-1.6.1** | US-1.6 | Créer ProfileEditType | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.5.2 |
| **T-1.6.2** | US-1.6 | Créer la route /profile/edit | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.6.1 |
| **T-1.6.3** | US-1.6 | Implémenter modification nom, prénom | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.6.2 |
| **T-1.6.4** | US-1.6 | Implémenter modification email (vérif unicité) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.6.3 |
| **T-1.6.5** | US-1.6 | Implémenter upload photo de profil | Backend | 3h | Haute | ✅ Fait | Dev Backend | T-1.6.4 |
| **T-1.6.6** | US-1.6 | Valider formats image (jpg, png, max 2MB) | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.6.5 |
| **T-1.6.7** | US-1.6 | Stocker image dans /public/uploads/profiles/ | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.6.6 |
| **T-1.6.8** | US-1.6 | Créer la vue profile_edit.html.twig | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.6.2 |
| **T-1.6.9** | US-1.6 | Ajouter prévisualisation de la photo | Frontend | 1h | Moyenne | ✅ Fait | Dev Frontend | T-1.6.8 |
| **T-1.6.10** | US-1.6 | Message de confirmation après modification | Frontend | 0.5h | Moyenne | ✅ Fait | Dev Frontend | T-1.6.9 |
| **T-1.6.11** | US-1.6 | Tester la modification | Test | 2h | Haute | ✅ Fait | QA | T-1.6.10 |
| **T-1.7.1** | US-1.7 | Créer la route /backoffice/users | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.7.2** | US-1.7 | Créer UserManagementController | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.7.1 |
| **T-1.7.3** | US-1.7 | Implémenter recherche par nom (LIKE query) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.7.4** | US-1.7 | Implémenter recherche par email (LIKE query) | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.7.3 |
| **T-1.7.5** | US-1.7 | Créer le formulaire de recherche | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.7.4 |
| **T-1.7.6** | US-1.7 | Afficher résultats dans un tableau | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.7.5 |
| **T-1.7.7** | US-1.7 | Ajouter pagination (10 résultats/page) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.7.6 |
| **T-1.7.8** | US-1.7 | Ajouter filtres (niveau, statut) | Backend | 2h | Moyenne | ✅ Fait | Dev Backend | T-1.7.7 |
| **T-1.7.9** | US-1.7 | Créer la vue users_list.html.twig | Frontend | 3h | Haute | ✅ Fait | Dev Frontend | T-1.7.8 |
| **T-1.7.10** | US-1.7 | Styliser le tableau et filtres | Frontend | 2h | Basse | ✅ Fait | Dev Frontend | T-1.7.9 |
| **T-1.7.11** | US-1.7 | Tester la recherche | Test | 2h | Haute | ✅ Fait | QA | T-1.7.10 |
| **T-1.8.1** | US-1.8 | Créer la route /backoffice/users/{id} | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.8.2** | US-1.8 | Créer méthode show() dans UserManagementController | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.8.1 |
| **T-1.8.3** | US-1.8 | Récupérer toutes les infos utilisateur | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.8.2 |
| **T-1.8.4** | US-1.8 | Afficher historique d'activité (UserActivity) | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.8.3 |
| **T-1.8.5** | US-1.8 | Afficher statistiques connexion | Backend | 2h | Moyenne | ✅ Fait | Dev Backend | T-1.8.4 |
| **T-1.8.6** | US-1.8 | Afficher historique modifications (Audit) | Backend | 2h | Moyenne | ✅ Fait | Dev Backend | T-1.8.5 |
| **T-1.8.7** | US-1.8 | Créer la vue user_detail.html.twig | Frontend | 3h | Haute | ✅ Fait | Dev Frontend | T-1.8.6 |
| **T-1.8.8** | US-1.8 | Ajouter boutons d'action (Modifier, Suspendre) | Frontend | 1h | Haute | ✅ Fait | Dev Frontend | T-1.8.7 |
| **T-1.8.9** | US-1.8 | Styliser la page profil admin | Frontend | 2h | Basse | ✅ Fait | Dev Frontend | T-1.8.8 |
| **T-1.8.10** | US-1.8 | Tester l'affichage | Test | 1.5h | Haute | ✅ Fait | QA | T-1.8.9 |
| **T-1.9.1** | US-1.9 | Créer la route /backoffice/users/new | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.9.2** | US-1.9 | Créer AdminUserCreateType | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.9.1 |
| **T-1.9.3** | US-1.9 | Implémenter création d'étudiant | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.9.2 |
| **T-1.9.4** | US-1.9 | Générer mot de passe temporaire | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.9.3 |
| **T-1.9.5** | US-1.9 | Envoyer email avec identifiants | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.9.4 |
| **T-1.9.6** | US-1.9 | Valider unicité email | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.9.5 |
| **T-1.9.7** | US-1.9 | Créer la vue user_create.html.twig | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.9.2 |
| **T-1.9.8** | US-1.9 | Validation côté serveur | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.9.6 |
| **T-1.9.9** | US-1.9 | Message de confirmation | Frontend | 0.5h | Moyenne | ✅ Fait | Dev Frontend | T-1.9.7 |
| **T-1.9.10** | US-1.9 | Logger l'action dans UserActivity | Backend | 1h | Moyenne | ✅ Fait | Dev Backend | T-1.9.8 |
| **T-1.9.11** | US-1.9 | Tester la création | Test | 2h | Haute | ✅ Fait | QA | T-1.9.10 |

## Récapitulatif Sprint 2

| Métrique | Valeur |
|----------|--------|
| **Nombre de tâches** | 49 |
| **Estimation totale** | 71h |
| **Tâches Backend** | 32 (46.5h) |
| **Tâches Frontend** | 12 (19.5h) |
| **Tâches Test** | 5 (5h) |
| **Statut** | 🔄 76% Terminé (37/49) |

---

# 🎯 SPRINT 3 - Finalisation et Fonctionnalités Complémentaires (2 semaines)

## Objectif du Sprint 3
Finaliser le module avec les fonctionnalités de suspension, modification, export et historique.

## User Stories du Sprint 3
- US-1.10: Désactiver un compte étudiant
- US-1.11: Modifier les informations d'un étudiant (Admin)
- US-1.12: Exporter la liste des utilisateurs (CSV/Excel)
- US-1.13: Changer son mot de passe depuis le profil
- US-1.14: Voir l'historique des connexions

## Tableau des Tâches - Sprint 3

| ID Tâche | User Story | Description de la Tâche | Type | Estimation | Priorité | Statut | Assigné | Dépendances |
|----------|-----------|------------------------|------|------------|----------|--------|---------|-------------|
| **T-1.10.1** | US-1.10 | Créer la route /backoffice/users/{id}/suspend | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.10.2** | US-1.10 | Implémenter méthode suspend() | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.10.1 |
| **T-1.10.3** | US-1.10 | Mettre à jour isSuspended = true | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.10.2 |
| **T-1.10.4** | US-1.10 | Enregistrer suspendedAt, suspendedBy, reason | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.10.3 |
| **T-1.10.5** | US-1.10 | Créer modal pour raison suspension | Frontend | 2h | Haute | ✅ Fait | Dev Frontend | T-1.10.4 |
| **T-1.10.6** | US-1.10 | Bloquer connexion comptes suspendus | Backend | 2h | Haute | ✅ Fait | Dev Backend | T-1.10.4 |
| **T-1.10.7** | US-1.10 | Message d'erreur lors connexion suspendu | Frontend | 1h | Haute | ✅ Fait | Dev Frontend | T-1.10.6 |
| **T-1.10.8** | US-1.10 | Créer route /backoffice/users/{id}/unsuspend | Backend | 0.5h | Haute | ✅ Fait | Dev Backend | T-1.10.6 |
| **T-1.10.9** | US-1.10 | Implémenter réactivation compte | Backend | 1h | Haute | ✅ Fait | Dev Backend | T-1.10.8 |
| **T-1.10.10** | US-1.10 | Logger actions suspension/réactivation | Backend | 1h | Moyenne | ✅ Fait | Dev Backend | T-1.10.9 |
| **T-1.10.11** | US-1.10 | Commande suspension auto (inactivité) | Backend | 3h | Moyenne | ✅ Fait | Dev Backend | T-1.10.9 |
| **T-1.10.12** | US-1.10 | Tester suspension/réactivation | Test | 2h | Haute | ✅ Fait | QA | T-1.10.11 |
| **T-1.11.1** | US-1.11 | Créer la route /backoffice/users/{id}/edit | Backend | 0.5h | Moyenne | ⏳ À faire | Dev Backend | T-1.7.2 |
| **T-1.11.2** | US-1.11 | Créer AdminUserEditType | Backend | 2h | Moyenne | ⏳ À faire | Dev Backend | T-1.11.1 |
| **T-1.11.3** | US-1.11 | Implémenter modification infos étudiant | Backend | 2h | Moyenne | ⏳ À faire | Dev Backend | T-1.11.2 |
| **T-1.11.4** | US-1.11 | Valider les modifications | Backend | 1h | Moyenne | ⏳ À faire | Dev Backend | T-1.11.3 |
| **T-1.11.5** | US-1.11 | Créer la vue user_edit.html.twig | Frontend | 2h | Moyenne | ⏳ À faire | Dev Frontend | T-1.11.2 |
| **T-1.11.6** | US-1.11 | Logger la modification | Backend | 1h | Basse | ⏳ À faire | Dev Backend | T-1.11.4 |
| **T-1.11.7** | US-1.11 | Tester la modification | Test | 1.5h | Moyenne | ⏳ À faire | QA | T-1.11.6 |
| **T-1.12.1** | US-1.12 | Installer PhpSpreadsheet ou CSV library | Backend | 0.5h | Basse | ⏳ À faire | Dev Backend | - |
| **T-1.12.2** | US-1.12 | Créer la route /backoffice/users/export | Backend | 0.5h | Basse | ⏳ À faire | Dev Backend | T-1.12.1 |
| **T-1.12.3** | US-1.12 | Implémenter export CSV | Backend | 2h | Basse | ⏳ À faire | Dev Backend | T-1.12.2 |
| **T-1.12.4** | US-1.12 | Implémenter export Excel | Backend | 2h | Basse | ⏳ À faire | Dev Backend | T-1.12.3 |
| **T-1.12.5** | US-1.12 | Ajouter bouton Export dans la liste | Frontend | 1h | Basse | ⏳ À faire | Dev Frontend | T-1.12.4 |
| **T-1.12.6** | US-1.12 | Tester l'export | Test | 1h | Basse | ⏳ À faire | QA | T-1.12.5 |
| **T-1.13.1** | US-1.13 | Créer ChangePasswordType | Backend | 1h | Moyenne | ⏳ À faire | Dev Backend | T-1.5.2 |
| **T-1.13.2** | US-1.13 | Créer la route /profile/change-password | Backend | 0.5h | Moyenne | ⏳ À faire | Dev Backend | T-1.13.1 |
| **T-1.13.3** | US-1.13 | Valider ancien mot de passe | Backend | 1h | Moyenne | ⏳ À faire | Dev Backend | T-1.13.2 |
| **T-1.13.4** | US-1.13 | Valider nouveau mot de passe (règles) | Backend | 1h | Moyenne | ⏳ À faire | Dev Backend | T-1.13.3 |
| **T-1.13.5** | US-1.13 | Hasher et sauvegarder nouveau mot de passe | Backend | 1h | Moyenne | ⏳ À faire | Dev Backend | T-1.13.4 |
| **T-1.13.6** | US-1.13 | Créer la vue change_password.html.twig | Frontend | 2h | Moyenne | ⏳ À faire | Dev Frontend | T-1.13.2 |
| **T-1.13.7** | US-1.13 | Message de confirmation | Frontend | 0.5h | Basse | ⏳ À faire | Dev Frontend | T-1.13.6 |
| **T-1.13.8** | US-1.13 | Tester changement mot de passe | Test | 1.5h | Moyenne | ⏳ À faire | QA | T-1.13.7 |
| **T-1.14.1** | US-1.14 | Créer table login_history | Backend | 1h | Basse | ⏳ À faire | Dev Backend | - |
| **T-1.14.2** | US-1.14 | Logger chaque connexion (IP, date, user-agent) | Backend | 2h | Basse | ⏳ À faire | Dev Backend | T-1.14.1 |
| **T-1.14.3** | US-1.14 | Créer la route /backoffice/users/{id}/login-history | Backend | 0.5h | Basse | ⏳ À faire | Dev Backend | T-1.14.2 |
| **T-1.14.4** | US-1.14 | Afficher historique dans profil admin | Frontend | 2h | Basse | ⏳ À faire | Dev Frontend | T-1.14.3 |
| **T-1.14.5** | US-1.14 | Ajouter pagination historique | Backend | 1h | Basse | ⏳ À faire | Dev Backend | T-1.14.4 |
| **T-1.14.6** | US-1.14 | Tester l'historique | Test | 1h | Basse | ⏳ À faire | QA | T-1.14.5 |

## Récapitulatif Sprint 3

| Métrique | Valeur |
|----------|--------|
| **Nombre de tâches** | 37 |
| **Estimation totale** | 48h |
| **Tâches Backend** | 24 (27.5h) |
| **Tâches Frontend** | 9 (12.5h) |
| **Tâches Test** | 4 (8h) |
| **Statut** | 🔄 32% Terminé (12/37) |

---

# 📊 RÉCAPITULATIF GLOBAL

## Vue d'ensemble des 3 Sprints

| Sprint | User Stories | Tâches | Estimation | Statut Global |
|--------|-------------|--------|------------|---------------|
| **Sprint 1** | 4 US | 38 tâches | 50.5h | ✅ 100% Terminé |
| **Sprint 2** | 5 US | 49 tâches | 71h | 🔄 76% Terminé |
| **Sprint 3** | 5 US | 37 tâches | 48h | 🔄 32% Terminé |
| **TOTAL** | **14 US** | **124 tâches** | **169.5h** | **67% Complété** |

## Statistiques par Type de Tâche

| Type | Sprint 1 | Sprint 2 | Sprint 3 | Total |
|------|----------|----------|----------|-------|
| **Backend** | 22 (31h) | 32 (46.5h) | 24 (27.5h) | 78 (105h) |
| **Frontend** | 11 (16.5h) | 12 (19.5h) | 9 (12.5h) | 32 (48.5h) |
| **Test** | 5 (3h) | 5 (5h) | 4 (8h) | 14 (16h) |

## Progression Globale

| Statut | Nombre de Tâches | Pourcentage |
|--------|------------------|-------------|
| ✅ Fait | 83 | 67% |
| 🔄 En cours | 12 | 10% |
| ⏳ À faire | 29 | 23% |

## Prochaines Étapes

### Priorité Haute (Sprint 2 - À terminer)
1. Finaliser la réinitialisation de mot de passe (US-1.4)
2. Compléter les tests du Sprint 2

### Priorité Moyenne (Sprint 3 - À planifier)
1. Implémenter la modification des utilisateurs par l'admin (US-1.11)
2. Développer le changement de mot de passe depuis le profil (US-1.13)

### Priorité Basse (Sprint 3 - Optionnel)
1. Ajouter l'export CSV/Excel (US-1.12)
2. Implémenter l'historique des connexions (US-1.14)

---

**Date de création**: 2026-02-22  
**Dernière mise à jour**: 2026-02-22  
**Version**: 2.0  
**Statut**: Document complet avec 3 sprints détaillés

