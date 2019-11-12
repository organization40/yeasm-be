<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191109084436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE usr ADD parent_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT FK_1762498CD526A7D3 FOREIGN KEY (parent_user_id) REFERENCES usr (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1762498CD526A7D3 ON usr (parent_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498CD526A7D3');
        $this->addSql('DROP INDEX IDX_1762498CD526A7D3');
        $this->addSql('ALTER TABLE usr DROP parent_user_id');
    }
}
