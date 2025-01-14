<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//
use App\Entity\Paso;
use App\Form\PasoType;
use App\Repository\PasoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class VistaPasosController extends AbstractController
{
    #[Route('/vista/pasos', name: 'app_vista_pasos')]
    public function index(): Response
    {
        return $this->render('vista_pasos/index.html.twig', [
            'controller_name' => 'VistaPasosController',
        ]);
    }

    #[Route('/new', name: 'app_Vista_Paso_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paso = new Paso();
        $form = $this->createForm(PasoType::class, $paso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paso);
            $entityManager->flush();

            return $this->redirectToRoute('app_Vista_pasos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Vista_Pasos/new.html.twig', [
            'paso' => $paso,
            'form' => $form,
        ]);
    }
}
