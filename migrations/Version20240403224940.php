<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403224940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, id_auteur INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(255) NOT NULL, id_auteur INT NOT NULL, id_article INT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE paniers');
        $this->addSql('ALTER TABLE assistance CHANGE date_envoie date_envoie DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE produit ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD image_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, contenu TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, id_auteur INT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, id_auteur INT NOT NULL, id_article INT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE paniers (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, produit_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE produit DROP updated_at, DROP image_name');
        $this->addSql('ALTER TABLE assistance CHANGE date_envoie date_envoie VARCHAR(255) NOT NULL');
    }
}
