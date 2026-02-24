# 🔔 COMMANDES POUR TESTER LE BADGE

## 1. Vérifier les notifications en BDD
```bash
php bin/console doctrine:query:sql "SELECT id, user_id, title, is_read FROM notification WHERE user_id = 2"
```

## 2. Vider le cache
```bash
php bin/console cache:clear
```

## 3. Tester l'API (après connexion)
Ouvrir dans le navigateur:
```
http://localhost:8000/notifications/api/unread-count
```

Résultat attendu: `{"count":2}`

## 4. Voir la page de test du badge
```
http://localhost:8000/test-notification-badge.html
```

## 5. Voir la page des notifications
```
http://localhost:8000/notifications
```

---

## ✅ Le badge s'affiche SI:
- Vous êtes connecté ✅
- Vous avez des notifications non lues ✅  
- Le JavaScript s'exécute sans erreur ✅
- L'API répond correctement ✅

**Vous avez 2 notifications non lues, donc le badge devrait afficher "2"**
