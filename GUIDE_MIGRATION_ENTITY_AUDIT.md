# Guide de Migration - Bundle EntityAudit

## Pour les membres de l'équipe qui pull depuis Amira

### Contexte
Une nouvelle migration a été ajoutée pour créer les tables nécessaires au bundle EntityAudit (utilisé par ilef pour l'audit des utilisateurs).

### Tables créées
- `revisions` : Stocke l'historique des révisions
- `user_audit` : Stocke les modifications des utilisateurs avec la colonne `rev`

### Instructions après pull

**1. Puller les dernières modifications:**
```bash
git pull origin main
```

**2. Exécuter les migrations:**
```bash
php bin/console doctrine:migrations:migrate
```

**3. Vider le cache:**
```bash
php bin/console cache:clear
```

### Sécurité de la migration

✅ **Utilise `IF NOT EXISTS`** : Si les tables existent déjà, elles ne seront pas recréées
✅ **Pas de perte de données** : Aucune donnée existante ne sera supprimée
✅ **Compatible avec toutes les bases** : Fonctionne que vous ayez déjà les tables ou non
✅ **Rollback possible** : En cas de problème, vous pouvez revenir en arrière

### En cas de problème

Si vous rencontrez l'erreur `Column 'rev' not found`:

```bash
# Réexécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Vider le cache
php bin/console cache:clear
```

### Vérification

Pour vérifier que tout fonctionne:

```bash
# Vérifier que les tables existent
php bin/console doctrine:query:sql "SHOW TABLES LIKE '%audit%'"
php bin/console doctrine:query:sql "SHOW TABLES LIKE 'revisions'"
```

Vous devriez voir:
- `user_audit`
- `revisions`

### Questions?

Si vous avez des problèmes, contactez Amira ou vérifiez que:
1. Vous avez bien pull les dernières modifications
2. Vous avez exécuté `doctrine:migrations:migrate`
3. Vous avez vidé le cache avec `cache:clear`

---

**Date de création:** 25/02/2026
**Auteur:** Amira
**Migration concernée:** `Version20260225164615`
