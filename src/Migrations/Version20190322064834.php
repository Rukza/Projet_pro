<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322064834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE serial_number ADD mother_id INT DEFAULT NULL, DROP mail_mother, CHANGE wristlet_title wristlet_title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE serial_number ADD CONSTRAINT FK_D948EE2B78A354D FOREIGN KEY (mother_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D948EE2B78A354D ON serial_number (mother_id)');
        $this->addSql('ALTER TABLE requested CHANGE requested_name requested_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE requested CHANGE requested_name requested_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE serial_number DROP FOREIGN KEY FK_D948EE2B78A354D');
        $this->addSql('DROP INDEX IDX_D948EE2B78A354D ON serial_number');
        $this->addSql('ALTER TABLE serial_number ADD mail_mother VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP mother_id, CHANGE wristlet_title wristlet_title VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
