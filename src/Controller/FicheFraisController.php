<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FicheFraisController extends AbstractController
{
    #[Route('/fiche', name: 'app_fiche_frais')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // recuperer l'utilisateur connecté
        $user = $this->getUser();

        // recuperer les fiches de frais
        $ficheFraisRepository = $entityManager->getRepository(FicheFrais::class);

        // filtre les fiche frais par utilisateur
        $ficheFrais = $ficheFraisRepository->findBy(['user' => $user]);


        return $this->render('fiche_frais/index.html.twig', [
            'ficheFrais' => $ficheFrais,
        ]);
    }
}
