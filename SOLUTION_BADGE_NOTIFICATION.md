# 🔔 SOLUTION - Afficher le Badge de Notification

**Date**: 22 février 2026  
**Problème**: Le badge de notification ne s'affiche pas dans la navbar

---

## ✅ SOLUTION RAPIDE

Le badge est déjà dans le code, mais il est **caché par défaut**. Il s'affiche automatiquement quand:
1. Vous êtes connecté en tant qu'étudiant
2. Vous avez des notifications non lues
3. Le JavaScript charge le compteur via l'API

---

## 🎯 ÉTAPES POUR VOIR LE BADGE

### ÉTAPE 1: Vérifier que vous avez des notifications non lues

```bash
php bin/console doctrine:query:sql "SELECT user_id, COUNT(*) as non_lues FROM notification WHERE is_read = 0 GROUP BY user_id"
```

**Résultat actuel**:
- User 2: 2 notifications non lues ✅
- User 4: 2 notifications non lues ✅
- User 5: 2 notifications non lues ✅
- User 6: 2 notifications non lues ✅
- User 7: 2 notifications non lues ✅
- User 8: 2 notifications non lues ✅

### ÉTAPE 2: Vider le cache Symfony

```bash
php bin/console cache:clear
```

### ÉTAPE 3: Démarrer le serveur

```bash
symfony server:start
```

Ou:
```bash
php -S localhost:8000 -t public
```

### ÉTAPE 4: Se connecter

1. Aller sur: http://localhost:8000
2. Cliquer sur "Connexion"
3. Se connecter avec: **yasmine@gmail.com** (user_id = 2)

### ÉTAPE 5: Vérifier le badge

Une fois connecté, regardez dans la navbar en haut:
- Vous devriez voir: `[Accueil] [Cours] [Événements] [🔔] [Yasmin]`
- Le badge rouge avec "2" devrait apparaître sur l'icône cloche

---

## 🔍 SI LE BADGE NE S'AFFICHE PAS

### Diagnostic 1: Ouvrir la Console du Navigateur

1. Appuyer sur **F12** pour ouvrir les outils de développement
2. Aller dans l'onglet **Console**
3. Chercher des erreurs JavaScript (texte rouge)

### Diagnostic 2: Tester l'API manuellement

Dans la console du navigateur, tapez:

```javascript
fetch('/notifications/api/unread-count')
    .then(response => response.json())
    .then(data => console.log('Notifications:', data))
    .catch(error => console.error('Erreur:', error));
```

**Résultat attendu**: `Notifications: {count: 2}`

### Diagnostic 3: Vérifier que le JavaScript s'exécute

Dans la console du navigateur, tapez:

```javascript
document.getElementById('notification-badge')
```

**Résultat attendu**: Un élément HTML (pas `null`)

### Diagnostic 4: Forcer l'affichage du badge

Dans la console du navigateur, tapez:

```javascript
var badge = document.getElementById('notification-badge');
badge.textContent = '2';
badge.style.display = 'flex';
```

Le badge devrait apparaître immédiatement!

---

## 🛠️ SOLUTION ALTERNATIVE: Badge Toujours Visible (pour test)

Si vous voulez voir le badge immédiatement sans attendre le JavaScript, modifiez le template:

**Fichier**: `templates/frontoffice/base.html.twig`

**Cherchez**:
```twig
<span id="notification-badge" class="notification-badge" style="display: none;"></span>
```

**Remplacez par**:
```twig
<span id="notification-badge" class="notification-badge" style="display: flex;">2</span>
```

Puis videz le cache:
```bash
php bin/console cache:clear
```

Le badge sera maintenant **toujours visible** avec le chiffre "2".

---

## 📸 À QUOI RESSEMBLE LE BADGE

```
┌─────────────────────────────────────────────────┐
│  AUTOLEARN                                      │
│                                                 │
│  [Accueil] [Cours] [Événements] [🔔②] [Yasmin] │
│                                      ↑          │
│                                Badge rouge      │
└─────────────────────────────────────────────────┘
```

Le badge est:
- ✅ Petit cercle rouge
- ✅ Avec le chiffre "2" en blanc
- ✅ Positionné en haut à droite de l'icône cloche
- ✅ Avec une animation pulse (pulsation)

---

## 🎨 TEST VISUEL

Pour voir à quoi ressemble le badge, ouvrez:

```
http://localhost:8000/test-notification-badge.html
```

Cette page de test montre:
- ✅ Le badge en action
- ✅ Différents nombres de notifications
- ✅ Le code HTML et CSS
- ✅ Des boutons pour tester

---

## 🔧 VÉRIFICATION COMPLÈTE

### 1. Vérifier que le code est bien dans base.html.twig

```bash
# Chercher le badge dans le fichier
Select-String -Path "templates/frontoffice/base.html.twig" -Pattern "notification-badge"
```

**Résultat attendu**: Plusieurs lignes trouvées

### 2. Vérifier que le contrôleur existe

```bash
php bin/console debug:router | Select-String "notification"
```

**Résultat attendu**: 6 routes trouvées

### 3. Vérifier que l'API fonctionne

Après vous être connecté, allez sur:
```
http://localhost:8000/notifications/api/unread-count
```

**Résultat attendu**: `{"count":2}`

---

## ✅ CHECKLIST DE VÉRIFICATION

- [ ] Cache Symfony vidé (`php bin/console cache:clear`)
- [ ] Serveur démarré (`symfony server:start`)
- [ ] Connecté en tant qu'étudiant (yasmine@gmail.com)
- [ ] Console navigateur ouverte (F12)
- [ ] Aucune erreur JavaScript dans la console
- [ ] API répond correctement (`/notifications/api/unread-count`)
- [ ] Badge visible dans la navbar

---

## 🎯 SI TOUT EST OK MAIS LE BADGE NE S'AFFICHE TOUJOURS PAS

### Solution Temporaire: Affichage Forcé

Ajoutez ce code à la fin de `templates/frontoffice/base.html.twig`, juste avant `</body>`:

```twig
{% if app.user %}
<script>
    // Forcer l'affichage du badge au chargement
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var badge = document.getElementById('notification-badge');
            if (badge) {
                fetch('{{ path('app_notifications_unread_count') }}')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Notifications chargées:', data);
                        if (data.count > 0) {
                            badge.textContent = data.count > 9 ? '9+' : data.count;
                            badge.style.display = 'flex';
                            console.log('Badge affiché avec:', data.count);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur chargement notifications:', error);
                    });
            } else {
                console.error('Badge non trouvé dans le DOM');
            }
        }, 500); // Attendre 500ms après le chargement
    });
</script>
{% endif %}
```

---

## 📞 BESOIN D'AIDE?

Si le badge ne s'affiche toujours pas:

1. **Envoyez-moi une capture d'écran** de:
   - La navbar (en haut de la page)
   - La console du navigateur (F12)

2. **Envoyez-moi le résultat** de ces commandes:
   ```bash
   php bin/console doctrine:query:sql "SELECT * FROM notification WHERE user_id = 2 AND is_read = 0"
   ```

3. **Testez la page de démo**:
   ```
   http://localhost:8000/test-notification-badge.html
   ```

---

## 🎉 RÉSULTAT FINAL ATTENDU

Quand tout fonctionne:

1. ✅ Badge rouge visible dans la navbar
2. ✅ Chiffre "2" affiché en blanc
3. ✅ Animation pulse (pulsation)
4. ✅ Clic sur le badge → Page des notifications
5. ✅ Badge se met à jour automatiquement

---

**Guide créé le 22 février 2026** ✅
