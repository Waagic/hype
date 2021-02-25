<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225174448 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E418C9E392E');
        $this->addSql('ALTER TABLE user_jeu DROP FOREIGN KEY FK_69F2EC3E8C9E392E');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, genre VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, video VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, what LONGTEXT NOT NULL, why LONGTEXT NOT NULL, cover VARCHAR(255) NOT NULL, thumbnail VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_game (user_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_59AA7D45A76ED395 (user_id), INDEX IDX_59AA7D45E48FD905 (game_id), PRIMARY KEY(user_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE jeu');
        $this->addSql('DROP TABLE user_jeu');
        $this->addSql('DROP INDEX IDX_58991E418C9E392E ON screenshot');
        $this->addSql('ALTER TABLE screenshot CHANGE jeu_id game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E41E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_58991E41E48FD905 ON screenshot (game_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE screenshot DROP FOREIGN KEY FK_58991E41E48FD905');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D45E48FD905');
        $this->addSql('CREATE TABLE jeu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, genre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prix DOUBLE PRECISION NOT NULL, video VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lien VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, what LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, why LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, cover VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, thumbnail VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_jeu (user_id INT NOT NULL, jeu_id INT NOT NULL, INDEX IDX_69F2EC3E8C9E392E (jeu_id), INDEX IDX_69F2EC3EA76ED395 (user_id), PRIMARY KEY(user_id, jeu_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_jeu ADD CONSTRAINT FK_69F2EC3E8C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_jeu ADD CONSTRAINT FK_69F2EC3EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE user_game');
        $this->addSql('DROP INDEX IDX_58991E41E48FD905 ON screenshot');
        $this->addSql('ALTER TABLE screenshot CHANGE game_id jeu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE screenshot ADD CONSTRAINT FK_58991E418C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_58991E418C9E392E ON screenshot (jeu_id)');
    }
}
