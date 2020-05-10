<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200507190535 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_user ADD user_join_member INT DEFAULT NULL, ADD user_join_club INT DEFAULT NULL, ADD user_status INT NOT NULL');
        $this->addSql('ALTER TABLE afamanager_user ADD CONSTRAINT FK_7B23E8F77111AECB FOREIGN KEY (user_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_user ADD CONSTRAINT FK_7B23E8F72956F80F FOREIGN KEY (user_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B23E8F77111AECB ON afamanager_user (user_join_member)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B23E8F72956F80F ON afamanager_user (user_join_club)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_user DROP FOREIGN KEY FK_7B23E8F77111AECB');
        $this->addSql('ALTER TABLE afamanager_user DROP FOREIGN KEY FK_7B23E8F72956F80F');
        $this->addSql('DROP INDEX UNIQ_7B23E8F77111AECB ON afamanager_user');
        $this->addSql('DROP INDEX UNIQ_7B23E8F72956F80F ON afamanager_user');
        $this->addSql('ALTER TABLE afamanager_user DROP user_join_member, DROP user_join_club, DROP user_status');
    }
}
