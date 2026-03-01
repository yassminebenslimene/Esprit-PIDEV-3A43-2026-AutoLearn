# Correction de la table challenge_audit

## Problème

Erreur lors de la création d'un challenge:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'duree' in 'field list'
```

L'erreur se produisait dans `challenge_audit` (table d'audit de Sonata Entity Audit Bundle).

## Cause

Lorsque nous avons modifié la table `challenge` pour remplacer `date_debut` et `date_fin` par `duree`, nous avons oublié de mettre à jour la table d'audit correspondante `challenge_audit`.

Le bundle Sonata Entity Audit crée automatiquement des tables `*_audit` pour tracer l'historique des modifications. Ces tables doivent avoir la même structure que les tables principales.

## Solution appliquée

Mise à jour de la table `challenge_audit` avec les mêmes modifications:

```sql
-- Ajouter la colonne duree
ALTER TABLE challenge_audit ADD COLUMN duree INT NOT NULL DEFAULT 30;

-- Supprimer les anciennes colonnes
ALTER TABLE challenge_audit DROP COLUMN date_debut;
ALTER TABLE challenge_audit DROP COLUMN date_fin;
```

## Commandes exécutées

```bash
php bin/console dbal:run-sql "ALTER TABLE challenge_audit ADD COLUMN duree INT NOT NULL DEFAULT 30"
php bin/console dbal:run-sql "ALTER TABLE challenge_audit DROP COLUMN date_debut"
php bin/console dbal:run-sql "ALTER TABLE challenge_audit DROP COLUMN date_fin"
```

## Fichiers mis à jour

- `update_challenge_table.sql` - Inclut maintenant les modifications pour challenge_audit

## Résultat

✅ La table `challenge_audit` a été mise à jour
✅ Les challenges peuvent maintenant être créés sans erreur
✅ L'audit fonctionne correctement avec la nouvelle structure

## Note importante

Chaque fois qu'une entité auditée est modifiée, il faut penser à mettre à jour:
1. La table principale (ex: `challenge`)
2. La table d'audit correspondante (ex: `challenge_audit`)

Les tables d'audit sont créées automatiquement par le bundle Sonata Entity Audit et suivent le pattern: `{nom_table}_audit`

---

**Date:** 1er mars 2026
