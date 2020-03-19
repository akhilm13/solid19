<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200319233417 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE list_requirements CHANGE date_time_status_changed date_time_status_changed DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE list_requirements ADD CONSTRAINT FK_A5B44B1A6D70A54 FOREIGN KEY (list_id_id) REFERENCES lists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lists ADD CONSTRAINT FK_8269FA54D2A43A5 FOREIGN KEY (volunteer_id_id) REFERENCES volunteer_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE volunteer_entity ADD created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE list_requirements DROP FOREIGN KEY FK_A5B44B1A6D70A54');
        $this->addSql('ALTER TABLE list_requirements CHANGE date_time_status_changed date_time_status_changed DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE lists DROP FOREIGN KEY FK_8269FA54D2A43A5');
        $this->addSql('ALTER TABLE volunteer_entity DROP created_at');
    }
}
