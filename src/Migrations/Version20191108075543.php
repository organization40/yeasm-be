<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191108075543 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8d67d2c520');
        $this->addSql('DROP INDEX idx_5a8a6c8d67d2c520');
        $this->addSql('ALTER TABLE post RENAME COLUMN crated_by_id TO created_by_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DB03A8386 FOREIGN KEY (created_by_id) REFERENCES usr (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DB03A8386 ON post (created_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DB03A8386');
        $this->addSql('DROP INDEX IDX_5A8A6C8DB03A8386');
        $this->addSql('ALTER TABLE post RENAME COLUMN created_by_id TO crated_by_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8d67d2c520 FOREIGN KEY (crated_by_id) REFERENCES usr (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5a8a6c8d67d2c520 ON post (crated_by_id)');
    }
}
