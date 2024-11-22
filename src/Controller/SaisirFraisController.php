<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SaisirFraisController extends AbstractController
{
    #[Route('/saisir/frais', name: 'app_saisir_frais')]
    public function index(): Response
    {
        return $this->render('saisir_frais/index.html.twig', [
            'controller_name' => 'SaisirFraisController',
        ]);
    }
}
