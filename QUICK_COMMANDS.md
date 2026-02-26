# ⚡ Commandes Rapides

## 🚀 Push web → Pull ilef → Fix Migrations

### Étape 1: Autoriser les secrets sur GitHub
Ouvrez ces liens et cliquez "Allow secret":
- https://github.com/yassminebenslimene/autolearn_3A43_Brain-up/security/secret-scanning/unblock-secret/39uWwWijMdciWnFDm1OLYloC1oW
- https://github.com/yassminebenslimene/autolearn_3A43_Brain-up/security/secret-scanning/unblock-secret/39uWwXUERTGhGtXaRFQWkrgvaVT

### Étape 2: Exécuter ces commandes

```bash
# Push web
git push origin web

# Si ça ne marche pas:
git push origin web --force

# Passer à ilef
git checkout ilef

# Pull web
git pull origin web

# Fixer migrations
php bin/console doctrine:migrations:version --add --all --no-interaction

# Vider cache
php bin/console cache:clear

# Push ilef
git push origin ilef
```

### Étape 3: Tester

```bash
# Vérifier routes
php bin/console debug:router | findstr suspend

# Démarrer serveur
symfony server:start
```

---

## 🎯 C'est tout!

Le système de suspension est maintenant sur les deux branches.
