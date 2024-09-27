<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920125216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_frais_forfait ADD frais_forfait_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_frais_forfait ADD CONSTRAINT FK_BD293ECF7B70375E FOREIGN KEY (frais_forfait_id) REFERENCES frais_forfait (id)');
        $this->addSql('CREATE INDEX IDX_BD293ECF7B70375E ON ligne_frais_forfait (frais_forfait_id)');
        $this->addSql('ALTER TABLE ligne_frais_hors_forfait ADD fiche_frais_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_frais_hors_forfait ADD CONSTRAINT FK_EC01626DD94F5755 FOREIGN KEY (fiche_frais_id) REFERENCES fiche_frais (id)');
        $this->addSql('CREATE INDEX IDX_EC01626DD94F5755 ON ligne_frais_hors_forfait (fiche_frais_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_frais_forfait DROP FOREIGN KEY FK_BD293ECF7B70375E');
        $this->addSql('DROP INDEX IDX_BD293ECF7B70375E ON ligne_frais_forfait');
        $this->addSql('ALTER TABLE ligne_frais_forfait DROP frais_forfait_id');
        $this->addSql('ALTER TABLE ligne_frais_hors_forfait DROP FOREIGN KEY FK_EC01626DD94F5755');
        $this->addSql('DROP INDEX IDX_EC01626DD94F5755 ON ligne_frais_hors_forfait');
        $this->addSql('ALTER TABLE ligne_frais_hors_forfait DROP fiche_frais_id');
    }
}
