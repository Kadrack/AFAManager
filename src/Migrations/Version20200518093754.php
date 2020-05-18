<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200518093754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_member_modification CHANGE member_modification_firstname member_modification_firstname VARCHAR(255) DEFAULT NULL, CHANGE member_modification_name member_modification_name VARCHAR(255) DEFAULT NULL, CHANGE member_modification_photo member_modification_photo VARCHAR(255) DEFAULT NULL, CHANGE member_modification_sex member_modification_sex INT DEFAULT NULL, CHANGE member_modification_address member_modification_address LONGTEXT DEFAULT NULL, CHANGE member_modification_city member_modification_city VARCHAR(255) DEFAULT NULL, CHANGE member_modification_country member_modification_country VARCHAR(255) DEFAULT NULL, CHANGE member_modification_birthday member_modification_birthday DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_member_modification CHANGE member_modification_firstname member_modification_firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE member_modification_name member_modification_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE member_modification_photo member_modification_photo VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE member_modification_sex member_modification_sex INT NOT NULL, CHANGE member_modification_address member_modification_address LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE member_modification_city member_modification_city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE member_modification_country member_modification_country VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE member_modification_birthday member_modification_birthday DATE NOT NULL');
    }
}
