<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318070338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE lists_volunteer_entity');
        $this->addSql('ALTER TABLE lists ADD volunteer_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lists ADD CONSTRAINT FK_8269FA54D2A43A5 FOREIGN KEY (volunteer_id_id) REFERENCES volunteer_entity (id)');
        $this->addSql('CREATE INDEX IDX_8269FA54D2A43A5 ON lists (volunteer_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE lists_volunteer_entity (lists_id INT NOT NULL, volunteer_entity_id INT NOT NULL, INDEX IDX_39BF99A59D26499B (lists_id), INDEX IDX_39BF99A51BB212BB (volunteer_entity_id), PRIMARY KEY(lists_id, volunteer_entity_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE lists_volunteer_entity ADD CONSTRAINT FK_39BF99A51BB212BB FOREIGN KEY (volunteer_entity_id) REFERENCES volunteer_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lists_volunteer_entity ADD CONSTRAINT FK_39BF99A59D26499B FOREIGN KEY (lists_id) REFERENCES lists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lists DROP FOREIGN KEY FK_8269FA54D2A43A5');
        $this->addSql('DROP INDEX IDX_8269FA54D2A43A5 ON lists');
        $this->addSql('ALTER TABLE lists DROP volunteer_id_id');
    }
}
