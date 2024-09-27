<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920125719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_frais ADD user_id INT DEFAULT NULL, ADD etat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_frais ADD CONSTRAINT FK_5FC0A6A7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fiche_frais ADD CONSTRAINT FK_5FC0A6A7D5E86FF FOREIGN KEY (etat_id) REFERENCES etat (id)');
        $this->addSql('CREATE INDEX IDX_5FC0A6A7A76ED395 ON fiche_frais (user_id)');
        $this->addSql('CREATE INDEX IDX_5FC0A6A7D5E86FF ON fiche_frais (etat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_frais DROP FOREIGN KEY FK_5FC0A6A7A76ED395');
        $this->addSql('ALTER TABLE fiche_frais DROP FOREIGN KEY FK_5FC0A6A7D5E86FF');
        $this->addSql('DROP INDEX IDX_5FC0A6A7A76ED395 ON fiche_frais');
        $this->addSql('DROP INDEX IDX_5FC0A6A7D5E86FF ON fiche_frais');
        $this->addSql('ALTER TABLE fiche_frais DROP user_id, DROP etat_id');
    }
}
