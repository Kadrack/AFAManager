<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519140002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_member ADD member_join_member_actual_club INT NOT NULL');
        $this->addSql('ALTER TABLE afamanager_member ADD CONSTRAINT FK_CBC392553510B9DD FOREIGN KEY (member_join_member_actual_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBC392553510B9DD ON afamanager_member (member_join_member_actual_club)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_member DROP FOREIGN KEY FK_CBC392553510B9DD');
        $this->addSql('DROP INDEX UNIQ_CBC392553510B9DD ON afamanager_member');
        $this->addSql('ALTER TABLE afamanager_member DROP member_join_member_actual_club');
    }
}
