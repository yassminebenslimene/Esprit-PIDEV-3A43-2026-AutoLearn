# 📋 Sprint Backlog Complet - Module Gestion Utilisateur

## 🎯 Product Backlog

| ID | User Story | Priorité |
|----|-----------|----------|
| US-1.1 | En tant qu'utilisateur, je souhaite m'inscrire (créer un compte) | 100 |
| US-1.2 | En tant qu'utilisateur, je souhaite me connecter avec mes identifiants afin d'accéder à mon espace personnel | 100 |
| US-1.3 | En tant qu'utilisateur, je souhaite me déconnecter afin de sécuriser mon compte | 90 |
| US-1.4 | En tant qu'utilisateur, je souhaite demander une réinitialisation de mot de passe (via email) afin de récupérer l'accès à mon compte | 80 |
| US-1.5 | En tant qu'utilisateur, je souhaite consulter mon profil afin de voir mes informations | 85 |
| US-1.6 | En tant qu'utilisateur, je souhaite modifier mes informations personnelles (nom, photo, email) afin de maintenir mon profil à jour | 80 |
| US-1.7 | En tant qu'administrateur, je souhaite rechercher un étudiant (par nom, email) afin de trouver rapidement un compte | 70 |
| US-1.8 | En tant qu'administrateur, je souhaite consulter le profil détaillé d'un utilisateur afin de vérifier son statut et son activité | 75 |
| US-1.9 | En tant qu'administrateur, je souhaite ajouter manuellement un nouvel étudiant afin de lui créer un accès | 80 |
| US-1.10 | En tant qu'administrateur, je souhaite désactiver un compte étudiant | 60 |
| US-1.11 | En tant qu'administrateur, je souhaite modifier les informations d'un étudiant | 65 |
| US-1.12 | En tant qu'administrateur, je souhaite exporter la liste des utilisateurs (CSV/Excel) | 50 |
| US-1.13 | En tant qu'utilisateur, je souhaite changer mon mot de passe depuis mon profil | 70 |
| US-1.14 | En tant qu'administrateur, je souhaite voir l'historique des connexions d'un utilisateur | 55 |

---


## 📊 Sprint Backlog Détaillé - Toutes les Tâches

