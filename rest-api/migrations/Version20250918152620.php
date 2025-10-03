<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250918152620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "INSERT INTO todo (title, description, is_completed, created_at, updated_at) VALUES ('Clean the house', 'living room, kitchen, bathroom', false, '2025-04-15', '2025-05-21')"
        );
        $this->addSql(
            "INSERT INTO todo (title, description, is_completed, created_at, updated_at) VALUES ('Finish report', 'annual financial report', true, '2025-04-16', '2025-05-22')"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM todo');
    }
}
