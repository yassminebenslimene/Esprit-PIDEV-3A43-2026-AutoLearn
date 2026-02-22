<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour tables cours/chapitre (déjà exécutée manuellement)
 * Ce fichier est vide car les tables ont été créées directement
 */
final class Version20260210233145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration vide - Tables cours/chapitre déjà créées';
    }

    public function up(Schema $schema): void
    {
        // Migration vide - tables déjà créées
    }

    public function down(Schema $schema): void
    {
        // Migration vide - ne rien faire
    }
}
