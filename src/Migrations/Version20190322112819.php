<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322112819 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE requested (id INT AUTO_INCREMENT NOT NULL, requested_for_id INT NOT NULL, requested_by_id INT NOT NULL, requested_name VARCHAR(255) DEFAULT NULL, requested_at DATETIME DEFAULT NULL, requested_token VARCHAR(255) DEFAULT NULL, requested_refused TINYINT(1) DEFAULT NULL, INDEX IDX_98521BAF37DD3AF1 (requested_for_id), INDEX IDX_98521BAF4DA1E751 (requested_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_user (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_332CA4DDD60322AC (role_id), INDEX IDX_332CA4DDA76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serial_number (id INT AUTO_INCREMENT NOT NULL, mother_id INT NOT NULL, serial_wristlet VARCHAR(255) NOT NULL, wristlet_title VARCHAR(255) NOT NULL, active TINYINT(1) DEFAULT NULL, INDEX IDX_D948EE2B78A354D (mother_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, pswd VARCHAR(255) NOT NULL, password_requested_at DATETIME DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE requested ADD CONSTRAINT FK_98521BAF37DD3AF1 FOREIGN KEY (requested_for_id) REFERENCES serial_number (id)');
        $this->addSql('ALTER TABLE requested ADD CONSTRAINT FK_98521BAF4DA1E751 FOREIGN KEY (requested_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serial_number ADD CONSTRAINT FK_D948EE2B78A354D FOREIGN KEY (mother_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('ALTER TABLE requested DROP FOREIGN KEY FK_98521BAF37DD3AF1');
        $this->addSql('ALTER TABLE requested DROP FOREIGN KEY FK_98521BAF4DA1E751');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDA76ED395');
        $this->addSql('ALTER TABLE serial_number DROP FOREIGN KEY FK_D948EE2B78A354D');
        $this->addSql('DROP TABLE requested');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_user');
        $this->addSql('DROP TABLE serial_number');
        $this->addSql('DROP TABLE user');
    }
}
