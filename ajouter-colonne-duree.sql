-- Script SQL pour ajouter la colonne 'duree' manquante
-- Exécuter ce script si doctrine:schema:update ne fonctionne pas

-- Ajouter la colonne duree dans la table cours
ALTER TABLE cours ADD COLUMN duree INT NOT NULL DEFAULT 0;

-- Ajouter la colonne duree dans la table challenge (si elle n'existe pas)
ALTER TABLE challenge ADD COLUMN duree INT NOT NULL DEFAULT 0;

-- Vérifier les colonnes
SHOW COLUMNS FROM cours LIKE 'duree';
SHOW COLUMNS FROM challenge LIKE 'duree';
