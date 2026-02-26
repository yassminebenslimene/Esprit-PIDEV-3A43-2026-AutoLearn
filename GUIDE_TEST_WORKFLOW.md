# 🧪 Guide de Test - Workflow Component pour Événements

## 📋 Prérequis

Avant de commencer les tests, assure-toi que:

✅ La migration a été exécutée: `php bin/console doctrine:migrations:migrate`  
✅ Le serveur Symfony est démarré: `symfony server:start` ou `php -S localhost:8000 -t public`  
✅ Tu as des événements dans la base de données avec des participations acceptées  
✅ Tu as configuré SendGrid dans `.env.local` pour l'envoi d'emails  

---

## 🎯 Plan de Test

### Test 1: Vérifier la Configuration du Workflow
### Test 2: Tester la Transition Manuelle (Annulation)
### Test 3: Tester les Transitions Automatiques (Command)
### Test 4: Vérifier l'Envoi d'Emails
### Test 5: Vérifier les Logs et l'Audit Trail
### Test 6: Tester les Guards (Conditions)

---

## ✅ Test 1: Vérifier la Configuration du Workflow

### Étape 1.1: Vérifier que le workflow est bien enregistré

```bash
php bin/console debug:container | findstr workflow
```

**Résultat attendu**: Tu devrais voir `evenement_publishing.state_machine`

### Étape 1.2: Lister les workflows disponibles

```bash
php bin/console debug:autowiring workflow
```

**Résultat attendu**: Tu devrais voir `WorkflowInterface $evenementPublishingStateMachine`

### Étape 1.3: Vérifier la configuration du workflow

```bash
php bin/console debug:config framework workflows
```

**Résultat attendu**: Tu devrais voir la configuration complète avec les places et transitions

---

## ✅ Test 2: Tester la Transition Manuelle (Annulation)

### Scénario: Annuler un événement depuis le backoffice

#### Étape 2.1: Créer un événement de test

1. Va sur `http://localhost:8000/backoffice/evenement`
2. Clique sur "➕ Ajouter un événement"
3. Remplis le formulaire:
   - **Titre**: Test Workflow - Conférence IA
   - **Description**: Événement de test pour le workflow
   - **Type**: Conférence
   - **Lieu**: Salle A
   - **Date début**: Demain à 10h00
   - **Date fin**: Demain à 12h00
   - **Nb Max**: 5
4. Clique sur "Enregistrer"

**Résultat attendu**: 
- L'événement est créé avec `workflowStatus = 'planifie'`
- Le statut affiché est "Planifié"

#### Étape 2.2: Créer une équipe et une participation

1. Va sur le frontoffice: `http://localhost:8000/`
2. Connecte-toi en tant qu'étudiant
3. Crée une équipe avec au moins 2 membres
4. Participe à l'événement créé
5. Va sur le backoffice et accepte la participation

**Résultat attendu**: La participation est acceptée

#### Étape 2.3: Annuler l'événement via le bouton

1. Retourne sur `http://localhost:8000/backoffice/evenement`
2. Trouve l'événement "Test Workflow - Conférence IA"
3. Clique sur le bouton "❌ Annuler"
4. Confirme l'annulation dans la popup

**Résultat attendu**:
- ✅ Message de succès: "L'événement a été annulé avec succès"
- ✅ Le statut de l'événement passe à "Annulé"
- ✅ Le bouton "❌ Annuler" disparaît (car déjà annulé)
- ✅ Tous les membres de l'équipe participante reçoivent un email d'annulation

#### Étape 2.4: Vérifier l'email d'annulation

Vérifie ta boîte email (ou les logs SendGrid):

**Contenu attendu**:
- Sujet: "⚠️ Event Cancelled - Test Workflow - Conférence IA"
- Corps: Template professionnel avec les détails de l'événement annulé

---

## ✅ Test 3: Tester les Transitions Automatiques (Command)

### Scénario: Démarrer et terminer automatiquement des événements

#### Étape 3.1: Créer un événement qui devrait démarrer maintenant

1. Va sur `http://localhost:8000/backoffice/evenement/new`
2. Crée un événement avec:
   - **Titre**: Test Auto Start
   - **Date début**: Il y a 5 minutes (dans le passé)
   - **Date fin**: Dans 2 heures
   - **Autres champs**: Remplis normalement
3. Enregistre

**État initial**: `workflowStatus = 'planifie'`

#### Étape 3.2: Exécuter la commande de mise à jour

```bash
php bin/console app:update-evenement-workflow
```

**Résultat attendu**:
```
Mise à jour automatique des statuts d'événements
================================================

Date/Heure actuelle: 2026-02-22 15:30:00
Nombre d'événements à vérifier: X

✓ Événement "Test Auto Start" (ID: X) démarré

Résumé
======
Action                  Nombre
Événements démarrés     1
Événements terminés     0
Total des transitions   1

[OK] Mise à jour terminée avec succès!
```

