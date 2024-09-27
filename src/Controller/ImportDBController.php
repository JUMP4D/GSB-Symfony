<?php

namespace App\Controller;

use App\Entity\User;
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
            $user->setEmail();
            $user->setPassword($userPasswordHasher->hashPassword($userorigin->mdp));
            $entity->persist($user);
            $entity->flush();
        }
        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }

}
