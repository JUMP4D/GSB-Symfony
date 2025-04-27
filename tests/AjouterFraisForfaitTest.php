<?php

namespace App\Tests\Entity;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use PHPUnit\Framework\TestCase;

class AjouterFraisForfaitTest extends TestCase
{
    public function testAjouterLigneFraisForfait(): void
    {
        // Création d'une instance de FicheFrais
        $ficheFrais = new FicheFrais();

        // Création d'une instance de FraisForfait
        $fraisForfait = new FraisForfait();
        $fraisForfait->setLibelle('Forfait Etape');
        $fraisForfait->setMontant(25.00);

        // Création d'une ligne de frais forfait
        $ligneFraisForfait = new LigneFraisForfait();
        $ligneFraisForfait->setFicheFrais($ficheFrais);
        $ligneFraisForfait->setFraisForfait($fraisForfait);
        $ligneFraisForfait->setQuantite(2);

        // Ajout de la ligne à la fiche de frais
        $ficheFrais->addLigneFraisForfait($ligneFraisForfait);

        // Vérification que la ligne a bien été ajoutée
        $this->assertContains($ligneFraisForfait, $ficheFrais->getLigneFraisForfaits());
        $this->assertSame(2, $ligneFraisForfait->getQuantite());
        $this->assertSame('Forfait Etape', $ligneFraisForfait->getFraisForfait()->getLibelle());
    }
}