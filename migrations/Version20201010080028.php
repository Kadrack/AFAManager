<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201010080028 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE afamanager_user_audit_trail (user_audit_trail_id INT AUTO_INCREMENT NOT NULL, user_audit_trail_join_user_user INT DEFAULT NULL, user_audit_trail_join_user_who INT DEFAULT NULL, user_audit_trail_date DATETIME NOT NULL, user_audit_trail_login VARCHAR(255) NOT NULL, user_audit_trail_action INT NOT NULL, INDEX IDX_90E1F324CE14F958 (user_audit_trail_join_user_user), INDEX IDX_90E1F324301041C9 (user_audit_trail_join_user_who), PRIMARY KEY(user_audit_trail_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD CONSTRAINT FK_90E1F324CE14F958 FOREIGN KEY (user_audit_trail_join_user_user) REFERENCES afamanager_user (id)');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD CONSTRAINT FK_90E1F324301041C9 FOREIGN KEY (user_audit_trail_join_user_who) REFERENCES afamanager_user (id)');
        $this->addSql('ALTER TABLE afamanager_commission CHANGE commission_role commission_role VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE afamanager_user_audit_trail');
        $this->addSql('ALTER TABLE afamanager_commission CHANGE commission_role commission_role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
