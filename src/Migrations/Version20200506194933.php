<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200506194933 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE afamanager_user ADD api_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B23E8F77BA2F5EB ON afamanager_user (api_token)');
        $this->addSql('ALTER TABLE afamanager_user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_7B23E8F7AA08CB10');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_7B23E8F77BA2F5EB ON afamanager_user');
        $this->addSql('ALTER TABLE afamanager_user DROP api_token');
        $this->addSql('ALTER TABLE afamanager_user RENAME INDEX uniq_7b23e8f7aa08cb10 TO UNIQ_8D93D649E7927C74');
    }
}
