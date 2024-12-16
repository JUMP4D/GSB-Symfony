<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisHorsForfait;
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

        // recuperer les fiche hors forfait
        $ficheHorsForfaitRepository = $entityManager->getRepository(LigneFraisHorsForfait::class);

        //donner les fichefrais au formulaire
        $form = $this->createForm(FicheFraisType::class, null, [
            'lesfiches' => $ficheFraisRepository->findBy(['user' => $user])
        ]);

        $form->handleRequest($request);

        $ficheFrais = [];
        $ligneFraisForfaits = [];
        $ligneFraisHorsForfaits = [];

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
                    $ligneFraisHorsForfaits = array_merge($ligneFraisHorsForfaits, $ficheHorsForfaitRepository->findBy(['ficheFrais' => $fiche]));
                }
            }
        }

        return $this->render('fiche_frais/index.html.twig', [
            'ficheFrais' => $ficheFrais,
            'ligneFraisForfaits' => $ligneFraisForfaits,
            'ligneFraisHorsForfaits' => $ligneFraisHorsForfaits,
            'form' => $form->createView()
        ]);
    }

    #[Route('/saisirfiche', name: 'app_saisir_fiche')]
    public function saisirFiche(Request $request, EntityManagerInterface $entityManager): Response
    {
        $datefiche = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-01 00:00:00'))->format('m-Y');
        // Créer le formulaire pour saisir les lignes frais Forfait
        $formF = $this->createForm(FicheType::class);

        // Créer le formulaire pour saisir les lignes frais hors Forfait
        $formFH = $this->createForm(FicheFraisHorsType::class);

        // Gérer la soumission du formulaire
        $formF->handleRequest($request);
        $formFH->handleRequest($request);

        $user = $this->getUser();

        $fichedumois = $entityManager->getRepository(FicheFrais::class)->findOneBy([
            'user' => $user,
            'mois' => new \DateTime(date('Y-m-01 00:00:00'))
        ]);

        if($request->get('action') == 'reset'){
            return $this->redirectToRoute('app_saisir_fiche');
        }

        if($fichedumois == null){

            $ficheFrais = new FicheFrais();
            $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->find(2));
            $ficheFrais->setUser($user);
            $ficheFrais->setDateModif(new \DateTime());
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0.00);
            $ficheFrais->setMois(new \DateTime(date('Y-m-01 00:00:00'))); // Set the current month as DateTime
            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            for ($i = 1; $i <= 4; $i++) {
                $ligneFraisForfait = new LigneFraisForfait();
                $ligneFraisForfait->setFicheFrais($ficheFrais);
                $ligneFraisForfait->setFraisForfait($entityManager->getRepository(FraisForfait::class)->find($i));
                $ligneFraisForfait->setQuantite(0);
                $entityManager->persist($ligneFraisForfait);
            }
            $entityManager->flush();

        }
        else{
            if($formF->isSubmitted() && $formF->isValid()) {
                $data = $formF->getData();
                $fichedumois->getLigneFraisForfaits()->get(0)->setQuantite($data['forfaitEtape']);
                $fichedumois->getLigneFraisForfaits()->get(1)->setQuantite($data['fraisKilometrique']);
                $fichedumois->getLigneFraisForfaits()->get(2)->setQuantite($data['nuiteeHotel']);
                $fichedumois->getLigneFraisForfaits()->get(3)->setQuantite($data['repasRestaurant']);
                $entityManager->flush();
            }else{
                $formF->setData([
                    'forfaitEtape' => $fichedumois->getLigneFraisForfaits()->get(0)->getQuantite(),
                    'fraisKilometrique' => $fichedumois->getLigneFraisForfaits()->get(1)->getQuantite(),
                    'nuiteeHotel' => $fichedumois->getLigneFraisForfaits()->get(2)->getQuantite(),
                    'repasRestaurant' => $fichedumois->getLigneFraisForfaits()->get(3)->getQuantite(),
                ]);
            }
        }

        if ($formFH->isSubmitted() && $formFH->isValid()) {
            $data = $formFH->getData();

            $ligneFraisHorsForfait = new LigneFraisHorsForfait();
            $ligneFraisHorsForfait->setFicheFrais($fichedumois);
            $ligneFraisHorsForfait->setLibelle($data['libelle']);
            $ligneFraisHorsForfait->setDate($data['Date']);
            $ligneFraisHorsForfait->setMontant($data['montant']);
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();
        }


        // Rendre le formulaire de saisie des fiches de frais
        return $this->render('fiche_frais/saisir.html.twig', [
            'datefiche' => $datefiche,
            'formF' => $formF->createView(),
            'formFH' => $formFH->createView()
        ]);
    }
}
