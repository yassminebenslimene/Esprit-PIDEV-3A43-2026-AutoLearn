-- Modifier la table challenge pour remplacer date_debut/date_fin par duree

-- Table principale: challenge
ALTER TABLE challenge ADD COLUMN duree INT NOT NULL DEFAULT 30;
ALTER TABLE challenge DROP COLUMN date_debut;
ALTER TABLE challenge DROP COLUMN date_fin;

-- Table d'audit: challenge_audit (utilisée par Sonata Entity Audit Bundle)
ALTER TABLE challenge_audit ADD COLUMN duree INT NOT NULL DEFAULT 30;
ALTER TABLE challenge_audit DROP COLUMN date_debut;
ALTER TABLE challenge_audit DROP COLUMN date_fin;
