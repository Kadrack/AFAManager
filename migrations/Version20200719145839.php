<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200719145839 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE afamanager_commission_member ADD commission_member_join_member INT NOT NULL');
        $this->addSql('ALTER TABLE afamanager_commission_member ADD CONSTRAINT FK_FB98DA5BDF061EC0 FOREIGN KEY (commission_member_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('CREATE INDEX IDX_FB98DA5BDF061EC0 ON afamanager_commission_member (commission_member_join_member)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE afamanager_commission_member DROP FOREIGN KEY FK_FB98DA5BDF061EC0');
        $this->addSql('DROP INDEX IDX_FB98DA5BDF061EC0 ON afamanager_commission_member');
        $this->addSql('ALTER TABLE afamanager_commission_member DROP commission_member_join_member');
    }
}
