<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208143302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Cette migration est ignorée car admin et etudiant
        // n'existent plus en tables séparées (Single Table Inheritance)
    }
    public function down(Schema $schema): void
    {
    // Nothing to revert
    }
}
