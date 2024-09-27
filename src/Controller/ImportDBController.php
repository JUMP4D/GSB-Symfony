<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportDBController extends AbstractController
{
    #[Route('/importdb', name: 'app_import_d_b')]
    public function index(): Response
    {
        return $this->render('import_db/index.html.twig', [
            'controller_name' => 'ImportDBController',
        ]);
    }
}
