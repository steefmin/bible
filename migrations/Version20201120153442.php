<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201120153442 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE line (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, number INTEGER NOT NULL, time INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D114B4F616A2B381 ON line (book_id)');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, source_id INTEGER NOT NULL, book_id INTEGER NOT NULL, external_id VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB953C1C61 ON location (source_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E9E89CB16A2B381 ON location (book_id)');
        $this->addSql('CREATE TABLE source (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE line');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE source');
    }
}
