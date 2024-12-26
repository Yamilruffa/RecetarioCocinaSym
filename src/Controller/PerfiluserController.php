<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PerfiluserController extends AbstractController
{
    #[Route('/perfiluser', name: 'app_perfiluser')]
    public function index(): Response
    {
        return $this->render('perfiluser/index.html.twig', [
            'controller_name' => 'PerfiluserController',
        ]);
    }
}
