# 🏃 SPRINT BACKLOG - Module Gestion des Événements (PARTIE 2/3)

## 📋 User Stories US-5.16 à US-5.30

---

## US-5.16 à US-5.19: Validations Système

**Total Estimation**: 8h 45min

### US-5.16: Empêcher participation sans équipe
- Vérification dans le contrôleur que l'étudiant appartient à une équipe
- Redirection avec message d'erreur si pas d'équipe
- Tests de validation

### US-5.17: Empêcher participation à événement annulé
- Vérification dans validateParticipation() si isCanceled = true
- Refus automatique avec message
- Tests avec événements annulés

### US-5.18: Limiter à la capacité maximale
- Déjà implémenté dans US-5.13
- Tests supplémentaires de cas limites

### US-5.19: Vérifier taille d'équipe (4-6 membres)
- Validation Assert\Count dans l'entité Equipe
- Messages d'erreur personnalisés
- Tests avec 3, 4, 6, 7 membres

---

## US-5.20 à US-5.22: Gestion Manuelle Participations (Admin)

**Total Estimation**: 12h 30min

### US-5.20: Consulter participations en attente
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.20.1 | Créer ParticipationController backoffice | 15 min |
| T5.20.2 | Route /backoffice/participation avec index() | 20 min |
| T5.20.3 | Filtrer par statut "En attente" | 25 min |
| T5.20.4 | Template index.html.twig avec tableau | 45 min |
| T5.20.5 | Afficher détails (équipe, événement, membres) | 35 min |
| T5.20.6 | Boutons Accepter/Refuser | 30 min |
| T5.20.7 | Tests affichage | 20 min |

### US-5.21: Accepter une participation
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.21.1 | Route /backoffice/participation/{id}/accept | 15 min |
| T5.21.2 | Méthode accept() avec setStatut(ACCEPTE) | 20 min |
| T5.21.3 | Appeler validateParticipation() pour vérifier | 25 min |
| T5.21.4 | Envoyer email de confirmation | 30 min |
| T5.21.5 | Tests acceptation | 20 min |

### US-5.22: Refuser une participation
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.22.1 | Route /backoffice/participation/{id}/refuse | 15 min |
| T5.22.2 | Méthode refuse() avec setStatut(REFUSE) | 20 min |
| T5.22.3 | Suppression automatique après refus | 25 min |
| T5.22.4 | Tests refus | 20 min |

---

## US-5.23 à US-5.27: Emails Automatiques

**Total Estimation**: 28h 15min

### US-5.23: Email confirmation avec QR code et badge
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.23.1 | Installer SendGrid via composer | 10 min |
| T5.23.2 | Configurer MAILER_DSN dans .env.local | 15 min |
| T5.23.3 | Créer EmailService avec MailerInterface | 25 min |
| T5.23.4 | Méthode sendParticipationConfirmation() | 45 min |
| T5.23.5 | Générer QR code via API externe | 35 min |
| T5.23.6 | Créer BadgeService pour générer PDF | 60 min |
| T5.23.7 | Intégrer dompdf pour génération PDF | 30 min |
| T5.23.8 | Créer template email participation_confirmation.html.twig | 50 min |
| T5.23.9 | Générer fichier .ics pour calendrier | 40 min |
| T5.23.10 | Attacher QR code, badge PDF, .ics à l'email | 35 min |
| T5.23.11 | Appeler sendParticipationConfirmation() après acceptation | 20 min |
| T5.23.12 | Tests envoi email | 30 min |

### US-5.24: Email annulation événement
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.24.1 | Méthode sendEventCancellation() dans EmailService | 30 min |
| T5.24.2 | Template email event_cancelled.html.twig | 40 min |
| T5.24.3 | Créer EvenementWorkflowSubscriber | 35 min |
| T5.24.4 | Écouter événement workflow.entered.annule | 25 min |
| T5.24.5 | Méthode onAnnule() pour envoyer emails | 40 min |
| T5.24.6 | Parcourir toutes participations acceptées | 30 min |
| T5.24.7 | Envoyer email à chaque membre de chaque équipe | 35 min |
| T5.24.8 | Logger les envois (succès/échecs) | 25 min |
| T5.24.9 | Tests envoi emails annulation | 30 min |

### US-5.25: Email démarrage événement
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.25.1 | Méthode sendEventStarted() dans EmailService | 30 min |
| T5.25.2 | Template email event_started.html.twig | 40 min |
| T5.25.3 | Écouter événement workflow.entered.en_cours | 20 min |
| T5.25.4 | Méthode onEnCours() pour envoyer emails | 35 min |
| T5.25.5 | Tests envoi emails démarrage | 25 min |

