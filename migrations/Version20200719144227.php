<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200719144227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE afamanager_commission_member (commission_member_id INT NOT NULL, commission_member_join_commission INT NOT NULL, INDEX IDX_FB98DA5B40693046 (commission_member_join_commission), PRIMARY KEY(commission_member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE afamanager_commission_member ADD CONSTRAINT FK_FB98DA5B40693046 FOREIGN KEY (commission_member_join_commission) REFERENCES afamanager_commission (commission_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE afamanager_commission_member');
    }
}
