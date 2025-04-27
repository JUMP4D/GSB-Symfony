<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use App\Entity\Etat;
use App\Form\FicheComptableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $visiteur = $entityManager->getRepository(User::class);
        $ficheFraisRepo = $entityManager->getRepository(FicheFrais::class);

        $visiteurs = $visiteur->findAll();
        $fichesFrais = [];

        $form = $this->createForm(FicheComptableType::class, null, [
            'lesvisiteurs' => $visiteurs,
            'lesfiches' => $fichesFrais,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $visiteur = $data['visiteur'];

            if ($visiteur) {
                $queryBuilder = $ficheFraisRepo->createQueryBuilder('f')
                    ->where('f.user = :visiteur')
                    ->setParameter('visiteur', $visiteur);
                }

                $fichesFrais = $queryBuilder->getQuery()->getResult();
            } else {
                $fichesFrais = [];
            }


        return $this->render('comptable/index.html.twig', [
            'controller_name' => 'ComptableController',
            'form' => $form->createView(),
            'fichesFrais' => $fichesFrais,
        ]);
    }

    #[Route('/comptable/fiche/{id}', name: 'app_comptable_fiche')]
    public function fiche(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la fiche de frais par son ID
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->find($id);

        if (!$ficheFrais) {
            throw $this->createNotFoundException('Fiche de frais non trouvée.');
        }

        // Récupérer les lignes de frais forfait
        $lignesFraisForfait = $entityManager->getRepository(LigneFraisForfait::class)
            ->findBy(['ficheFrais' => $ficheFrais]);

        // Récupérer les lignes de frais hors forfait
        $lignesFraisHorsForfait = $entityManager->getRepository(LigneFraisHorsForfait::class)
            ->findBy(['ficheFrais' => $ficheFrais]);

        return $this->render('comptable/detail.html.twig', [
            'ficheFrais' => $ficheFrais,
            'lignesFraisForfait' => $lignesFraisForfait,
            'lignesFraisHorsForfait' => $lignesFraisHorsForfait,
        ]);
    }
    #[\Symfony\Component\Routing\Annotation\Route('/comptable/fiche/{id}/valider', name: 'app_comptable_valider_fiche', methods: ['POST'])]
    public function validerFiche(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Récupérer la fiche de frais
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->find($id);

        if (!$ficheFrais) {
            $this->addFlash('error', 'Fiche de frais introuvable.');
            return $this->redirectToRoute('app_comptable');
        }

        // Récupérer l'objet Etat correspondant à l'ID 4
        $etat = $entityManager->getRepository(Etat::class)->find(4);

        if (!$etat) {
            $this->addFlash('error', 'État introuvable.');
            return $this->redirectToRoute('app_comptable');
        }

        // Mettre à jour l'état de la fiche
        $ficheFrais->setEtat($etat);
        $entityManager->flush();

        $this->addFlash('success', 'La fiche de frais a été validée avec succès.');

        return $this->redirectToRoute('app_comptable_fiche', ['id' => $id]);
    }
}