| ID Tâche | User Story | Description de la Tâche | Type | Estimation | Priorité | Sprint | Statut | Assigné | Dépendances |
|----------|-----------|------------------------|------|------------|----------|--------|--------|---------|-------------|
| **T-1.1.1** | US-1.1 | Créer l'entité User (abstract) avec Single Table Inheritance | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | - |
| **T-1.1.2** | US-1.1 | Créer l'entité Etudiant (extends User) | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.1 |
| **T-1.1.3** | US-1.1 | Créer l'entité Admin (extends User) | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.1 |
| **T-1.1.4** | US-1.1 | Ajouter les validations (email unique, mot de passe fort) | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.1 |
| **T-1.1.5** | US-1.1 | Créer la migration de base de données | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.4 |
| **T-1.1.6** | US-1.1 | Créer RegistrationFormType | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.5 |
| **T-1.1.7** | US-1.1 | Créer RegistrationController avec méthode register() | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.6 |
| **T-1.1.8** | US-1.1 | Implémenter le hashage du mot de passe | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.7 |
| **T-1.1.9** | US-1.1 | Créer la vue Twig register.html.twig | Frontend | 2h | Haute | 1 | ✅ Fait | Dev Frontend | T-1.1.7 |
| **T-1.1.10** | US-1.1 | Ajouter validation JavaScript côté client | Frontend | 2h | Moyenne | 1 | ✅ Fait | Dev Frontend | T-1.1.9 |
| **T-1.1.11** | US-1.1 | Styliser le formulaire d'inscription (CSS) | Frontend | 1h | Basse | 1 | ✅ Fait | Dev Frontend | T-1.1.9 |
| **T-1.1.12** | US-1.1 | Tester l'inscription avec données valides | Test | 1h | Haute | 1 | ✅ Fait | QA | T-1.1.11 |
| **T-1.1.13** | US-1.1 | Tester l'inscription avec données invalides | Test | 1h | Haute | 1 | ✅ Fait | QA | T-1.1.11 |
| **T-1.2.1** | US-1.2 | Configurer Symfony Security (security.yaml) | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.1.5 |
| **T-1.2.2** | US-1.2 | Créer LoginFormType | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.2.3** | US-1.2 | Créer SecurityController avec méthode login() | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.2 |
| **T-1.2.4** | US-1.2 | Implémenter l'authentification par email/password | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.3 |
| **T-1.2.5** | US-1.2 | Configurer les rôles (ROLE_ADMIN, ROLE_ETUDIANT) | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.4 |
| **T-1.2.6** | US-1.2 | Créer la vue Twig login.html.twig | Frontend | 2h | Haute | 1 | ✅ Fait | Dev Frontend | T-1.2.3 |
| **T-1.2.7** | US-1.2 | Implémenter "Se souvenir de moi" (remember_me) | Backend | 1h | Moyenne | 1 | ✅ Fait | Dev Backend | T-1.2.4 |
| **T-1.2.8** | US-1.2 | Redirection selon rôle (Admin→backoffice, Etudiant→frontoffice) | Backend | 2h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.5 |
| **T-1.2.9** | US-1.2 | Gérer les erreurs de connexion (identifiants incorrects) | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.8 |
| **T-1.2.10** | US-1.2 | Mettre à jour lastLoginAt lors de la connexion | Backend | 1h | Moyenne | 1 | ✅ Fait | Dev Backend | T-1.2.8 |
| **T-1.2.11** | US-1.2 | Styliser le formulaire de connexion | Frontend | 1h | Basse | 1 | ✅ Fait | Dev Frontend | T-1.2.6 |
| **T-1.2.12** | US-1.2 | Tester connexion Admin | Test | 1h | Haute | 1 | ✅ Fait | QA | T-1.2.11 |
| **T-1.2.13** | US-1.2 | Tester connexion Etudiant | Test | 1h | Haute | 1 | ✅ Fait | QA | T-1.2.11 |
| **T-1.3.1** | US-1.3 | Configurer la route de déconnexion dans security.yaml | Backend | 0.5h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.3.2** | US-1.3 | Créer le lien de déconnexion dans le menu | Frontend | 0.5h | Haute | 1 | ✅ Fait | Dev Frontend | T-1.3.1 |
| **T-1.3.3** | US-1.3 | Configurer la redirection après déconnexion | Backend | 0.5h | Moyenne | 1 | ✅ Fait | Dev Backend | T-1.3.1 |
| **T-1.3.4** | US-1.3 | Invalider la session utilisateur | Backend | 0.5h | Haute | 1 | ✅ Fait | Dev Backend | T-1.3.3 |
| **T-1.3.5** | US-1.3 | Tester la déconnexion | Test | 1h | Haute | 1 | ✅ Fait | QA | T-1.3.4 |
| **T-1.4.1** | US-1.4 | Créer l'entité PasswordResetToken | Backend | 2h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.1.5 |
| **T-1.4.2** | US-1.4 | Créer la migration pour password_reset_token | Backend | 0.5h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.1 |
| **T-1.4.3** | US-1.4 | Créer le formulaire "Mot de passe oublié" | Backend | 1h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.2 |
| **T-1.4.4** | US-1.4 | Générer un token unique de réinitialisation | Backend | 2h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.3 |
| **T-1.4.5** | US-1.4 | Configurer Symfony Mailer | Backend | 2h | Haute | 2 | 🔄 En cours | Dev Backend | - |
| **T-1.4.6** | US-1.4 | Créer le template email de réinitialisation | Frontend | 2h | Haute | 2 | 🔄 En cours | Dev Frontend | T-1.4.5 |
| **T-1.4.7** | US-1.4 | Implémenter l'envoi d'email avec token | Backend | 2h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.6 |
| **T-1.4.8** | US-1.4 | Créer la page de réinitialisation | Frontend | 2h | Haute | 2 | 🔄 En cours | Dev Frontend | T-1.4.7 |
| **T-1.4.9** | US-1.4 | Valider le token (expiration 1h, usage unique) | Backend | 2h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.8 |
| **T-1.4.10** | US-1.4 | Mettre à jour le mot de passe | Backend | 1h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.9 |
| **T-1.4.11** | US-1.4 | Invalider le token après utilisation | Backend | 1h | Haute | 2 | 🔄 En cours | Dev Backend | T-1.4.10 |
| **T-1.4.12** | US-1.4 | Tester le processus complet | Test | 3h | Haute | 2 | ⏳ À faire | QA | T-1.4.11 |
| **T-1.5.1** | US-1.5 | Créer la route /profile | Backend | 0.5h | Haute | 1 | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.5.2** | US-1.5 | Créer ProfileController | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.5.1 |
| **T-1.5.3** | US-1.5 | Récupérer les infos de l'utilisateur connecté | Backend | 1h | Haute | 1 | ✅ Fait | Dev Backend | T-1.5.2 |
| **T-1.5.4** | US-1.5 | Créer la vue profile.html.twig | Frontend | 3h | Haute | 1 | ✅ Fait | Dev Frontend | T-1.5.3 |
| **T-1.5.5** | US-1.5 | Afficher toutes les informations utilisateur | Frontend | 2h | Haute | 1 | ✅ Fait | Dev Frontend | T-1.5.4 |
| **T-1.5.6** | US-1.5 | Ajouter la photo de profil | Frontend | 2h | Moyenne | 1 | ✅ Fait | Dev Frontend | T-1.5.5 |
| **T-1.5.7** | US-1.5 | Styliser la page profil | Frontend | 2h | Basse | 1 | ✅ Fait | Dev Frontend | T-1.5.6 |
| **T-1.5.8** | US-1.5 | Tester l'affichage du profil | Test | 1h | Haute | 1 | ✅ Fait | QA | T-1.5.7 |
| **T-1.6.1** | US-1.6 | Créer ProfileEditType | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.5.2 |
| **T-1.6.2** | US-1.6 | Créer la route /profile/edit | Backend | 0.5h | Haute | 2 | ✅ Fait | Dev Backend | T-1.6.1 |
| **T-1.6.3** | US-1.6 | Implémenter modification nom, prénom | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.6.2 |
| **T-1.6.4** | US-1.6 | Implémenter modification email (vérif unicité) | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.6.3 |
| **T-1.6.5** | US-1.6 | Implémenter upload photo de profil | Backend | 3h | Haute | 2 | ✅ Fait | Dev Backend | T-1.6.4 |
| **T-1.6.6** | US-1.6 | Valider formats image (jpg, png, max 2MB) | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.6.5 |
| **T-1.6.7** | US-1.6 | Stocker image dans /public/uploads/profiles/ | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.6.6 |
| **T-1.6.8** | US-1.6 | Créer la vue profile_edit.html.twig | Frontend | 2h | Haute | 2 | ✅ Fait | Dev Frontend | T-1.6.2 |
| **T-1.6.9** | US-1.6 | Ajouter prévisualisation de la photo | Frontend | 1h | Moyenne | 2 | ✅ Fait | Dev Frontend | T-1.6.8 |
| **T-1.6.10** | US-1.6 | Message de confirmation après modification | Frontend | 0.5h | Moyenne | 2 | ✅ Fait | Dev Frontend | T-1.6.9 |
| **T-1.6.11** | US-1.6 | Tester la modification | Test | 2h | Haute | 2 | ✅ Fait | QA | T-1.6.10 |

