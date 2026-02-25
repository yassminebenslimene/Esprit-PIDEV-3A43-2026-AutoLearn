# ✅ Test Rapide - Workflow Fonctionnel!

## 🎉 Statut: Workflow Configuré et Opérationnel

### ✅ Vérifications Effectuées

1. **Service workflow enregistré**: ✅
   ```
   WorkflowInterface $evenementPublishingStateMachine
   ```

2. **Configuration complète**: ✅
   - Places: planifie, en_cours, termine, annule
   - Transitions: demarrer, terminer, annuler
   - Audit trail: activé

3. **Commande fonctionnelle**: ✅
   ```
   php bin/console app:update-evenement-workflow
   ```
   Résultat: 7 événements vérifiés

4. **Serveur démarré**: ✅
   ```
   http://localhost:8000
   ```

---

## 🧪 Tests à Faire Maintenant

### Test 1: Voir le Bouton "Annuler" dans le Backoffice

1. Ouvre ton navigateur: `http://localhost:8000/backoffice/evenement`
2. Connecte-toi en tant qu'admin
3. Tu devrais voir un bouton **"❌ Annuler"** sur les événements planifiés ou en cours

**Résultat attendu**: Le bouton apparaît uniquement pour les événements non terminés/non annulés

---

### Test 2: Annuler un Événement Manuellement

1. Va sur `http://localhost:8000/backoffice/evenement`
2. Trouve un événement avec le statut "Planifié" ou "En cours"
3. Clique sur **"❌ Annuler"**
4. Confirme dans la popup

**Résultat attendu**:
- ✅ Message: "L'événement a été annulé avec succès"
- ✅ Le statut passe à "Annulé"
- ✅ Le bouton "Annuler" disparaît
- ✅ Si l'événement a des participations acceptées, des emails sont envoyés

---

### Test 3: Créer un Événement de Test pour Transition Automatique

#### Étape 1: Créer un événement qui devrait démarrer maintenant

1. Va sur `http://localhost:8000/backoffice/evenement/new`
2. Remplis le formulaire:
   - **Titre**: Test Auto Workflow
   - **Description**: Test de transition automatique
   - **Type**: Conférence
   - **Lieu**: Salle Test
   - **Date début**: **AUJOURD'HUI à 03:00** (dans le passé)
   - **Date fin**: **AUJOURD'HUI à 23:59** (dans le futur)
   - **Nb Max**: 5
3. Enregistre

**État initial**: L'événement sera créé avec le statut "Planifié"

#### Étape 2: Exécuter la commande de mise à jour

Ouvre un terminal et exécute:

```bash
php bin/console app:update-evenement-workflow
```

**Résultat attendu**:
```
✓ Événement "Test Auto Workflow" (ID: X) démarré

Résumé
------
Action                  Nombre
Événements démarrés     1
Événements terminés     0
Total des transitions   1

[OK] Mise à jour terminée avec succès!
```

#### Étape 3: Vérifier dans le backoffice

1. Rafraîchis la page `http://localhost:8000/backoffice/evenement`
2. L'événement "Test Auto Workflow" devrait maintenant avoir le statut **"En cours"**

---

### Test 4: Vérifier les Logs

Ouvre le fichier de logs:

```bash
type var\log\dev.log | findstr "Transition"
```

**Résultat attendu**: Tu devrais voir des entrées comme:

```
[2026-02-22 03:27:31] app.INFO: Transition d'événement {"evenement_id":5,"transition":"demarrer","from":["planifie"],"to":["en_cours"],"user":"SYSTEM"}
```

---

### Test 5: Tester l'Envoi d'Emails (Si tu as des participations)

#### Prérequis:
- Un événement avec au moins 1 équipe participante (participation acceptée)
- Les membres de l'équipe doivent avoir des emails valides

#### Test:
1. Annule l'événement via le bouton "❌ Annuler"
2. Vérifie les logs d'envoi d'emails:

```bash
type var\log\dev.log | findstr "Email envoyé"
```

**Résultat attendu**:
```
[2026-02-22 03:30:00] app.INFO: Email envoyé {"type":"cancelled","evenement_id":5,"student_email":"etudiant@example.com","team":"Équipe Alpha"}
```

---

## 🎯 Scénarios de Test Complets

