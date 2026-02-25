# ⚡ Test Rapide: Participation aux Événements en Cours

## 🎯 Test Complet (10 minutes)

### Préparation
1. Créer un événement de test:
   - Titre: "Test Participation En Cours"
   - Date début: Dans 2 minutes
   - Date fin: Dans 15 minutes
   - Places: 10 équipes

---

## Test 1: Avant le Démarrage (Événement Planifié)

### Étapes
1. Aller sur `/events`
2. Trouver l'événement "Test Participation En Cours"
3. Développer les détails

### Résultat Attendu
✅ Badge vert "PLANIFIÉ"  
✅ Bouton "🎯 Participate in This Event" visible  
✅ Pas de message d'avertissement

---

## Test 2: Pendant l'Événement (Événement en Cours)

### Étapes
1. Attendre 2 minutes (événement démarre)
2. Exécuter: `php bin/console app:update-event-status`
3. Rafraîchir la page `/events`
4. Développer les détails de l'événement

### Résultat Attendu
✅ Badge jaune "⏳ IN PROGRESS"  
✅ Message jaune: "⏰ Event in progress! You can still join now."  
✅ Bouton "🎯 Participate in This Event" TOUJOURS visible  

### Test de Participation
5. Cliquer sur "Participate in This Event"
6. Créer une nouvelle équipe
7. Soumettre la participation

### Résultat Attendu
✅ Accès autorisé à la page de participation  
✅ Formulaire accessible  
✅ Participation créée avec succès  
✅ Message: "Participation accepted!"  
✅ Email de confirmation envoyé

---

## Test 3: Après l'Événement (Événement Terminé)

### Étapes
1. Attendre 15 minutes (événement se termine)
2. Exécuter: `php bin/console app:update-event-status`
3. Rafraîchir la page `/events`
4. Développer les détails de l'événement

### Résultat Attendu
✅ Badge gris "🏁 COMPLETED"  
❌ Bouton "Participate" invisible  
✅ Message: "Event Completed - Registrations are now closed"

### Test de Blocage
5. Essayer d'accéder directement à l'URL:
   `/events/[ID]/participate`

### Résultat Attendu
❌ Redirection vers `/events`  
✅ Message d'erreur: "This event has ended. Registrations are now closed."

---

## 📊 Tableau de Validation

| Test | Statut | Bouton Visible | Message | Participation |
|------|--------|----------------|---------|---------------|
| Planifié | ✅ | ✅ OUI | Aucun | ✅ Autorisée |
| En Cours | ✅ | ✅ OUI | "Event in progress!" | ✅ Autorisée |
| Terminé | ✅ | ❌ NON | "Event Completed" | ❌ Bloquée |

---

## 🔍 Vérifications Supplémentaires

### Console Navigateur (F12)
```
Aucune erreur JavaScript
```

### Logs Symfony
```bash
tail -f var/log/dev.log
# Aucune erreur PHP
```

### Base de Données
```sql
SELECT id, titre, workflow_status, status 
FROM evenement 
WHERE titre = 'Test Participation En Cours';

-- Vérifier que workflow_status change:
-- 'planifie' → 'en_cours' → 'termine'
```

---

## ✅ Checklist Finale

- [ ] Événement planifié: Bouton visible
- [ ] Événement en cours: Bouton visible + message jaune
- [ ] Événement en cours: Participation fonctionne
- [ ] Événement terminé: Bouton invisible
- [ ] Événement terminé: Accès bloqué
- [ ] Emails envoyés correctement
- [ ] Aucune erreur dans les logs

---

**Si tous les tests passent, la fonctionnalité est opérationnelle! ✅**
