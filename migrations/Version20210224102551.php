<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224102551 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jeu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, genre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, video LONGTEXT NOT NULL, lien LONGTEXT NOT NULL, what LONGTEXT NOT NULL, why LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE screenshot (id INT AUTO_INCREMENT NOT NULL, jeu_id INT DEFAULT NULL, file VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_58991E418C9E392E (jeu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E418C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E418C9E392E');
        $this->addSql('DROP TABLE jeu');
        $this->addSql('DROP TABLE screenshot');
    }
}