### Scénario A: Cycle de Vie Complet d'un Événement

1. **Créer** un événement futur → Statut: "Planifié"
2. **Attendre** que la date de début arrive (ou modifier la date dans le passé)
3. **Exécuter** `php bin/console app:update-evenement-workflow` → Statut: "En cours"
4. **Attendre** que la date de fin passe (ou modifier la date dans le passé)
5. **Exécuter** `php bin/console app:update-evenement-workflow` → Statut: "Passé"

### Scénario B: Annulation Manuelle

1. **Créer** un événement futur → Statut: "Planifié"
2. **Ajouter** des participations et les accepter
3. **Cliquer** sur "❌ Annuler" → Statut: "Annulé"
4. **Vérifier** que les emails ont été envoyés aux participants

### Scénario C: Vérifier les Guards

1. **Créer** un événement futur (date début dans 2 jours)
2. **Exécuter** `php bin/console app:update-evenement-workflow`
3. **Résultat**: L'événement reste "Planifié" (guard bloque la transition car la date n'est pas arrivée)

---

## 📊 Checklist de Validation

### Interface Backoffice
- [ ] Le bouton "❌ Annuler" apparaît dans la liste des événements
- [ ] Le bouton "❌ Annuler l'événement" apparaît dans le formulaire d'édition
- [ ] Le bouton disparaît pour les événements annulés
- [ ] Le bouton disparaît pour les événements terminés
- [ ] La popup de confirmation s'affiche avant annulation
- [ ] Le message de succès s'affiche après annulation

### Transitions Automatiques
- [ ] La commande s'exécute sans erreur
- [ ] Les événements passent de "planifie" à "en_cours" automatiquement
- [ ] Les événements passent de "en_cours" à "termine" automatiquement
- [ ] Le résumé affiche le bon nombre de transitions

### Logs et Traçabilité
- [ ] Les transitions sont loggées dans var/log/dev.log
- [ ] Les logs contiennent: evenement_id, transition, from, to, user
- [ ] Les logs d'envoi d'emails sont présents (si applicable)

### Envoi d'Emails (Si applicable)
- [ ] Email envoyé quand événement annulé
- [ ] Email envoyé quand événement démarré (si participations)
- [ ] Tous les membres des équipes participantes reçoivent l'email
- [ ] Le template d'email est correct

---

## 🐛 Problèmes Possibles et Solutions

### Le bouton "Annuler" n'apparaît pas

**Cause**: La variable `can_annuler` n'est pas passée au template

**Solution**: Vérifie que le contrôleur passe bien cette variable (déjà fait normalement)

### Erreur "Call to a member function can() on null"

**Cause**: Le workflow n'est pas injecté dans le contrôleur

**Solution**: Vide le cache:
```bash
php bin/console cache:clear
```

### Les emails ne sont pas envoyés

**Cause**: Pas de participations acceptées ou problème SendGrid

**Solution**: 
1. Vérifie qu'il y a des participations avec statut "Accepté"
2. Vérifie la configuration SendGrid dans `.env.local`

### La commande ne trouve pas les événements

**Cause**: Tous les événements sont dans le futur ou déjà dans le bon état

**Solution**: C'est normal! Crée un événement avec une date de début dans le passé pour tester

---

## 🎓 Prochaines Étapes

Une fois les tests validés:

1. **Configurer un cron job** (optionnel):
   ```bash
   # Windows Task Scheduler
   # Exécuter toutes les 5 minutes:
   php C:\path\to\project\bin\console app:update-evenement-workflow
   ```

2. **Ajouter des fonctionnalités**:
   - Génération automatique de certificats à la fin
   - Envoi de rappels 3 jours avant
   - Archivage automatique des événements terminés

3. **Créer des tests unitaires** pour le workflow

---

## 📝 Résumé

✅ **Workflow configuré et fonctionnel**  
✅ **Boutons d'annulation ajoutés dans le backoffice**  
✅ **Commande de mise à jour automatique opérationnelle**  
✅ **Logs et audit trail activés**  
✅ **Envoi d'emails intégré**  

**Tu peux maintenant tester l'interface et les transitions!**

---

**Date**: 22 Février 2026  
**Statut**: ✅ Prêt pour les tests