| **T-1.7.1** | US-1.7 | Créer la route /backoffice/users | Backend | 0.5h | Haute | 2 | ✅ Fait | Dev Backend | T-1.2.1 |
| **T-1.7.2** | US-1.7 | Créer UserManagementController | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.1 |
| **T-1.7.3** | US-1.7 | Implémenter recherche par nom (LIKE query) | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.7.4** | US-1.7 | Implémenter recherche par email (LIKE query) | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.3 |
| **T-1.7.5** | US-1.7 | Créer le formulaire de recherche | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.4 |
| **T-1.7.6** | US-1.7 | Afficher résultats dans un tableau | Frontend | 2h | Haute | 2 | ✅ Fait | Dev Frontend | T-1.7.5 |
| **T-1.7.7** | US-1.7 | Ajouter pagination (10 résultats/page) | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.6 |
| **T-1.7.8** | US-1.7 | Ajouter filtres (niveau, statut) | Backend | 2h | Moyenne | 2 | ✅ Fait | Dev Backend | T-1.7.7 |
| **T-1.7.9** | US-1.7 | Créer la vue users_list.html.twig | Frontend | 3h | Haute | 2 | ✅ Fait | Dev Frontend | T-1.7.8 |
| **T-1.7.10** | US-1.7 | Styliser le tableau et filtres | Frontend | 2h | Basse | 2 | ✅ Fait | Dev Frontend | T-1.7.9 |
| **T-1.7.11** | US-1.7 | Tester la recherche | Test | 2h | Haute | 2 | ✅ Fait | QA | T-1.7.10 |
| **T-1.8.1** | US-1.8 | Créer la route /backoffice/users/{id} | Backend | 0.5h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.8.2** | US-1.8 | Créer méthode show() dans UserManagementController | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.8.1 |
| **T-1.8.3** | US-1.8 | Récupérer toutes les infos utilisateur | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.8.2 |
| **T-1.8.4** | US-1.8 | Afficher historique d'activité (UserActivity) | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.8.3 |
| **T-1.8.5** | US-1.8 | Afficher statistiques connexion | Backend | 2h | Moyenne | 2 | ✅ Fait | Dev Backend | T-1.8.4 |
| **T-1.8.6** | US-1.8 | Afficher historique modifications (Audit) | Backend | 2h | Moyenne | 2 | ✅ Fait | Dev Backend | T-1.8.5 |
| **T-1.8.7** | US-1.8 | Créer la vue user_detail.html.twig | Frontend | 3h | Haute | 2 | ✅ Fait | Dev Frontend | T-1.8.6 |
| **T-1.8.8** | US-1.8 | Ajouter boutons d'action (Modifier, Suspendre) | Frontend | 1h | Haute | 2 | ✅ Fait | Dev Frontend | T-1.8.7 |
| **T-1.8.9** | US-1.8 | Styliser la page profil admin | Frontend | 2h | Basse | 2 | ✅ Fait | Dev Frontend | T-1.8.8 |
| **T-1.8.10** | US-1.8 | Tester l'affichage | Test | 1.5h | Haute | 2 | ✅ Fait | QA | T-1.8.9 |
| **T-1.9.1** | US-1.9 | Créer la route /backoffice/users/new | Backend | 0.5h | Haute | 2 | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.9.2** | US-1.9 | Créer AdminUserCreateType | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.9.1 |
| **T-1.9.3** | US-1.9 | Implémenter création d'étudiant | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.9.2 |
| **T-1.9.4** | US-1.9 | Générer mot de passe temporaire | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.9.3 |
| **T-1.9.5** | US-1.9 | Envoyer email avec identifiants | Backend | 2h | Haute | 2 | ✅ Fait | Dev Backend | T-1.9.4 |
| **T-1.9.6** | US-1.9 | Valider unicité email | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.9.5 |
| **T-1.9.7** | US-1.9 | Créer la vue user_create.html.twig | Frontend | 2h | Haute | 2 | ✅ Fait | Dev Frontend | T-1.9.2 |
| **T-1.9.8** | US-1.9 | Validation côté serveur | Backend | 1h | Haute | 2 | ✅ Fait | Dev Backend | T-1.9.6 |
| **T-1.9.9** | US-1.9 | Message de confirmation | Frontend | 0.5h | Moyenne | 2 | ✅ Fait | Dev Frontend | T-1.9.7 |
| **T-1.9.10** | US-1.9 | Logger l'action dans UserActivity | Backend | 1h | Moyenne | 2 | ✅ Fait | Dev Backend | T-1.9.8 |
| **T-1.9.11** | US-1.9 | Tester la création | Test | 2h | Haute | 2 | ✅ Fait | QA | T-1.9.10 |
| **T-1.10.1** | US-1.10 | Créer la route /backoffice/users/{id}/suspend | Backend | 0.5h | Haute | 3 | ✅ Fait | Dev Backend | T-1.7.2 |
| **T-1.10.2** | US-1.10 | Implémenter méthode suspend() | Backend | 1h | Haute | 3 | ✅ Fait | Dev Backend | T-1.10.1 |
| **T-1.10.3** | US-1.10 | Mettre à jour isSuspended = true | Backend | 0.5h | Haute | 3 | ✅ Fait | Dev Backend | T-1.10.2 |
| **T-1.10.4** | US-1.10 | Enregistrer suspendedAt, suspendedBy, reason | Backend | 1h | Haute | 3 | ✅ Fait | Dev Backend | T-1.10.3 |
| **T-1.10.5** | US-1.10 | Créer modal pour raison suspension | Frontend | 2h | Haute | 3 | ✅ Fait | Dev Frontend | T-1.10.4 |
| **T-1.10.6** | US-1.10 | Bloquer connexion comptes suspendus | Backend | 2h | Haute | 3 | ✅ Fait | Dev Backend | T-1.10.4 |
| **T-1.10.7** | US-1.10 | Message d'erreur lors connexion suspendu | Frontend | 1h | Haute | 3 | ✅ Fait | Dev Frontend | T-1.10.6 |
| **T-1.10.8** | US-1.10 | Créer route /backoffice/users/{id}/unsuspend | Backend | 0.5h | Haute | 3 | ✅ Fait | Dev Backend | T-1.10.6 |
| **T-1.10.9** | US-1.10 | Implémenter réactivation compte | Backend | 1h | Haute | 3 | ✅ Fait | Dev Backend | T-1.10.8 |
| **T-1.10.10** | US-1.10 | Logger actions suspension/réactivation | Backend | 1h | Moyenne | 3 | ✅ Fait | Dev Backend | T-1.10.9 |
| **T-1.10.11** | US-1.10 | Commande suspension auto (inactivité) | Backend | 3h | Moyenne | 3 | ✅ Fait | Dev Backend | T-1.10.9 |
| **T-1.10.12** | US-1.10 | Tester suspension/réactivation | Test | 2h | Haute | 3 | ✅ Fait | QA | T-1.10.11 |
| **T-1.11.1** | US-1.11 | Créer la route /backoffice/users/{id}/edit | Backend | 0.5h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.7.2 |
| **T-1.11.2** | US-1.11 | Créer AdminUserEditType | Backend | 2h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.11.1 |
| **T-1.11.3** | US-1.11 | Implémenter modification infos étudiant | Backend | 2h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.11.2 |
| **T-1.11.4** | US-1.11 | Valider les modifications | Backend | 1h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.11.3 |
| **T-1.11.5** | US-1.11 | Créer la vue user_edit.html.twig | Frontend | 2h | Moyenne | 3 | ⏳ À faire | Dev Frontend | T-1.11.2 |
| **T-1.11.6** | US-1.11 | Logger la modification | Backend | 1h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.11.4 |
| **T-1.11.7** | US-1.11 | Tester la modification | Test | 1.5h | Moyenne | 3 | ⏳ À faire | QA | T-1.11.6 |
| **T-1.12.1** | US-1.12 | Installer PhpSpreadsheet ou CSV library | Backend | 0.5h | Basse | 3 | ⏳ À faire | Dev Backend | - |
| **T-1.12.2** | US-1.12 | Créer la route /backoffice/users/export | Backend | 0.5h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.12.1 |
| **T-1.12.3** | US-1.12 | Implémenter export CSV | Backend | 2h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.12.2 |
| **T-1.12.4** | US-1.12 | Implémenter export Excel | Backend | 2h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.12.3 |
| **T-1.12.5** | US-1.12 | Ajouter bouton Export dans la liste | Frontend | 1h | Basse | 3 | ⏳ À faire | Dev Frontend | T-1.12.4 |
| **T-1.12.6** | US-1.12 | Tester l'export | Test | 1h | Basse | 3 | ⏳ À faire | QA | T-1.12.5 |
| **T-1.13.1** | US-1.13 | Créer ChangePasswordType | Backend | 1h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.5.2 |
| **T-1.13.2** | US-1.13 | Créer la route /profile/change-password | Backend | 0.5h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.13.1 |
| **T-1.13.3** | US-1.13 | Valider ancien mot de passe | Backend | 1h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.13.2 |
| **T-1.13.4** | US-1.13 | Valider nouveau mot de passe (règles) | Backend | 1h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.13.3 |
| **T-1.13.5** | US-1.13 | Hasher et sauvegarder nouveau mot de passe | Backend | 1h | Moyenne | 3 | ⏳ À faire | Dev Backend | T-1.13.4 |
| **T-1.13.6** | US-1.13 | Créer la vue change_password.html.twig | Frontend | 2h | Moyenne | 3 | ⏳ À faire | Dev Frontend | T-1.13.2 |
| **T-1.13.7** | US-1.13 | Message de confirmation | Frontend | 0.5h | Basse | 3 | ⏳ À faire | Dev Frontend | T-1.13.6 |
| **T-1.13.8** | US-1.13 | Tester changement mot de passe | Test | 1.5h | Moyenne | 3 | ⏳ À faire | QA | T-1.13.7 |
| **T-1.14.1** | US-1.14 | Créer table login_history | Backend | 1h | Basse | 3 | ⏳ À faire | Dev Backend | - |
| **T-1.14.2** | US-1.14 | Logger chaque connexion (IP, date, user-agent) | Backend | 2h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.14.1 |
| **T-1.14.3** | US-1.14 | Créer la route /backoffice/users/{id}/login-history | Backend | 0.5h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.14.2 |
| **T-1.14.4** | US-1.14 | Afficher historique dans profil admin | Frontend | 2h | Basse | 3 | ⏳ À faire | Dev Frontend | T-1.14.3 |
| **T-1.14.5** | US-1.14 | Ajouter pagination historique | Backend | 1h | Basse | 3 | ⏳ À faire | Dev Backend | T-1.14.4 |
| **T-1.14.6** | US-1.14 | Tester l'historique | Test | 1h | Basse | 3 | ⏳ À faire | QA | T-1.14.5 |

