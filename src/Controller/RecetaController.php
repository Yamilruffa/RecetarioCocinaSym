<?php

namespace App\Controller;

use App\Entity\Receta;
use App\Form\RecetaType;
use App\Repository\RecetaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/receta')]
class RecetaController extends AbstractController
{
    #[Route('/', name: 'app_receta_index', methods: ['GET'])]
    public function index(RecetaRepository $recetaRepository): Response
    {
        return $this->render('receta/index.html.twig', [
            'recetas' => $recetaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_receta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recetum = new Receta();
        $form = $this->createForm(RecetaType::class, $recetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recetum);
            $entityManager->flush();

            return $this->redirectToRoute('app_receta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('receta/new.html.twig', [
            'recetum' => $recetum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_receta_show', methods: ['GET'])]
    public function show(Receta $recetum): Response
    {
        return $this->render('receta/show.html.twig', [
            'recetum' => $recetum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_receta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Receta $recetum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetaType::class, $recetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_receta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('receta/edit.html.twig', [
            'recetum' => $recetum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_receta_delete', methods: ['POST'])]
    public function delete(Request $request, Receta $recetum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recetum->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recetum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_receta_index', [], Response::HTTP_SEE_OTHER);
    }
}
