# 🎯 GUIDE TESTS EMAILS POUR VALIDATION

## ⚠️ IMPORTANT : Quota SendGrid limité à 100 emails/jour

Pour éviter de dépasser le quota pendant la validation, suis ces règles strictes :

---

## 1️⃣ NE PAS TESTER L'ENVOI MASSIF DE CERTIFICATS

### ❌ À NE PAS FAIRE :
```bash
# Cette commande envoie des certificats à TOUS les participants
php bin/console app:send-certificates
```

### ✅ À FAIRE À LA PLACE :
- **Montre le code** dans `src/Command/SendCertificatesCommand.php`
- **Explique la logique** : "Cette commande récupère tous les événements terminés et envoie les certificats"
- **Montre les logs** dans `var/log/dev.log` des envois précédents

---

## 2️⃣ TESTER AVEC UN SEUL ÉVÉNEMENT ET UNE SEULE ÉQUIPE

### Stratégie de test économique :

#### A. Créer un événement de test
1. Va dans le backoffice
2. Crée un événement "Test Validation" avec :
   - Date début : aujourd'hui
   - Date fin : aujourd'hui + 2h
   - Type : Conférence
   - Statut : Brouillon

#### B. Créer une équipe de test avec 2-3 étudiants maximum
1. Crée une équipe "Team Test" avec seulement 2-3 membres
2. Utilise des emails de test (tes propres emails ou ceux de tes camarades présents)

#### C. Tester le workflow complet (≈ 6-8 emails max)
```
1. Participation → Email de confirmation (2-3 emails)
2. Démarrer l'événement → Email "Event Started" (2-3 emails)
3. Terminer l'événement → Email avec certificat (2-3 emails)
```

**Total : 6-9 emails maximum** ✅

---

## 3️⃣ DÉMONSTRATION SANS ENVOI D'EMAILS

### Option 1 : Montrer les templates d'emails
- Ouvre `templates/emails/participation_confirmation.html.twig`
- Ouvre `templates/emails/event_cancelled.html.twig`
- Ouvre `templates/emails/event_started.html.twig`
- Explique le design et le contenu

### Option 2 : Montrer les logs d'envois précédents
```bash
# Afficher les logs des emails envoyés
php bin/console debug:log --env=dev | findstr "Email envoyé"
```

Ou ouvre directement `var/log/dev.log` et cherche :
- `✓ Email envoyé`
- `✓ Certificat envoyé`
- `🚀 Événement démarré`
- `✅ Événement terminé`

### Option 3 : Montrer le code sans exécuter
- Ouvre `src/Service/EmailService.php`
- Explique chaque méthode :
  - `sendParticipationConfirmation()` : Email + QR code + Badge PDF + fichier .ics
  - `sendEventCancellation()` : Email d'annulation
  - `sendEventStarted()` : Email de démarrage
  - `sendCertificate()` : Email avec certificat PDF

---

## 4️⃣ COMMANDES À UTILISER PENDANT LA VALIDATION

### ✅ Commandes SANS envoi d'emails (safe) :

```bash
# Afficher les événements et leurs statuts
php bin/console doctrine:query:sql "SELECT id, titre, workflow_status FROM evenement"

# Afficher les participations
php bin/console doctrine:query:sql "SELECT * FROM participation"

# Vider le cache
php bin/console cache:clear

# Lister les workflows disponibles
php bin/console debug:workflow evenement_publishing
```

### ⚠️ Commandes AVEC envoi d'emails (limiter) :

```bash
# Envoyer des rappels (seulement pour événements dans 3 jours)
php bin/console app:send-event-reminders

# Mettre à jour les statuts (peut déclencher des emails si événements démarrent/terminent)
php bin/console app:update-event-status
```

---

## 5️⃣ SCÉNARIO DE DÉMONSTRATION OPTIMAL

### Scénario complet (10-12 emails max) :

1. **Créer un événement de test** (0 email)
2. **Créer une équipe avec 2 étudiants** (0 email)
3. **Participer à l'événement** (2 emails de confirmation)
4. **Accepter la participation** (0 email)
5. **Démarrer l'événement via workflow** (2 emails "Event Started")
6. **Terminer l'événement via workflow** (2 emails avec certificats)
7. **Annuler un autre événement** (2 emails d'annulation)

**Total : 8 emails** ✅

### Le reste : MONTRER LE CODE et EXPLIQUER

---

## 6️⃣ SI LE QUOTA EST DÉPASSÉ PENDANT LA VALIDATION

### Message à dire au professeur :
> "Nous avons dépassé le quota SendGrid de 100 emails/jour car nous avons testé l'envoi massif de certificats hier. Le système détecte automatiquement cette erreur (code 403) et arrête l'envoi pour éviter de spammer les logs. Voici le code qui gère cette situation..."

Puis montre le code dans `EvenementWorkflowSubscriber.php` lignes 265-275 :
```php
// Détecter si c'est une erreur de quota (code 403)
if (strpos($e->getMessage(), '403') !== false) {
    $quotaExceeded = true;
    $this->logger->error('❌ QUOTA SENDGRID DÉPASSÉ');
}
```

---

## 7️⃣ CHECKLIST AVANT LA VALIDATION

- [ ] Quota SendGrid vérifié (doit être < 90/100)
- [ ] Événement de test créé avec date aujourd'hui
- [ ] Équipe de test créée avec 2-3 membres maximum
- [ ] Logs vérifiés dans `var/log/dev.log`
- [ ] Cache vidé : `php bin/console cache:clear`
- [ ] Serveur démarré : `symfony server:start` ou `php -S localhost:8000 -t public`

---

## 8️⃣ QUOTA ACTUEL

**Aujourd'hui (25 février)** : 104/100 emails envoyés ❌

**Demain (26 février après 1h du matin)** : 0/100 emails disponibles ✅

**Recommandation** : Garde 10-15 emails pour les tests, le reste pour les explications et démonstrations de code.

---

## 🎓 RÉSUMÉ

- ✅ Teste avec 1 événement et 1 équipe de 2-3 personnes
- ✅ Montre le code et explique au lieu d'envoyer des emails
- ✅ Utilise les logs pour prouver que ça fonctionne
- ❌ N'utilise PAS `php bin/console app:send-certificates`
- ❌ Ne teste PAS avec plusieurs équipes de 10 personnes

**Budget emails pour validation : 10-15 emails maximum** 🎯