---


## 📊 Récapitulatif par Sprint

| Sprint | User Stories | Nombre de Tâches | Estimation Totale | Statut |
|--------|-------------|------------------|-------------------|--------|
| **Sprint 1** | US-1.1, US-1.2, US-1.3, US-1.5 | 38 tâches | 50.5h | ✅ Terminé |
| **Sprint 2** | US-1.4, US-1.6, US-1.7, US-1.8, US-1.9 | 49 tâches | 71h | 🔄 En cours |
| **Sprint 3** | US-1.10, US-1.11, US-1.12, US-1.13, US-1.14 | 37 tâches | 48h | ⏳ Planifié |
| **TOTAL** | 14 User Stories | **124 tâches** | **169.5h** | - |

---

## 📈 Statistiques Globales

### Par Type de Tâche
| Type | Nombre | Estimation | Pourcentage |
|------|--------|------------|-------------|
| Backend | 78 | 105h | 62% |
| Frontend | 32 | 48.5h | 29% |
| Test | 14 | 16h | 9% |

### Par Statut
| Statut | Nombre | Pourcentage |
|--------|--------|-------------|
| ✅ Fait | 83 | 67% |
| 🔄 En cours | 12 | 10% |
| ⏳ À faire | 29 | 23% |

### Par Priorité
| Priorité | Nombre | Estimation |
|----------|--------|------------|
| Haute | 92 | 128h |
| Moyenne | 24 | 32.5h |
| Basse | 8 | 9h |

