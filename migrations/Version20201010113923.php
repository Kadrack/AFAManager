<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201010113923 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD user_audit_trail_join_club INT DEFAULT NULL');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD CONSTRAINT FK_90E1F3246949CCFE FOREIGN KEY (user_audit_trail_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('CREATE INDEX IDX_90E1F3246949CCFE ON afamanager_user_audit_trail (user_audit_trail_join_club)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE afamanager_user_audit_trail DROP FOREIGN KEY FK_90E1F3246949CCFE');
        $this->addSql('DROP INDEX IDX_90E1F3246949CCFE ON afamanager_user_audit_trail');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail DROP user_audit_trail_join_club');
    }
}
