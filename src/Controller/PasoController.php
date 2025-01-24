<?php

namespace App\Controller;

use App\Entity\Paso;
use App\Form\PasoType;
use App\Repository\PasoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/paso')]
class PasoController extends AbstractController
{
    #[Route('/', name: 'app_paso_index', methods: ['GET'])]
    public function index(PasoRepository $pasoRepository): Response
    {
        return $this->render('paso/index.html.twig', [
            'pasos' => $pasoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_paso_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paso = new Paso();
        $form = $this->createForm(PasoType::class, $paso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paso);
            $entityManager->flush();

            return $this->redirectToRoute('app_paso_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paso/new.html.twig', [
            'paso' => $paso,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paso_show', methods: ['GET'])]
    public function show(Paso $paso): Response
    {
        return $this->render('paso/show.html.twig', [
            'paso' => $paso,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paso_edit', methods: ['GET', 'POST'])]
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