---

## 🎯 Critères d'Acceptation Globaux

### Fonctionnels
- ✅ Inscription avec validation complète
- ✅ Connexion avec gestion des rôles
- ✅ Déconnexion sécurisée
- 🔄 Réinitialisation mot de passe par email
- ✅ Consultation et modification de profil
- ✅ Recherche et gestion des utilisateurs (Admin)
- ✅ Suspension/Réactivation de comptes
- ⏳ Export des données utilisateurs
- ⏳ Changement de mot de passe
- ⏳ Historique des connexions

### Techniques
- ✅ Architecture MVC respectée
- ✅ Sécurité Symfony implémentée
- ✅ Validation côté serveur et client
- ✅ Gestion des erreurs
- ✅ Logging des actions importantes
- ✅ Audit des modifications (Etudiant)
- ✅ Responsive design
- 🔄 Tests unitaires et fonctionnels
- ⏳ Documentation technique

### Sécurité
- ✅ Hashage des mots de passe (bcrypt)
- ✅ Protection CSRF
- ✅ Validation des entrées
- ✅ Gestion des sessions
- ✅ Contrôle d'accès par rôle
- 🔄 Tokens de réinitialisation sécurisés
- ⏳ Logging des tentatives de connexion

---

## 🔧 Technologies et Outils

