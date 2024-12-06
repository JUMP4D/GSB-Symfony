<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\FraisForfait;
use App\Form\FicheFraisHorsType;
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
        // Créer le formulaire pour saisir les lignes frais Forfait
        $formF = $this->createForm(FicheType::class);

        // Créer le formulaire pour saisir les lignes frais hors Forfait
        $formFH = $this->createForm(FicheFraisHorsType::class);

        // Gérer la soumission du formulaire
        $formF->handleRequest($request);
        $formFH->handleRequest($request);

        $user = $this->getUser();

        $existingFicheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy([
            'user' => $user,
            'mois' => new \DateTime(date('Y-m-01 00:00:00'))
        ]);

        $datefiche = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-01 00:00:00'))->format('Y-m');

        // crée une ligne fiche frais
        if ($existingFicheFrais) {

            if($formF->isSubmitted() && $formF->isValid()) {
                $data = $formF->getData();

                $categories = [
                    'Forfait Etape' => $data['forfaitEtape'],
                    'Frais Kilométrique' => $data['fraisKilometrique'],
                    'Nuitée Hôtel' => $data['nuiteeHotel'],
                    'Repas Restaurant' => $data['repasRestaurant'],
                ];

                // Mettre à jour les lignes de frais forfaitaires existantes
                foreach ($categories as $libelle => $quantite) {
                    $fraisForfait = $entityManager->getRepository(FraisForfait::class)->findOneBy(['libelle' => $libelle]);
                    $ligneFraisForfait = $entityManager->getRepository(LigneFraisForfait::class)->findOneBy([
                        'ficheFrais' => $existingFicheFrais,
                        'fraisForfait' => $fraisForfait
                    ]);

                    if ($ligneFraisForfait) {
                        $ligneFraisForfait->setQuantite($quantite);
                        $entityManager->persist($ligneFraisForfait);
                    }
                }
                $entityManager->flush();
            }else{
                $formF->setData([
                    'forfaitEtape' => $existingFicheFrais->getLigneFraisForfaits()->get(0)->getQuantite(),
                    'fraisKilometrique' => $existingFicheFrais->getLigneFraisForfaits()->get(1)->getQuantite(),
                    'nuiteeHotel' => $existingFicheFrais->getLigneFraisForfaits()->get(2)->getQuantite(),
                    'repasRestaurant' => $existingFicheFrais->getLigneFraisForfaits()->get(3)->getQuantite(),
                ]);
            }
        } else {
            // Vérifier si le formulaire est soumis et valide
            if ($formF->isSubmitted() && $formF->isValid()) {
                // Récupérer les données du formulaire
                $data = $formF->getData();

                // Récupérer l'utilisateur connecté
                $user = $this->getUser();

                // Créer une nouvelle fiche de frais
                $ficheFrais = new FicheFrais();
                $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->find(2));
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
        }

        // crée une ligne fiche frais hors forfait

        // Rendre le formulaire de saisie des fiches de frais
        return $this->render('fiche_frais/saisir.html.twig', [
            'datefiche' => $datefiche,
            'formF' => $formF->createView(),
            'formFH' => $formFH->createView()
        ]);
    }
}
