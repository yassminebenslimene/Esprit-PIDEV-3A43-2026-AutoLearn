# 🧪 Guide de Test des Améliorations

## Tests à Effectuer

### ✅ Test 1: Bouton "Participate" pour Événements Passés

**Objectif:** Vérifier que le bouton n'apparaît pas pour les événements passés

**Étapes:**
1. Aller sur la page frontoffice des événements: `/events`
2. Identifier un événement avec statut "Passé" (badge rouge "🏁 COMPLETED")
3. Cliquer pour développer les détails de l'événement
4. Vérifier qu'il y a un message "Event Completed - Registrations are now closed"
5. Vérifier qu'il n'y a PAS de bouton "🎯 Participate in This Event"

**Résultat attendu:**
- ✅ Message "Event Completed" affiché
- ✅ Aucun bouton "Participate"
- ✅ Fond gris avec icône 🏁

---

### ✅ Test 2: Envoi Automatique d'Emails au Démarrage

**Objectif:** Vérifier que les emails sont envoyés quand un événement démarre

**Préparation:**
1. Créer un événement de test avec:
   - Date de début: Dans 2 minutes
   - Date de fin: Dans 1 heure
   - Au moins 1 équipe inscrite avec 1 étudiant

**Étapes:**
1. Exécuter la commande de mise à jour:
   ```bash
   php bin/console app:update-event-status
   ```

2. Vérifier les logs:
   ```bash
   tail -f var/log/dev.log | grep "Événement démarré"
   ```

3. Vérifier l'email reçu par l'étudiant:
   - Sujet: "🚀 Event Started - [Nom de l'événement]"
   - Contenu: Détails de l'événement, alerte "Don't Miss It!"

**Résultat attendu:**
- ✅ Commande affiche "✓ Événement [nom] démarré"
- ✅ Log contient "🚀 Événement démarré"
- ✅ Email reçu avec template professionnel
- ✅ Statut de l'événement passe à "En cours"

---

### ✅ Test 3: Rapports AI - Affichage Correct

**Objectif:** Vérifier que les rapports AI s'affichent correctement

**Étapes:**
1. Aller sur le backoffice: `/backoffice/evenement`
2. Scroller jusqu'à la section "🤖 Statistiques & Rapports AI"
3. Cliquer sur "📊 Générer Rapport d'Analyse"
4. Attendre 30-60 secondes (spinner visible)
5. Vérifier que le rapport s'affiche dans un conteneur blanc

**Résultat attendu:**
- ✅ Spinner animé pendant la génération
- ✅ Rapport affiché dans un conteneur blanc avec bordure bleue
- ✅ Texte lisible et bien formaté
- ✅ Bouton "×" pour fermer le rapport
- ✅ Scroll automatique vers le rapport

**En cas d'erreur:**
- Vérifier que `HUGGINGFACE_API_KEY` est configuré dans `.env.local`
- Vérifier que le token a la permission "Make calls to Inference Providers"
- Vérifier les logs: `var/log/dev.log`

---

### ✅ Test 4: Filtre par Type d'Événement

**Objectif:** Vérifier que le filtre fonctionne correctement

**Étapes:**

#### A. Test du Filtre Visuel
1. Aller sur `/backoffice/evenement`
2. Observer les cartes de statistiques (Conference, Hackathon, Workshop)
3. Sélectionner "Conference" dans le filtre
4. Vérifier que seule la carte "Conference" est visible
5. Sélectionner "Hackathon"
6. Vérifier que seule la carte "Hackathon" est visible
7. Sélectionner "Tous les types d'événements"
8. Vérifier que toutes les cartes sont visibles

**Résultat attendu:**
- ✅ Les cartes se masquent/affichent dynamiquement
- ✅ Pas de rechargement de page
- ✅ Transition fluide

#### B. Test du Filtre AI
1. Sélectionner "Conference" dans le filtre
2. Cliquer sur "📊 Générer Rapport d'Analyse"
3. Attendre la génération
4. Vérifier qu'un badge "🎯 Filtre actif: Conference" est affiché
5. Lire le rapport et vérifier qu'il parle uniquement des conférences

**Résultat attendu:**
- ✅ Badge "Filtre actif" visible
- ✅ Rapport mentionne uniquement les conférences
- ✅ Pas de mention des hackathons ou workshops

#### C. Test Comparatif
1. Générer un rapport d'analyse SANS filtre
2. Noter les statistiques globales
3. Sélectionner "Conference"
4. Générer un nouveau rapport d'analyse
5. Comparer les deux rapports

**Résultat attendu:**
- ✅ Rapport sans filtre: Analyse tous les types
- ✅ Rapport avec filtre: Analyse uniquement les conférences
- ✅ Les statistiques sont différentes
- ✅ Les recommandations sont spécifiques au type

---

### ✅ Test 5: Commande de Mise à Jour Automatique

**Objectif:** Vérifier que la commande fonctionne correctement

