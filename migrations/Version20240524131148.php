<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524131148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_record ADD COLUMN time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_record AS SELECT id, player_name, result FROM game_record');
        $this->addSql('DROP TABLE game_record');
        $this->addSql('CREATE TABLE game_record (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, player_name VARCHAR(255) NOT NULL, result INTEGER NOT NULL)');
        $this->addSql('INSERT INTO game_record (id, player_name, result) SELECT id, player_name, result FROM __temp__game_record');
        $this->addSql('DROP TABLE __temp__game_record');
    }
}
