<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//
use App\Repository\RecetaRepository;
use App\Entity\Paso;
use App\Form\PasoType;
use App\Repository\PasoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class VistaPasosController extends AbstractController
{
    //#[Route('/vista/pasos', name: 'app_vista_pasos')]
    //public function index(): Response
    //{
    //    return $this->render('vista_pasos/index.html.twig', [
    //        'controller_name' => 'VistaPasosController',
    //    ]);
    //}

    #[Route('/vista/pasos', name: 'app_vista_pasos', methods: ['GET'])]
    public function index(PasoRepository $pasoRepository): Response
    {
        return $this->render('vista_pasos/index.html.twig', [
            'pasos' => $pasoRepository->findAll(),
        ]);
    }

    #[Route('app_vista_pasos_new', name: 'app_vista_pasos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paso = new Paso();
        $form = $this->createForm(PasoType::class, $paso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paso);
            $entityManager->flush();

            return $this->redirectToRoute('app_vista_pasos', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vista_pasos/new.html.twig', [
            'paso' => $paso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vista_paso_show', methods: ['GET'])]
    public function show(Paso $paso): Response
    {
        return $this->render('paso/show.html.twig', [
            'paso' => $paso,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vista_paso_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paso $paso, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasoType::class, $paso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_paso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paso/edit.html.twig', [
            'paso' => $paso,
            'form' => $form,
        ]);
    }
}
