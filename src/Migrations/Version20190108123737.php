<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190108123737 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, libelle LONGTEXT NOT NULL, ouverte TINYINT(1) NOT NULL, piecejointe VARCHAR(255) DEFAULT NULL, INDEX IDX_B6F7494EE075F7A4 (partie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_5FB6DEC7FB88E14F (utilisateur_id), INDEX IDX_5FB6DEC71E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse_reponse_possible (reponse_id INT NOT NULL, reponse_possible_id INT NOT NULL, INDEX IDX_2AD38062CF18BB82 (reponse_id), INDEX IDX_2AD38062C53BC6BC (reponse_possible_id), PRIMARY KEY(reponse_id, reponse_possible_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse_possible (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, question_id INT NOT NULL, libelle LONGTEXT NOT NULL, piecejointe VARCHAR(255) DEFAULT NULL, correct TINYINT(1) NOT NULL, INDEX IDX_21290E49FB88E14F (utilisateur_id), INDEX IDX_21290E491E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(40) NOT NULL, mdp VARCHAR(32) NOT NULL, mail VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur_partie (utilisateur_id INT NOT NULL, partie_id INT NOT NULL, INDEX IDX_643625B6FB88E14F (utilisateur_id), INDEX IDX_643625B6E075F7A4 (partie_id), PRIMARY KEY(utilisateur_id, partie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EE075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE reponse_reponse_possible ADD CONSTRAINT FK_2AD38062CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse_reponse_possible ADD CONSTRAINT FK_2AD38062C53BC6BC FOREIGN KEY (reponse_possible_id) REFERENCES reponse_possible (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse_possible ADD CONSTRAINT FK_21290E49FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reponse_possible ADD CONSTRAINT FK_21290E491E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE utilisateur_partie ADD CONSTRAINT FK_643625B6FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_partie ADD CONSTRAINT FK_643625B6E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EE075F7A4');
        $this->addSql('ALTER TABLE utilisateur_partie DROP FOREIGN KEY FK_643625B6E075F7A4');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('ALTER TABLE reponse_possible DROP FOREIGN KEY FK_21290E491E27F6BF');
        $this->addSql('ALTER TABLE reponse_reponse_possible DROP FOREIGN KEY FK_2AD38062CF18BB82');
        $this->addSql('ALTER TABLE reponse_reponse_possible DROP FOREIGN KEY FK_2AD38062C53BC6BC');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7FB88E14F');
        $this->addSql('ALTER TABLE reponse_possible DROP FOREIGN KEY FK_21290E49FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_partie DROP FOREIGN KEY FK_643625B6FB88E14F');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reponse_reponse_possible');
        $this->addSql('DROP TABLE reponse_possible');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_partie');
    }
}
