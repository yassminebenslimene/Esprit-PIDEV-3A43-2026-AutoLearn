# ❌ Problème: Access Denied pour la génération IA

## Diagnostic

L'erreur "Erreur de connexion" que vous voyez est en réalité une erreur **"Access Denied" (403)**.

### Cause

L'utilisateur connecté `amirabahri@gmail.com` a le rôle **ETUDIANT** mais la route `/backoffice/ai-generator/generate-chapter` nécessite le rôle **ROLE_ADMIN**.

### Logs

```
[2026-03-02T21:56:21.296338+01:00] security.DEBUG: Access denied, the user is neither anonymous, nor remember-me.
[2026-03-02T21:56:21.303204+01:00] request.ERROR: Uncaught PHP Exception AccessDeniedHttpException: "Access Denied."
```

## Solutions

### Solution 1: Se connecter avec un compte ADMIN (Recommandé)

1. Déconnectez-vous du backoffice
2. Connectez-vous avec un compte qui a le rôle ADMIN
3. Essayez à nouveau de générer un chapitre

### Solution 2: Changer le rôle de l'utilisateur actuel

Exécutez cette commande SQL pour donner le rôle ADMIN à l'utilisateur:

```sql
UPDATE user SET role = 'ADMIN' WHERE email = 'amirabahri@gmail.com';
```

Ou via la console Symfony:

```bash
php bin/console doctrine:query:sql "UPDATE user SET role = 'ADMIN' WHERE email = 'amirabahri@gmail.com'"
```

Ensuite:
1. Déconnectez-vous
2. Reconnectez-vous
3. Essayez à nouveau

### Solution 3: Créer un nouveau compte ADMIN

Si vous n'avez pas de compte ADMIN, créez-en un:

```bash
php bin/console doctrine:query:sql "INSERT INTO user (nom, prenom, email, password, role, createdAt, discr) VALUES ('Admin', 'System', 'admin@autolearn.com', '\$2y\$13\$hashedpassword', 'ADMIN', NOW(), 'admin')"
```

Note: Vous devrez hasher le mot de passe correctement.

## Vérification

Pour vérifier le rôle d'un utilisateur:

```bash
php bin/console doctrine:query:sql "SELECT userId, email, role FROM user WHERE email = 'votre@email.com'"
```

## Pourquoi cette restriction?

La génération de chapitres avec l'IA est une fonctionnalité administrative qui:
- Modifie la base de données
- Utilise des ressources API (coût)
- Crée du contenu de cours

C'est pourquoi elle est réservée aux administrateurs.

## Test après correction

Une fois connecté avec un compte ADMIN:

1. Allez dans **Backoffice** → **Cours** → **Chapitres**
2. Cliquez sur **"🤖 Générer un Chapitre avec l'IA"**
3. Entrez un titre de chapitre
4. Cliquez sur **"Générer"**
5. Le chapitre devrait se créer sans erreur

Vous pouvez aussi tester avec: `http://127.0.0.1:8000/test-ia-generation.html`
