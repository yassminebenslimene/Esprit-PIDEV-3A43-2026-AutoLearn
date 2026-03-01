<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour remplacer date_debut et date_fin par duree dans la table challenge
 */
final class Version20260301120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remplace les colonnes date_debut et date_fin par duree (en minutes) dans la table challenge';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne duree
        $this->addSql('ALTER TABLE challenge ADD duree INT NOT NULL DEFAULT 30');
        
        // Supprimer les colonnes date_debut et date_fin
        $this->addSql('ALTER TABLE challenge DROP COLUMN date_debut');
        $this->addSql('ALTER TABLE challenge DROP COLUMN date_fin');
    }

    public function down(Schema $schema): void
    {
        // Restaurer les colonnes date_debut et date_fin
        $this->addSql('ALTER TABLE challenge ADD date_debut DATETIME NOT NULL');
        $this->addSql('ALTER TABLE challenge ADD date_fin DATETIME NOT NULL');
        
        // Supprimer la colonne duree
        $this->addSql('ALTER TABLE challenge DROP COLUMN duree');
    }
}
