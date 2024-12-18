<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216164320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE connection (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, connected_to_user_id INT NOT NULL, INDEX IDX_29F77366A76ED395 (user_id), INDEX IDX_29F7736691668D72 (connected_to_user_id), UNIQUE INDEX unique_user_connection (user_id, connected_to_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `personal_data` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, given_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, about_me LONGTEXT DEFAULT NULL, UNIQUE INDEX unique_user_personal_data (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password LONGTEXT NOT NULL, auth_token LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, token_refreshed_at DATETIME NOT NULL, user_code INT UNSIGNED NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX unique_email (email), UNIQUE INDEX unique_auth_token (auth_token), UNIQUE INDEX unique_user_code (user_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F7736691668D72 FOREIGN KEY (connected_to_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `personal_data` ADD CONSTRAINT FK_9CF9F45EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F77366A76ED395');
        $this->addSql('ALTER TABLE connection DROP FOREIGN KEY FK_29F7736691668D72');
        $this->addSql('ALTER TABLE `personal_data` DROP FOREIGN KEY FK_9CF9F45EA76ED395');
        $this->addSql('DROP TABLE connection');
        $this->addSql('DROP TABLE `personal_data`');
        $this->addSql('DROP TABLE `user`');
    }
}