### Backend
- Symfony 6.4
- Doctrine ORM
- Symfony Security
- Symfony Mailer
- Symfony Validator

### Frontend
- Twig
- Bootstrap 5
- JavaScript (Vanilla)
- CSS3

### Base de Données
- MySQL 8.0
- Migrations Doctrine

### Bundles
- SimpleThings EntityAudit Bundle
- UserActivity Bundle (Custom)
- PhpSpreadsheet (pour export)

### Outils de Développement
- Git
- Composer
- PHPUnit (tests)
- Symfony Profiler

---

## 📝 Notes Importantes

### Fonctionnalités Déjà Implémentées ✅
1. Système d'authentification complet (inscription, connexion, déconnexion)
2. Gestion des profils utilisateurs
3. Interface d'administration avec recherche et filtres
4. Système de suspension automatique après inactivité
5. Audit des modifications sur les étudiants
6. Suivi d'activité utilisateur
7. Upload de photos de profil
8. Sidebar fixe dans le backoffice

### En Cours de Développement 🔄
1. Réinitialisation de mot de passe par email
2. Tests automatisés complets

### À Développer ⏳
1. Modification des utilisateurs par l'admin
2. Export CSV/Excel des utilisateurs
3. Changement de mot de passe depuis le profil
4. Historique détaillé des connexions

