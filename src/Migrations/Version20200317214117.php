<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200317214117 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE lists (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lists_volunteer_entity (lists_id INT NOT NULL, volunteer_entity_id INT NOT NULL, INDEX IDX_39BF99A59D26499B (lists_id), INDEX IDX_39BF99A51BB212BB (volunteer_entity_id), PRIMARY KEY(lists_id, volunteer_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lists_volunteer_entity ADD CONSTRAINT FK_39BF99A59D26499B FOREIGN KEY (lists_id) REFERENCES lists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lists_volunteer_entity ADD CONSTRAINT FK_39BF99A51BB212BB FOREIGN KEY (volunteer_entity_id) REFERENCES volunteer_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE volunteer_entity ADD password VARCHAR(255) NOT NULL, CHANGE road_number road_number INT DEFAULT NULL, CHANGE road_name road_name VARCHAR(50) DEFAULT NULL, CHANGE zip zip INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lists_volunteer_entity DROP FOREIGN KEY FK_39BF99A59D26499B');
        $this->addSql('DROP TABLE lists');
        $this->addSql('DROP TABLE lists_volunteer_entity');
        $this->addSql('ALTER TABLE volunteer_entity DROP password, CHANGE road_number road_number INT NOT NULL, CHANGE road_name road_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE zip zip INT NOT NULL');
    }
}
