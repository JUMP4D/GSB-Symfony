<?php

namespace App\Tests\Entity;

use App\Entity\FicheFrais;
use App\Entity\Etat;
use PHPUnit\Framework\TestCase;

class FicheFraisTest extends TestCase
{
    public function testSetEtat(): void
    {
        // Création d'une instance de FicheFrais
        $ficheFrais = new FicheFrais();

        // Création d'une instance de Etat
        $etat = new Etat();
        $etat->setLibelle('Validée');

        // Appel de la méthode setEtat
        $ficheFrais->setEtat($etat);

        // Vérification que l'état a bien été défini
        $this->assertSame($etat, $ficheFrais->getEtat());
    }
}