**Étapes:**
1. Créer 3 événements de test:
   - Événement A: Date de début passée, date de fin future (doit démarrer)
   - Événement B: Date de début et fin passées (doit terminer)
   - Événement C: Date de début future (ne change pas)

2. Exécuter la commande:
   ```bash
   php bin/console app:update-event-status
   ```

3. Observer la sortie de la commande

**Résultat attendu:**
```
Mise à jour automatique des statuts d'événements
================================================

Traitement de 3 événement(s)...

 ✓ Événement "Événement A" démarré
 ✓ Événement "Événement B" terminé

Résumé
======

 --------------- ------- 
  Statistique     Valeur 
 --------------- ------- 
  Événements      3      
  traités                
  Événements      2      
  mis à jour             
  Événements      1      
  démarrés               
  Événements      1      
  terminés               
 --------------- ------- 

 [OK] Terminé! 2 événement(s) mis à jour avec succès.
```

---

## 🔍 Vérifications Supplémentaires

### Vérifier les Logs
```bash
# Logs généraux
tail -f var/log/dev.log

# Filtrer les logs d'événements
tail -f var/log/dev.log | grep "Événement"

# Filtrer les logs d'emails
tail -f var/log/dev.log | grep "Email"
```

### Vérifier la Base de Données
```sql
-- Vérifier les statuts des événements
SELECT id, titre, workflow_status, status, is_canceled 
FROM evenement 
ORDER BY date_debut DESC;

-- Vérifier les participations
SELECT e.titre, eq.nom, p.statut 
FROM participation p
JOIN evenement e ON p.evenement_id = e.id
JOIN equipe eq ON p.equipe_id = eq.id
WHERE p.statut = 'Accepté';
```

### Vérifier les Emails Envoyés
1. Consulter les logs SendGrid (si configuré)
2. Vérifier la boîte mail des étudiants de test
3. Vérifier que les templates sont corrects

---

## 🐛 Résolution de Problèmes

### Problème: Emails non envoyés
**Solutions:**
1. Vérifier `MAILER_DSN` dans `.env.local`
2. Vérifier que SendGrid est configuré
3. Vérifier les logs: `var/log/dev.log`
4. Tester manuellement: `php bin/console app:send-event-reminders`

### Problème: Rapports AI vides
**Solutions:**
1. Vérifier `HUGGINGFACE_API_KEY` dans `.env.local`
2. Vérifier que le token est valide sur https://huggingface.co/settings/tokens
3. Vérifier que le token a la permission "Make calls to Inference Providers"
4. Vérifier les logs: `var/log/dev.log`
5. Tester avec un autre modèle: `HUGGINGFACE_MODEL=mistralai/Mistral-7B-Instruct-v0.2`

### Problème: Filtre ne fonctionne pas
**Solutions:**
1. Vider le cache: `php bin/console cache:clear`
2. Vérifier la console JavaScript (F12) pour les erreurs
3. Vérifier que les cartes ont l'attribut `data-type`
4. Recharger la page (Ctrl+F5)

### Problème: Commande ne met pas à jour
**Solutions:**
1. Vérifier les dates des événements dans la base de données
2. Vérifier que les événements ne sont pas annulés
3. Vérifier les logs pour les erreurs
4. Exécuter avec verbosité: `php bin/console app:update-event-status -vvv`

---

## ✅ Checklist Finale

Avant de considérer les tests terminés, vérifier:

- [ ] Bouton "Participate" masqué pour événements passés
- [ ] Bouton "Participate" masqué pour événements en cours
- [ ] Bouton "Participate" masqué pour événements annulés
- [ ] Bouton "Participate" visible uniquement pour événements planifiés avec places disponibles
- [ ] Emails envoyés automatiquement au démarrage d'événement
- [ ] Template email "Event Started" correct et professionnel
- [ ] Rapports AI s'affichent correctement (pas de page blanche)
- [ ] Filtre par type d'événement fonctionne visuellement
- [ ] Filtre par type d'événement affecte les rapports AI
- [ ] Badge "Filtre actif" s'affiche correctement
- [ ] Commande `app:update-event-status` fonctionne
- [ ] Logs détaillés et clairs
- [ ] Aucune erreur dans `var/log/dev.log`

---

## 📊 Rapport de Test

Après avoir effectué tous les tests, remplir ce rapport:

```
Date: _______________
Testeur: _______________

Test 1 - Bouton Participate: ☐ OK ☐ KO
Test 2 - Emails automatiques: ☐ OK ☐ KO
Test 3 - Rapports AI: ☐ OK ☐ KO
Test 4 - Filtre type événement: ☐ OK ☐ KO
Test 5 - Commande mise à jour: ☐ OK ☐ KO

Problèmes rencontrés:
_________________________________
_________________________________
_________________________________

Commentaires:
_________________________________
_________________________________
_________________________________
```

---

**Bonne chance pour les tests! 🚀**
