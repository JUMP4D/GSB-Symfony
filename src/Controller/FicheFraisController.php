<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\FraisForfait;
use App\Form\FicheFraisType;
use App\Form\FicheType;
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
        $ligneFraisForfaitRepository = $entityManager->getRepository(LigneFraisForfait::class);

        //donner les fichefrais au formulaire
        $form = $this->createForm(FicheFraisType::class, null, [
            'lesfiches' => $ficheFraisRepository->findBy(['user' => $user])
        ]);

        $form->handleRequest($request);

        $ficheFrais = [];
        $ligneFraisForfaits = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedFicheFrais = $form->get('mois')->getData();
            if ($selectedFicheFrais) {
                $selectedMois = $selectedFicheFrais->getMois();
                $ficheFrais = $ficheFraisRepository->findBy([
                    'user' => $user,
                    'mois' => $selectedMois
                ]);

                foreach ($ficheFrais as $fiche) {
                    $ligneFraisForfaits = array_merge($ligneFraisForfaits, $ligneFraisForfaitRepository->findBy(['ficheFrais' => $fiche]));
                }
            }
        }

        return $this->render('fiche_frais/index.html.twig', [
            'ficheFrais' => $ficheFrais,
            'ligneFraisForfaits' => $ligneFraisForfaits,
            'form' => $form->createView()
        ]);
    }

    #[Route('/saisirfiche', name: 'app_saisir_fiche')]
    public function saisirFiche(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire pour saisir les fiches de frais
        $form = $this->createForm(FicheType::class);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Récupérer l'utilisateur connecté
            $user = $this->getUser();

            // Créer une nouvelle fiche de frais
            $ficheFrais = new FicheFrais();
            $ficheFrais->setUser($user);
            $ficheFrais->setDateModif(new \DateTime());
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0.00);
            $ficheFrais->setMois(new \DateTime(date('Y-m-01 00:00:00'))); // Set the current month as DateTime

            // Persister la fiche de frais dans la base de données
            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            // Définir les catégories de frais forfaitaires
            $categories = [
                'Forfait Etape' => $data['forfaitEtape'],
                'Frais Kilométrique' => $data['fraisKilometrique'],
                'Nuitée Hôtel' => $data['nuiteeHotel'],
                'Repas Restaurant' => $data['repasRestaurant'],
            ];

            // Créer et persister les lignes de frais forfaitaires
            foreach ($categories as $libelle => $quantite) {
                $fraisForfait = $entityManager->getRepository(FraisForfait::class)->findOneBy(['libelle' => $libelle]);
                $ligneFraisForfait = new LigneFraisForfait();
                $ligneFraisForfait->setFicheFrais($ficheFrais);
                $ligneFraisForfait->setFraisForfait($fraisForfait);
                $ligneFraisForfait->setQuantite($quantite);
                $entityManager->persist($ligneFraisForfait);
            }

            // Sauvegarder les lignes de frais forfaitaires dans la base de données
            $entityManager->flush();

            // Rediriger vers la route 'app_fiche_frais'
            return $this->redirectToRoute('app_fiche_frais');
        }

        // Rendre le formulaire de saisie des fiches de frais
        return $this->render('fiche_frais/saisir.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