#### Étape 3.3: Vérifier dans le backoffice

1. Rafraîchis la page `http://localhost:8000/backoffice/evenement`
2. Vérifie que "Test Auto Start" a le statut "En cours"

**Résultat attendu**: `workflowStatus = 'en_cours'` et statut = "En cours"

#### Étape 3.4: Créer un événement qui devrait être terminé

1. Crée un événement avec:
   - **Titre**: Test Auto Finish
   - **Date début**: Il y a 3 heures
   - **Date fin**: Il y a 1 heure (dans le passé)
2. Enregistre

#### Étape 3.5: Exécuter la commande à nouveau

```bash
php bin/console app:update-evenement-workflow
```

**Résultat attendu**:
```
✓ Événement "Test Auto Start" (ID: X) démarré
✓ Événement "Test Auto Finish" (ID: Y) terminé

Résumé
======
Action                  Nombre
Événements démarrés     1
Événements terminés     1
Total des transitions   2
```

---

## ✅ Test 4: Vérifier l'Envoi d'Emails

### Scénario: Vérifier que les emails sont envoyés lors des transitions

#### Étape 4.1: Créer un événement avec participations

1. Crée un événement "Test Email Workflow"
2. Crée 2 équipes avec 2 étudiants chacune
3. Fais participer les 2 équipes
4. Accepte les 2 participations dans le backoffice

**Total**: 4 étudiants devraient recevoir des emails

#### Étape 4.2: Démarrer l'événement manuellement

Modifie la date de début pour qu'elle soit dans le passé, puis exécute:

```bash
php bin/console app:update-evenement-workflow
```

**Résultat attendu**:
- ✅ 4 emails envoyés (1 par étudiant)
- ✅ Sujet: "🚀 Event Started - Test Email Workflow"
- ✅ Corps: Template avec détails de l'événement

#### Étape 4.3: Annuler l'événement

1. Va sur le backoffice
2. Clique sur "❌ Annuler" pour l'événement "Test Email Workflow"

**Résultat attendu**:
- ✅ 4 emails d'annulation envoyés
- ✅ Sujet: "⚠️ Event Cancelled - Test Email Workflow"

---

## ✅ Test 5: Vérifier les Logs et l'Audit Trail

### Scénario: Vérifier que toutes les transitions sont loggées

#### Étape 5.1: Consulter les logs

```bash
type var\log\dev.log | findstr "Transition d'événement"
```

**Résultat attendu**: Tu devrais voir des entrées comme:

```json
{
    "message": "Transition d'événement",
    "context": {
        "evenement_id": 5,
        "evenement_titre": "Test Workflow - Conférence IA",
        "transition": "annuler",
        "from": ["planifie"],
        "to": ["annule"],
        "user": "admin@autolearn.com",
        "timestamp": "2026-02-22 15:30:00",
        "workflow": "evenement_publishing"
    }
}
```

#### Étape 5.2: Vérifier les logs d'envoi d'emails

```bash
type var\log\dev.log | findstr "Email envoyé"
```

**Résultat attendu**:

```json
{
    "message": "Email envoyé",
    "context": {
        "type": "cancelled",
        "evenement_id": 5,
        "student_email": "etudiant1@example.com",
        "team": "Équipe Alpha"
    }
}
```

#### Étape 5.3: Vérifier le résumé d'envoi

```bash
type var\log\dev.log | findstr "Envoi d'emails terminé"
```

**Résultat attendu**:

```json
{
    "message": "Envoi d'emails terminé",
    "context": {
        "type": "cancelled",
        "evenement_id": 5,
        "emails_sent": 4,
        "emails_failed": 0
    }
}
```

---

## ✅ Test 6: Tester les Guards (Conditions)

### Scénario: Vérifier que les guards bloquent les transitions invalides

#### Étape 6.1: Tester le guard "demarrer"

Le guard empêche de démarrer un événement si la date de début n'est pas encore arrivée.

**Test via console PHP**:

```bash
php bin/console debug:container evenement_publishing.state_machine
```

Puis dans un contrôleur de test ou via Tinker:

```php
// Créer un événement futur
$evenement = new Evenement();
$evenement->setDateDebut(new \DateTime('+2 days'));
$evenement->setWorkflowStatus('planifie');

// Essayer de démarrer
$workflow->can($evenement, 'demarrer'); // Devrait retourner false
```

#### Étape 6.2: Tester le guard "terminer"

Le guard empêche de terminer un événement si la date de fin n'est pas encore passée.

```php
// Créer un événement en cours
$evenement = new Evenement();
$evenement->setDateDebut(new \DateTime('-1 hour'));
$evenement->setDateFin(new \DateTime('+1 hour'));
$evenement->setWorkflowStatus('en_cours');

// Essayer de terminer
$workflow->can($evenement, 'terminer'); // Devrait retourner false
```

---

## 🔍 Checklist de Validation Complète

