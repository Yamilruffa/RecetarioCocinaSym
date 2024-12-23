<?php

namespace App\Controller;

use App\Entity\Calificacion;
use App\Form\CalificacionType;
use App\Repository\CalificacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calificacion')]
class CalificacionController extends AbstractController
{
    #[Route('/', name: 'app_calificacion_index', methods: ['GET'])]
    public function index(CalificacionRepository $calificacionRepository): Response
    {
        return $this->render('calificacion/index.html.twig', [
            'calificacions' => $calificacionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_calificacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $calificacion = new Calificacion();
        $form = $this->createForm(CalificacionType::class, $calificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($calificacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_calificacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calificacion/new.html.twig', [
            'calificacion' => $calificacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calificacion_show', methods: ['GET'])]
    public function show(Calificacion $calificacion): Response
    {
        return $this->render('calificacion/show.html.twig', [
            'calificacion' => $calificacion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calificacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calificacion $calificacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalificacionType::class, $calificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_calificacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calificacion/edit.html.twig', [
            'calificacion' => $calificacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calificacion_delete', methods: ['POST'])]
    public function delete(Request $request, Calificacion $calificacion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calificacion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calificacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_calificacion_index', [], Response::HTTP_SEE_OTHER);
    }
}
