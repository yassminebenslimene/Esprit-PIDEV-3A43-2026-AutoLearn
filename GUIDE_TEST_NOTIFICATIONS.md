# 🔔 GUIDE DE TEST - SYSTÈME DE NOTIFICATIONS INTERNES

**Date**: 22 février 2026  
**Module**: Gestion de Cours - Notifications

---

## 🎯 Objectif

Tester le système complet de notifications internes pour les étudiants, incluant:
- Affichage des notifications dans l'interface
- Badge de compteur en temps réel
- Marquage comme lu/non lu
- Suppression de notifications

---

## 📋 Prérequis

✅ Système de rappel d'inactivité fonctionnel  
✅ Notifications créées en base de données  
✅ Compte étudiant pour se connecter

---

## 🚀 ÉTAPE 1: Vérifier les Notifications en Base de Données

```bash
php bin/console doctrine:query:sql "SELECT id, user_id, type, title, is_read, created_at FROM notification ORDER BY created_at DESC LIMIT 10"
```

**Résultat attendu**: Liste des notifications existantes

Si aucune notification n'existe, créez-en avec:
```bash
php bin/console app:send-inactivity-reminders
```

---

## 🚀 ÉTAPE 2: Démarrer le Serveur

```bash
symfony server:start
```

Ou:
```bash
php -S localhost:8000 -t public
```

**URL**: http://localhost:8000

---

## 🚀 ÉTAPE 3: Se Connecter en tant qu'Étudiant

1. Aller sur: http://localhost:8000
2. Cliquer sur "Connexion"
3. Se connecter avec un compte étudiant qui a des notifications:
   - Email: `yasmine@gmail.com`
   - Mot de passe: (votre mot de passe)

---

## 🚀 ÉTAPE 4: Vérifier le Badge de Notification

Une fois connecté, dans la navbar en haut:

✅ **Vérifier**:
- Une icône de cloche (🔔) est visible
- Un badge rouge avec un chiffre apparaît si vous avez des notifications non lues
- Le badge affiche le nombre correct de notifications non lues

**Exemple**: Si vous avez 6 notifications non lues, le badge affiche "6"

---

## 🚀 ÉTAPE 5: Accéder à la Page des Notifications

**Méthode 1**: Cliquer sur l'icône de cloche dans la navbar

**Méthode 2**: Aller directement sur: http://localhost:8000/notifications

**Ce que vous devez voir**:
- ✅ Titre "📬 Mes Notifications"
- ✅ Badge avec le nombre de notifications non lues
- ✅ Bouton "✓ Tout marquer comme lu" (si notifications non lues)
- ✅ Liste de toutes vos notifications

---

## 🚀 ÉTAPE 6: Tester les Fonctionnalités

### 6.1 Notifications Non Lues

Les notifications non lues ont:
- ✅ Une bordure gauche violette
- ✅ Un fond légèrement coloré
- ✅ Un badge "Rappel" ou "Info"

### 6.2 Marquer une Notification comme Lue

1. Cliquer sur le bouton "✓ Marquer comme lu" sur une notification
2. **Résultat attendu**:
   - La notification change d'apparence (plus de bordure violette)
   - Le compteur de notifications non lues diminue
   - Le badge dans la navbar se met à jour

### 6.3 Marquer Toutes les Notifications comme Lues

1. Cliquer sur "✓ Tout marquer comme lu" en haut de la page
2. **Résultat attendu**:
   - Message de succès: "Toutes les notifications ont été marquées comme lues"
   - Toutes les notifications deviennent "lues"
   - Le badge dans la navbar disparaît
   - Le bouton "Tout marquer comme lu" disparaît

### 6.4 Supprimer une Notification

1. Cliquer sur "🗑️ Supprimer" sur une notification
2. Confirmer la suppression dans la popup
3. **Résultat attendu**:
   - Message de succès: "Notification supprimée"
   - La notification disparaît de la liste
   - Le compteur se met à jour

---

## 🚀 ÉTAPE 7: Tester le Badge en Temps Réel

Le badge se met à jour automatiquement toutes les 30 secondes.

**Test**:
1. Ouvrir la page d'accueil dans un onglet
2. Ouvrir la page des notifications dans un autre onglet
3. Marquer une notification comme lue dans le 2ème onglet
4. Attendre 30 secondes maximum
5. **Résultat attendu**: Le badge dans le 1er onglet se met à jour automatiquement

---

## 🚀 ÉTAPE 8: Tester avec Plusieurs Utilisateurs

