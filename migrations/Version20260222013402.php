<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222013402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne workflow_status pour le Workflow Component';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne workflow_status seulement si elle n'existe pas
        $this->addSql('ALTER TABLE evenement ADD COLUMN IF NOT EXISTS workflow_status VARCHAR(50) NOT NULL DEFAULT \'planifie\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP workflow_status');
    }
}