### Configuration
- [ ] Le workflow est enregistré dans Symfony
- [ ] La colonne `workflow_status` existe dans la table `evenement`
- [ ] Les templates affichent le bouton "Annuler" correctement

### Transitions Manuelles
- [ ] Le bouton "Annuler" fonctionne dans index.html.twig
- [ ] Le bouton "Annuler" fonctionne dans edit.html.twig
- [ ] Le bouton disparaît pour les événements déjà annulés
- [ ] Le bouton disparaît pour les événements terminés
- [ ] Message de confirmation avant annulation
- [ ] Message de succès après annulation

### Transitions Automatiques
- [ ] La commande `app:update-evenement-workflow` fonctionne
- [ ] Les événements passent de "planifie" à "en_cours" automatiquement
- [ ] Les événements passent de "en_cours" à "termine" automatiquement
- [ ] Les événements annulés ne sont pas affectés par la commande

### Envoi d'Emails
- [ ] Email envoyé quand événement démarre (transition: demarrer)
- [ ] Email envoyé quand événement annulé (transition: annuler)
- [ ] Tous les membres de toutes les équipes participantes reçoivent l'email
- [ ] Les emails ont le bon template (event_started.html.twig / event_cancelled.html.twig)
- [ ] Les emails contiennent les bonnes informations (nom, équipe, événement, date, lieu)

### Logs et Audit Trail
- [ ] Toutes les transitions sont loggées dans var/log/dev.log
- [ ] Les logs contiennent: qui, quand, quelle transition, from, to
- [ ] Les logs d'envoi d'emails sont présents
- [ ] Les logs d'erreurs sont présents en cas de problème

### Guards
- [ ] Impossible de démarrer un événement avant sa date de début
- [ ] Impossible de terminer un événement avant sa date de fin
- [ ] Possible d'annuler depuis "planifie" ou "en_cours"
- [ ] Impossible d'annuler un événement déjà terminé

### Synchronisation Status
- [ ] `workflowStatus` et `status` (enum) sont synchronisés
- [ ] planifie → PLANIFIE
- [ ] en_cours → EN_COURS
- [ ] termine → PASSE
- [ ] annule → ANNULE

---

## 🐛 Dépannage

### Problème: Le bouton "Annuler" n'apparaît pas

**Solution**: Vérifie que la propriété `can_annuler` est bien passée au template dans le contrôleur:

```php
return $this->render('backoffice/evenement/edit.html.twig', [
    'evenement' => $evenement,
    'form' => $form,
    'can_annuler' => $this->evenementPublishingStateMachine->can($evenement, 'annuler'),
]);
```

### Problème: Erreur "Service not found"

**Solution**: Vide le cache:

```bash
php bin/console cache:clear
```

### Problème: Les emails ne sont pas envoyés

**Solution**: Vérifie la configuration SendGrid dans `.env.local`:

```env
MAILER_DSN=sendgrid://YOUR_API_KEY@default
```

### Problème: La commande ne trouve pas les événements

**Solution**: Vérifie que les événements ont `isCanceled = false`:

```sql
SELECT id, titre, workflow_status, is_canceled FROM evenement;
```

### Problème: Les logs ne s'affichent pas

**Solution**: Vérifie le niveau de log dans `config/packages/monolog.yaml`:

```yaml
monolog:
    handlers:
        main:
            level: info  # Doit être "info" ou "debug"
```

---

## 📊 Résultats Attendus

Après avoir complété tous les tests, tu devrais avoir:

✅ **3 événements de test** avec différents statuts (planifié, en cours, terminé, annulé)  
✅ **Logs complets** dans `var/log/dev.log` avec toutes les transitions  
✅ **Emails envoyés** aux participants lors des transitions  
✅ **Boutons fonctionnels** dans le backoffice  
✅ **Transitions automatiques** via la commande  
✅ **Guards actifs** empêchant les transitions invalides  

---

## 🎓 Prochaines Étapes

Une fois les tests validés, tu peux:

1. **Configurer un cron job** pour exécuter la commande automatiquement:
   ```bash
   # Toutes les 5 minutes
   */5 * * * * cd /path/to/project && php bin/console app:update-evenement-workflow
   ```

2. **Ajouter des fonctionnalités supplémentaires**:
   - Génération automatique de certificats à la fin d'un événement
   - Envoi de rappels 3 jours avant l'événement
   - Archivage automatique des événements terminés
   - Notifications push aux participants

3. **Créer des tests unitaires** pour le workflow:
   ```php
   // tests/Workflow/EvenementWorkflowTest.php
   public function testCanAnnulerFromPlanifie(): void
   {
       $evenement = new Evenement();
       $evenement->setWorkflowStatus('planifie');
       
       $this->assertTrue($this->workflow->can($evenement, 'annuler'));
   }
   ```

---

**Auteur**: Kiro AI Assistant  
**Date**: 22 Février 2026  
**Version**: 1.0
