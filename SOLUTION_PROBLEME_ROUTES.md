# 🔧 Solution au problème "No route found"

## 🐛 Problème identifié

L'erreur `No route found for "GET http://localhost:8000/backoffice/evenement/"` apparaît parce que:

1. ✅ Les routes existent bien dans l'application
2. ✅ Les contrôleurs sont correctement configurés
3. ❌ Le serveur web n'est pas démarré correctement sur le port 8000

## ✅ Solution

### Option 1: Utiliser le serveur Symfony (Recommandé)

Le serveur Symfony écoute actuellement sur le port **8001** au lieu de **8000**.

**Utilisez cette URL:**
```
http://localhost:8001/backoffice/evenement/
http://localhost:8001/backoffice/equipe/
http://localhost:8001/backoffice/participation/
```

**OU redémarrez le serveur sur le port 8000:**

```bash
# Arrêter tous les serveurs
symfony server:stop

# Vérifier qu'aucun processus n'utilise le port 8000
netstat -ano | findstr :8000

# Si un processus utilise le port, l'arrêter (remplacer PID par le numéro du processus)
Stop-Process -Id PID -Force

# Démarrer le serveur sur le port 8000
symfony server:start --port=8000 -d
```

### Option 2: Utiliser le serveur PHP intégré

```bash
# Démarrer le serveur PHP sur le port 8000
php -S localhost:8000 -t public
```

**Note:** Cette commande bloque le terminal. Ouvrez un nouveau terminal pour continuer à travailler.

### Option 3: Utiliser XAMPP/WAMP

Si vous utilisez XAMPP ou WAMP:

1. Placez le projet dans le dossier `htdocs` ou `www`
2. Configurez un VirtualHost
3. Accédez via `http://localhost/votre-projet/public/backoffice/evenement/`

## 🎯 URLs correctes

Une fois le serveur démarré correctement, utilisez ces URLs:

### Événements
- Liste: `http://localhost:8000/backoffice/evenement/` (ou port 8001)
- Créer: `http://localhost:8000/backoffice/evenement/new`
- Modifier: `http://localhost:8000/backoffice/evenement/{id}/edit`
- Voir: `http://localhost:8000/backoffice/evenement/{id}`

### Équipes
- Liste: `http://localhost:8000/backoffice/equipe/`
- Créer: `http://localhost:8000/backoffice/equipe/new`
- Modifier: `http://localhost:8000/backoffice/equipe/{id}/edit`
- Voir: `http://localhost:8000/backoffice/equipe/{id}`

### Participations
- Liste: `http://localhost:8000/backoffice/participation/`
- Créer: `http://localhost:8000/backoffice/participation/new`
- Modifier: `http://localhost:8000/backoffice/participation/{id}/edit`
- Voir: `http://localhost:8000/backoffice/participation/{id}`

## 🔍 Vérification

Pour vérifier que les routes existent:

```bash
# Lister toutes les routes
php bin/console debug:router

# Filtrer les routes événement
php bin/console debug:router | findstr evenement

# Tester une route spécifique
php bin/console router:match /backoffice/evenement/
```

## ✅ Résultat attendu

Après avoir démarré le serveur correctement, vous devriez voir:

1. **Page liste des événements** avec:
   - Titre "Gestion des Événements"
   - Bouton "➕ Ajouter un événement"
   - Tableau vide (si aucun événement) ou liste des événements

2. **Page création d'événement** avec:
   - Formulaire avec tous les champs
   - Boutons "Créer" et "Annuler"

## 🐛 Si le problème persiste

1. **Vider le cache:**
   ```bash
   php bin/console cache:clear
   ```

2. **Vérifier les logs:**
   ```bash
   # Logs Symfony
   tail -f var/log/dev.log
   
   # Logs du serveur
   symfony server:log
   ```

3. **Vérifier la configuration PHP:**
   ```bash
   php -v
   php -m | findstr pdo
   ```

4. **Redémarrer complètement:**
   ```bash
   symfony server:stop
   taskkill /F /IM php.exe
   symfony server:start -d
   ```

## 📝 Note importante

Les routes sont **correctement configurées** dans votre application. Le problème vient uniquement du serveur web qui n'est pas démarré sur le bon port ou qui a un cache obsolète.

**Tout le code est fonctionnel!** ✅
