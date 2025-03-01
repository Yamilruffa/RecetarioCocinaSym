<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//
use App\Entity\Receta;
use App\Form\RecetaType;
use App\Repository\RecetaRepository;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(RecetaRepository $recetaRepository): Response
    {
        $recetas = $recetaRepository->createQueryBuilder('r')
            ->where('r.visible = :visible')
            ->setParameter('visible', 'si')
            ->getQuery()
            ->getResult();
        
        return $this->render('home/index.html.twig', [
            'recetas' => $recetas,
        ]);
    }

    // Ruta para mostrar los detalles de una receta
    #[Route('/receta/{id}', name: 'vista_receta_show')]
    public function show(Receta $receta): Response
    {
        if ($receta->getVisible() !== 'si') {
            throw $this->createNotFoundException('La receta no está disponible.');
        }
        
        return $this->render('vista_receta/show.html.twig', [
            'recetum' => $receta,
        ]);
    }
}
