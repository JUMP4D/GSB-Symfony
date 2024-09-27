<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920122442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_frais CHANGE montant_valid montant_valid NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE frais_forfait CHANGE montant montant NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE ligne_frais_hors_forfait CHANGE montant montant NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_frais CHANGE montant_valid montant_valid NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE frais_forfait CHANGE montant montant NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE ligne_frais_hors_forfait CHANGE montant montant NUMERIC(10, 0) NOT NULL');
    }
}
