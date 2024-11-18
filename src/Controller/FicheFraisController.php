<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\FicheFraisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FicheFraisController extends AbstractController
{
    #[Route('/fiche', name: 'app_fiche_frais')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // recuperer l'utilisateur connecté
        $user = $this->getUser();

        // recuperer les fiches de frais
        $ficheFraisRepository = $entityManager->getRepository(FicheFrais::class);


        //donner les fichefrais au formulaire
        $form = $this->createForm(FicheFraisType::class, null, [
            'lesfiches' => $ficheFraisRepository->findBy(['user' => $user])
        ]);

        $form->handleRequest($request);

        $ficheFrais = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedFicheFrais = $form->get('mois')->getData();
            if ($selectedFicheFrais) {
                $selectedMois = $selectedFicheFrais->getMois();
                $ficheFrais = $ficheFraisRepository->findBy([
                    'user' => $user,
                    'mois' => $selectedMois
                ]);
            }
        }

        return $this->render('fiche_frais/index.html.twig', [
            'ficheFrais' => $ficheFrais,
            'form' => $form->createView()
        ]);
    }
}
