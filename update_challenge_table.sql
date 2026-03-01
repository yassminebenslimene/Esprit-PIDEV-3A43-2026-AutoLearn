-- Modifier la table challenge pour remplacer date_debut/date_fin par duree

-- Ajouter la colonne duree (en minutes)
ALTER TABLE challenge ADD COLUMN duree INT NOT NULL DEFAULT 30;

-- Supprimer les colonnes date_debut et date_fin
ALTER TABLE challenge DROP COLUMN date_debut;
ALTER TABLE challenge DROP COLUMN date_fin;
