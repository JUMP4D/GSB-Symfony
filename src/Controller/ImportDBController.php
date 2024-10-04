<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\User;
use App\Entity\Etat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

class ImportDBController extends AbstractController
{
    #[Route('/importdb', name: 'app_import_d_b')]
    public function index(EntityManagerInterface $entity, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // recuperer le chemin du fichier json
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';
        // lire le contenu du fichier json
        $jsonData = file_get_contents($jsonFile);
        // Convertir les donnée json en tableau php
        $data = json_decode($jsonData);
        foreach ($data as $userorigin){
            $user = new User();
            $user->setOldid($userorigin->id);
            $user->setLogin($userorigin->login);
            $user->setNom($userorigin->nom);
            $user->setPrenom($userorigin->prenom);
            $user->setAdresse($userorigin->adresse);
            $user->setCp($userorigin->cp);
            $user->setVille($userorigin->ville);
            $user->setDateEmbauche(new \DateTime($userorigin->dateEmbauche));
            $user->setPassword($userPasswordHasher->hashPassword($user, $userorigin->mdp));
            $user->setEmail($userorigin->nom . $userorigin->prenom . '@gmail.com');
            $entity->persist($user);
            $entity->flush();
        }
        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }

    #[Route('/importdbfichefrais', name: 'app_import_db_fiche')]
    public function fichefrais(EntityManagerInterface $entity): Response
    {
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/fichefrais.json';

        $jsonData = file_get_contents($jsonFile);

        $data = json_decode($jsonData);

        foreach ($data as $fichefraisorigin){
            $fichefrais = new FicheFrais();
            $fichefrais->setUser($entity->getRepository(User::class)->findOneBy(['oldid' => $fichefraisorigin->idVisiteur]));

            $mois = $fichefraisorigin->mois;
            $date = \DateTime::createFromFormat('Ym', $mois)->setDate((int)substr($mois, 0, 4), (int)substr($mois, 4, 2), 1);
            $fichefrais->setMois($date);

            $fichefrais->setNbJustificatifs($fichefraisorigin->nbJustificatifs);
            $fichefrais->setMontantValid($fichefraisorigin->montantValide);
            $fichefrais->setDateModif(new \DateTime($fichefraisorigin->dateModif));
            if ($fichefraisorigin->idEtat === "CL") {
                // Récupérer l'entité Etat avec l'id 1
                $etat = $entity->getRepository(Etat::class)->find(1);
            } elseif ($fichefraisorigin->idEtat === "CR") {
                // Récupérer l'entité Etat avec l'id 2
                $etat = $entity->getRepository(Etat::class)->find(2);
            } elseif ($fichefraisorigin->idEtat === "RB") {
                // Récupérer l'entité Etat avec l'id 3
                $etat = $entity->getRepository(Etat::class)->find(3);
            } elseif ($fichefraisorigin->idEtat === "RB") {
                // Récupérer l'entité Etat avec l'id 4
                $etat = $entity->getRepository(Etat::class)->find(4);
            }
            $fichefrais->setEtat($etat);
            $entity->persist($fichefrais);
            $entity->flush();
        }


        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }

    #[Route('/importdbetat', name: 'app_import_db_etat')]
    public function etat(EntityManagerInterface $entity): Response
    {
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/etat.json';

        $jsonData = file_get_contents($jsonFile);

        $data = json_decode($jsonData);

        foreach ($data as $etatorigin){
            $etat = new Etat();
            $etat->setLibelle($etatorigin->libelle);
            $entity->persist($etat);
            $entity->flush();
        }

        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }

    #[Route('/importdbfraisforfait', name: 'app_import_db_fraisforfait')]
    public function fraisforfait(EntityManagerInterface $entity): Response
    {
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/fraisforfait.json';

        $jsonData = file_get_contents($jsonFile);

        $data = json_decode($jsonData);

        foreach ($data as $fraisforfatorigin){
            $fraisforfait = new FraisForfait();
            $fraisforfait->setLibelle($fraisforfatorigin->libelle);
            $fraisforfait->setMontant($fraisforfatorigin->montant);
            $entity->persist($fraisforfait);
            $entity->flush();
        }


        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }

    #[Route('/importdbligneFraisForfait', name: 'app_import_db_ligneFraisForfait')]
    function lignefraisforfait(EntityManagerInterface $entity): Response
    {

        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/lignefraisforfait.json';

        $jsonData = file_get_contents($jsonFile);

        $data = json_decode($jsonData);

        foreach ($data as $lignefraisforfatorigin){
            $lignefraisforfatorigin->setUser($entity->getRepository(User::class)->findOneBy(['oldid' => $lignefraisforfatorigin->idVisiteur]));
            $lignefraisforfatorigin->setFicheFrais($entity->getRepository(FicheFrais::class)->findOneBy(['mois' => $lignefraisforfatorigin->mois]));
            if ($lignefraisforfatorigin->idEtat === "CL") {
                // Récupérer l'entité Etat avec l'id 1
                $etat = $entity->getRepository(FraisForfait::class)->find(1);
            } elseif ($lignefraisforfatorigin->idEtat === "CR") {
                // Récupérer l'entité Etat avec l'id 2
                $etat = $entity->getRepository(Etat::class)->find(2);
            } elseif ($lignefraisforfatorigin->idEtat === "RB") {
                // Récupérer l'entité Etat avec l'id 3
                $etat = $entity->getRepository(Etat::class)->find(3);
            } elseif ($lignefraisforfatorigin->idEtat === "RB") {
                // Récupérer l'entité Etat avec l'id 4
                $etat = $entity->getRepository(Etat::class)->find(4);
            }

        }



        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }
}