### Améliorations Futures 🚀
1. Authentification à deux facteurs (2FA)
2. Connexion via réseaux sociaux (OAuth)
3. Notifications push
4. Dashboard avec statistiques avancées
5. Gestion des permissions granulaires
6. API REST pour mobile
7. Internationalisation (FR/EN/AR)
8. Mode sombre

---

## 🧪 Plan de Tests

### Tests Unitaires
- [ ] Tests des entités (User, Etudiant, Admin)
- [ ] Tests des services (UserService, AuthService)
- [ ] Tests des validateurs
- [ ] Tests des formulaires

### Tests Fonctionnels
- [x] Test inscription utilisateur
- [x] Test connexion/déconnexion
- [ ] Test réinitialisation mot de passe
- [x] Test modification profil
- [x] Test recherche utilisateurs
- [x] Test suspension compte
- [ ] Test export données

### Tests de Sécurité
- [x] Test protection CSRF
- [x] Test hashage mots de passe
- [x] Test contrôle d'accès par rôle
- [ ] Test tokens de réinitialisation
- [ ] Test injection SQL
- [ ] Test XSS

### Tests de Performance
- [ ] Test charge page connexion
- [ ] Test recherche avec 10000+ utilisateurs
- [ ] Test upload photos multiples

---

## 📅 Planning Prévisionnel

### Sprint 1 (2 semaines) - ✅ TERMINÉ
- Semaine 1: US-1.1, US-1.2, US-1.3
- Semaine 2: US-1.5

### Sprint 2 (2 semaines) - 🔄 EN COURS
- Semaine 3: US-1.4, US-1.6
- Semaine 4: US-1.7, US-1.8, US-1.9

### Sprint 3 (2 semaines) - ⏳ PLANIFIÉ
- Semaine 5: US-1.10, US-1.11, US-1.13
- Semaine 6: US-1.12, US-1.14, Tests finaux

---

## 👥 Équipe et Rôles

| Rôle | Responsabilité | Tâches |
|------|---------------|--------|
| **Dev Backend** | Développement Symfony | 78 tâches |
| **Dev Frontend** | Développement Twig/JS/CSS | 32 tâches |
| **QA** | Tests et validation | 14 tâches |
| **Product Owner** | Validation fonctionnelle | Revues |
| **Scrum Master** | Animation sprints | Coordination |

---

## 🎓 Livrables

### Documentation
- [x] Product Backlog
- [x] Sprint Backlog détaillé
- [ ] Guide utilisateur
- [ ] Documentation technique
- [ ] Guide d'installation
- [ ] Guide de déploiement

### Code
- [x] Code source versionné (Git)
- [x] Migrations base de données
- [ ] Tests automatisés
- [ ] Configuration production

### Démonstration
- [x] Environnement de développement
- [ ] Environnement de staging
- [ ] Présentation finale

---

**Date de création**: 2026-02-22  
**Dernière mise à jour**: 2026-02-22  
**Version**: 2.0  
**Statut global**: 67% complété
