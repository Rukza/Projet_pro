<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322102508 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD mother_for_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492043D998 FOREIGN KEY (mother_for_id) REFERENCES serial_number (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492043D998 ON user (mother_for_id)');
        $this->addSql('ALTER TABLE serial_number DROP FOREIGN KEY FK_D948EE2B78A354D');
        $this->addSql('DROP INDEX IDX_D948EE2B78A354D ON serial_number');
        $this->addSql('ALTER TABLE serial_number DROP mother_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE serial_number ADD mother_id INT NOT NULL');
        $this->addSql('ALTER TABLE serial_number ADD CONSTRAINT FK_D948EE2B78A354D FOREIGN KEY (mother_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D948EE2B78A354D ON serial_number (mother_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492043D998');
        $this->addSql('DROP INDEX IDX_8D93D6492043D998 ON user');
        $this->addSql('ALTER TABLE user DROP mother_for_id');
    }
}
