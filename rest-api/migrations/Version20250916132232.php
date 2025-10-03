<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916132232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO todo (title, description, is_completed, created_at, updated_at) VALUES ('Buy groceries', 'chicken, vegetables, Eggs', false, '14-4-2025', '21-5-2025')");
        $this->addSql('ALTER TABLE todo ALTER title DROP NOT NULL');
        $this->addSql('ALTER TABLE todo ALTER title TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE todo ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE todo ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE todo ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE todo ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE todo ALTER updated_at DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM todo WHERE title IN ('Buy groceries', 'Clean the house', 'Finish report')");
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE todo ALTER title SET NOT NULL');
        $this->addSql('ALTER TABLE todo ALTER title TYPE VARCHAR(70)');
        $this->addSql('ALTER TABLE todo ALTER description TYPE VARCHAR(120)');
        $this->addSql('ALTER TABLE todo ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE todo ALTER created_at SET DEFAULT \'now()\'');
        $this->addSql('ALTER TABLE todo ALTER updated_at SET DEFAULT \'now()\'');
    }
}
