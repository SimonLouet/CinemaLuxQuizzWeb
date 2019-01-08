<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190108135542 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponse_possible DROP FOREIGN KEY FK_21290E49FB88E14F');
        $this->addSql('DROP INDEX IDX_21290E49FB88E14F ON reponse_possible');
        $this->addSql('ALTER TABLE reponse_possible DROP utilisateur_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponse_possible ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE reponse_possible ADD CONSTRAINT FK_21290E49FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_21290E49FB88E14F ON reponse_possible (utilisateur_id)');
    }
}