### Test 1: Utilisateur avec Notifications
- Se connecter avec: `yasmine@gmail.com`
- Vérifier que les notifications s'affichent

### Test 2: Utilisateur sans Notifications
- Se connecter avec un autre compte étudiant
- **Résultat attendu**:
  - Pas de badge dans la navbar
  - Message "Aucune notification" sur la page

---

## 📊 Checklist de Test Complète

### Interface
- [ ] Badge de notification visible dans la navbar
- [ ] Badge affiche le bon nombre de notifications non lues
- [ ] Badge disparaît quand toutes les notifications sont lues
- [ ] Page des notifications accessible via l'icône cloche
- [ ] Design responsive et professionnel

### Fonctionnalités
- [ ] Liste des notifications s'affiche correctement
- [ ] Notifications non lues ont un style différent
- [ ] Bouton "Marquer comme lu" fonctionne
- [ ] Bouton "Tout marquer comme lu" fonctionne
- [ ] Bouton "Supprimer" fonctionne
- [ ] Messages de confirmation s'affichent
- [ ] Badge se met à jour après chaque action

### Temps Réel
- [ ] Badge se met à jour automatiquement (30 secondes)
- [ ] API `/notifications/api/unread-count` fonctionne
- [ ] API `/notifications/api/recent` fonctionne

### Sécurité
- [ ] Seul l'utilisateur connecté voit ses notifications
- [ ] Impossible d'accéder aux notifications d'un autre utilisateur
- [ ] Redirection vers login si non connecté

---

## 🎨 Aperçu Visuel

### Badge dans la Navbar
```
[Accueil] [Cours] [Événements] [🔔 (6)] [👤 Yasmin]
                                    ↑
                              Badge rouge
```

### Page des Notifications
```
📬 Mes Notifications                    [6 non lues]

[✓ Tout marquer comme lu]

┌─────────────────────────────────────────────────┐
│ ⏰ Rappel d'activité          [Rappel] 22/02/26 │
│ Bonjour yasmin, nous avons remarqué...          │
│ [✓ Marquer comme lu] [🗑️ Supprimer]            │
└─────────────────────────────────────────────────┘
```

---

## 🔧 Dépannage

### Problème: Badge ne s'affiche pas
**Solution**: 
```bash
# Vérifier que l'utilisateur a des notifications non lues
php bin/console doctrine:query:sql "SELECT * FROM notification WHERE user_id = 2 AND is_read = 0"

# Vider le cache
php bin/console cache:clear
```

### Problème: Erreur 404 sur /notifications
**Solution**:
```bash
# Vérifier les routes
php bin/console debug:router | Select-String "notification"

# Vider le cache
php bin/console cache:clear
```

### Problème: Badge ne se met pas à jour
**Solution**:
- Ouvrir la console du navigateur (F12)
- Vérifier les erreurs JavaScript
- Vérifier que l'API répond: http://localhost:8000/notifications/api/unread-count

---

## 📈 Résultats Attendus

### Scénario Complet Réussi

1. ✅ Badge affiche "6" notifications non lues
2. ✅ Page des notifications affiche 6 notifications
3. ✅ Marquer 1 notification comme lue → Badge affiche "5"
4. ✅ Tout marquer comme lu → Badge disparaît
5. ✅ Supprimer 1 notification → Liste mise à jour
6. ✅ Badge se met à jour automatiquement après 30 secondes

---

## 🎉 Validation Finale

Le système de notifications est **100% fonctionnel** si:

✅ Le badge s'affiche correctement  
✅ Les notifications sont visibles et lisibles  
✅ Toutes les actions fonctionnent (marquer lu, supprimer)  
✅ Le badge se met à jour en temps réel  
✅ L'interface est responsive et professionnelle  

---

## 📚 Fichiers Créés

- `src/Controller/NotificationController.php` - Contrôleur des notifications
- `templates/frontoffice/notifications/index.html.twig` - Page des notifications
- `templates/frontoffice/base.html.twig` - Badge dans la navbar (modifié)

---

## 🚀 Prochaines Étapes (Optionnelles)

1. **Dropdown de notifications**: Afficher les 5 dernières notifications dans un dropdown
2. **Notifications en temps réel**: Utiliser WebSocket ou Mercure pour notifications instantanées
3. **Types de notifications**: Ajouter d'autres types (nouveau cours, mise à jour, etc.)
4. **Préférences**: Permettre aux utilisateurs de gérer leurs préférences de notification

---

**Guide créé le 22 février 2026** ✅
