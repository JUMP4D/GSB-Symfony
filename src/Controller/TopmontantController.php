<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TopmontantController extends AbstractController
{
    #[Route('/topmontant', name: 'choisir_mois')]
    public function choisirMois(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ficheFrais = [];
        $selectedMonth = null;

        // Récupérer le mois sélectionné depuis la requête
        if ($request->query->get('mois')) {
            $selectedMonth = $request->query->get('mois');

            // Récupérer les utilisateurs ayant des fiches de frais validées pour le mois sélectionné
            $ficheFraisRepository = $entityManager->getRepository(FicheFrais::class);

            // On récupère les fiches de frais validées (etat = 4) pour le mois sélectionné
            $ficheFrais = $ficheFraisRepository->createQueryBuilder('f')
                ->innerJoin('f.user', 'u')
                ->where('f.mois = :mois')
                ->andWhere('f.etat = 4')  // État 4 pour les fiches validées
                ->setParameter('mois', $selectedMonth)
                ->getQuery()
                ->getResult();
        }


        return $this->render('topmontant/index.html.twig', [
            'ficheFrais' => $ficheFrais,
            'selectedMonth' => $selectedMonth,
        ]);
    }
}
