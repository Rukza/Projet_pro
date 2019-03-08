<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190308033029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE requested (id INT AUTO_INCREMENT NOT NULL, requested_for_id INT NOT NULL, requested_by_id INT NOT NULL, requested_at DATETIME NOT NULL, requested_token VARCHAR(255) NOT NULL, INDEX IDX_98521BAF37DD3AF1 (requested_for_id), INDEX IDX_98521BAF4DA1E751 (requested_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE requested ADD CONSTRAINT FK_98521BAF37DD3AF1 FOREIGN KEY (requested_for_id) REFERENCES serial_number (id)');
        $this->addSql('ALTER TABLE requested ADD CONSTRAINT FK_98521BAF4DA1E751 FOREIGN KEY (requested_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD token_child_request VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE serial_number ADD child_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE requested');
        $this->addSql('ALTER TABLE serial_number DROP child_requested_at');
        $this->addSql('ALTER TABLE user DROP token_child_request');
    }
}
