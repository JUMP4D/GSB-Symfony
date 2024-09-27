<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920124919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_frais DROP FOREIGN KEY FK_5FC0A6A7D94F5755');
        $this->addSql('DROP INDEX IDX_5FC0A6A7D94F5755 ON fiche_frais');
        $this->addSql('ALTER TABLE fiche_frais DROP fiche_frais_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_frais ADD fiche_frais_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche_frais ADD CONSTRAINT FK_5FC0A6A7D94F5755 FOREIGN KEY (fiche_frais_id) REFERENCES etat (id)');
        $this->addSql('CREATE INDEX IDX_5FC0A6A7D94F5755 ON fiche_frais (fiche_frais_id)');
    }
}
