<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190401062300 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE weared (id INT AUTO_INCREMENT NOT NULL, wear_wristlet_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_708D721C862CC228 (wear_wristlet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE weared ADD CONSTRAINT FK_708D721C862CC228 FOREIGN KEY (wear_wristlet_id) REFERENCES serial_number (id)');
        $this->addSql('DROP TABLE serial_number_user');
        $this->addSql('ALTER TABLE requested ADD requested_accepted TINYINT(1) DEFAULT NULL, ADD requested_banned TINYINT(1) DEFAULT NULL, CHANGE requested_name requested_mother_response VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE serial_number ADD mother_id INT NOT NULL, ADD attributed_to TINYINT(1) DEFAULT NULL, DROP mail_mother, CHANGE active active_serial TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE serial_number ADD CONSTRAINT FK_D948EE2B78A354D FOREIGN KEY (mother_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D948EE2B78A354D ON serial_number (mother_id)');
        $this->addSql('ALTER TABLE user ADD adress VARCHAR(255) NOT NULL, ADD postal_code VARCHAR(10) NOT NULL, ADD city VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE serial_number_user (serial_number_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C0909D84A76ED395 (user_id), INDEX IDX_C0909D849E45236B (serial_number_id), PRIMARY KEY(serial_number_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE serial_number_user ADD CONSTRAINT FK_C0909D849E45236B FOREIGN KEY (serial_number_id) REFERENCES serial_number (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serial_number_user ADD CONSTRAINT FK_C0909D84A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE weared');
        $this->addSql('ALTER TABLE requested DROP requested_accepted, DROP requested_banned, CHANGE requested_mother_response requested_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE serial_number DROP FOREIGN KEY FK_D948EE2B78A354D');
        $this->addSql('DROP INDEX IDX_D948EE2B78A354D ON serial_number');
        $this->addSql('ALTER TABLE serial_number ADD active TINYINT(1) DEFAULT NULL, ADD mail_mother VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP mother_id, DROP active_serial, DROP attributed_to');
        $this->addSql('ALTER TABLE user DROP adress, DROP postal_code, DROP city');
    }
}
