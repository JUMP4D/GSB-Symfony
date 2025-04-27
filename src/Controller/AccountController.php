<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $User = $this->getUser();

        $email = $User->getEmail();
        $nom = $User->getNom();
        $prenom = $User->getPrenom();
        $roles = $User->getRoles();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($userPasswordHasher->isPasswordValid($User, $data['currentPassword'])) {
                $User->setPassword($userPasswordHasher->hashPassword($User, $data['newPassword']));
                $entityManager->persist($User);
                $entityManager->flush();
                $this->addFlash('success', 'Le mot de passe a été modifié avec succès.');
            } else {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            }

        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom,
            'roles' => $roles,
            'form' => $form->createView()
        ]);
    }
}