### US-5.26: Email rappel 3 jours avant
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.26.1 | Créer SendEventRemindersCommand | 35 min |
| T5.26.2 | Récupérer événements dans 3 jours | 30 min |
| T5.26.3 | Méthode sendEventReminder() dans EmailService | 30 min |
| T5.26.4 | Template email event_reminder.html.twig | 35 min |
| T5.26.5 | Envoyer à tous les participants | 30 min |
| T5.26.6 | Configurer cron job (documentation) | 20 min |
| T5.26.7 | Tests commande | 25 min |

### US-5.27: Certificats PDF automatiques
| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.27.1 | Créer CertificateService | 40 min |
| T5.27.2 | Méthode generateCertificate() avec dompdf | 60 min |
| T5.27.3 | Design template certificat (HTML/CSS) | 75 min |
| T5.27.4 | Ajouter logo, signature, QR code | 45 min |
| T5.27.5 | Méthode sendCertificate() dans EmailService | 30 min |
| T5.27.6 | Créer SendCertificatesCommand | 35 min |
| T5.27.7 | Récupérer événements terminés | 25 min |
| T5.27.8 | Envoyer certificat à chaque participant | 35 min |
| T5.27.9 | Tests génération et envoi | 30 min |

---

## US-5.28: Calendrier Visuel

**Total Estimation**: 10h 45min

| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.28.1 | Installer tattali/calendar-bundle | 10 min |
| T5.28.2 | Créer config/packages/calendar.yaml | 15 min |
| T5.28.3 | Créer CalendarSubscriber | 35 min |
| T5.28.4 | Implémenter onCalendarSetData() | 40 min |
| T5.28.5 | Convertir événements en CalendarEvent | 30 min |
| T5.28.6 | Route /events/calendar | 15 min |
| T5.28.7 | Template calendar.html.twig avec FullCalendar | 60 min |
| T5.28.8 | Intégrer FullCalendar v6.1.10 (CDN) | 25 min |
| T5.28.9 | Configurer 4 vues (Month, Week, Day, List) | 35 min |
| T5.28.10 | Styliser avec couleurs par type d'événement | 40 min |
| T5.28.11 | Ajouter modal popup au clic sur événement | 50 min |
| T5.28.12 | Intégrer navbar frontoffice | 25 min |
| T5.28.13 | Tests affichage calendrier | 30 min |

---

## US-5.29: Météo Prévue

**Total Estimation**: 6h 30min

| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.29.1 | Créer compte OpenWeatherMap API | 10 min |
| T5.29.2 | Configurer WEATHER_API_KEY dans .env.local | 10 min |
| T5.29.3 | Créer WeatherService | 35 min |
| T5.29.4 | Méthode getWeatherForEvent() | 50 min |
| T5.29.5 | Gérer météo actuelle vs prévisions (5 jours) | 40 min |
| T5.29.6 | Méthode getWeatherEmoji() pour icônes | 25 min |
| T5.29.7 | Intégrer dans FrontofficeEvenementController | 30 min |
| T5.29.8 | Afficher section météo dans index.html.twig | 45 min |
| T5.29.9 | Styliser avec gradients et émojis | 35 min |
| T5.29.10 | Messages personnalisés selon météo | 30 min |
| T5.29.11 | Gérer erreurs API (timeout, clé invalide) | 25 min |
| T5.29.12 | Tests affichage météo | 25 min |

---

## US-5.30: Donner Feedback

**Total Estimation**: 8h 45min

| ID Tâche | Tâches | Estimation |
|----------|--------|------------|
| T5.30.1 | Ajouter colonne feedbacks (JSON) dans Participation | 20 min |
| T5.30.2 | Créer migration pour feedbacks | 15 min |
| T5.30.3 | Créer SentimentFeedback enum | 25 min |
| T5.30.4 | Méthodes addFeedback(), getFeedbackByEtudiant() | 40 min |
| T5.30.5 | Créer FeedbackController | 25 min |
| T5.30.6 | Route /feedback/{participationId}/form | 15 min |
| T5.30.7 | Méthode form() avec vérifications | 35 min |
| T5.30.8 | Route POST /feedback/{participationId}/submit | 20 min |
| T5.30.9 | Méthode submit() avec sauvegarde JSON | 40 min |
| T5.30.10 | Template feedback/form.html.twig | 60 min |
| T5.30.11 | Système de notation par étoiles (1-5) | 45 min |
| T5.30.12 | Sélection de sentiment avec émojis | 35 min |
| T5.30.13 | Ratings par catégories (organisation, contenu, lieu, animation) | 50 min |
| T5.30.14 | Champ commentaire libre | 20 min |
| T5.30.15 | Styliser avec design moderne | 40 min |
| T5.30.16 | Tests soumission feedback | 30 min |

---

**FIN PARTIE 2/3**

**Total Estimation Partie 2**: 75h 30min  
**User Stories Couvertes**: US-5.16 à US-5.30 (15 US)
