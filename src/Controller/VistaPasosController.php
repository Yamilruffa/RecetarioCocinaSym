<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\RecetaRepository;
use App\Entity\Receta;
use App\Entity\Paso;
use App\Form\PasoType;
use App\Repository\PasoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class VistaPasosController extends AbstractController
{

    #[Route('/vista/pasos', name: 'app_vista_pasos', methods: ['GET'])]
    public function index(PasoRepository $pasoRepository): Response
    {
        return $this->render('vista_pasos/index.html.twig', [
            'pasos' => $pasoRepository->findAll(),
        ]);
    }

    #[Route('/vista/receta/{id}/pasos/agregar', name: 'app_vista_pasos_new', methods: ['GET', 'POST'])]
public function new(int $id, Request $request, EntityManagerInterface $entityManager, RecetaRepository $recetaRepository, PasoRepository $pasoRepository): Response
{
    $receta = $recetaRepository->find($id);

    if (!$receta) {
        return new Response('Error: No se encontró la receta.', Response::HTTP_BAD_REQUEST);
    }

    $paso = new Paso();
    $paso->setReceta($receta);

    $form = $this->createForm(PasoType::class, $paso, ['receta_actual' => $receta]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($paso);
        $entityManager->flush();

        // 🔹 Detecta si el usuario presionó el botón "Finalizar"
        if ($request->request->get('action') === 'finalizar') {
            return $this->redirectToRoute('app_vista_receta', ['id' => $receta->getId()]);
        }

        // 🔹 Si el usuario presionó "Agregar otro paso", recargar la misma vista
        return $this->redirectToRoute('app_vista_pasos_new', ['id' => $receta->getId()]);
    }

    return $this->render('vista_pasos/new.html.twig', [
        'form' => $form->createView(),
        'pasos' => $pasoRepository->findBy(['receta' => $receta]),
        'receta' => $receta,
    ]);
}



    #[Route('/{id}', name: 'app_vista_paso_show', methods: ['GET'])]
    public function show(Paso $paso): Response
    {
        return $this->render('paso/show.html.twig', [
            'paso' => $paso,
        ]);
    }

    #[Route('/vista/pasos/edit/{id}', name: 'app_vista_pasos_edit', methods: ['GET', 'POST'])]
        public function editPaso(Request $request, Paso $paso, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(PasoType::class, $paso);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                return new Response('OK', Response::HTTP_OK);
            }

            return $this->render('vista_pasos/edit.html.twig', [
                'form' => $form->createView(),
                'paso' => $paso
            ]);
        }



    #[Route('/vista/pasos/eliminar/{id}', name: 'app_vista_pasos_delete', methods: ['POST'])]
        public function deletePaso(Paso $paso, EntityManagerInterface $entityManager): JsonResponse
        {
            try {
                $entityManager->remove($paso);
                $entityManager->flush();

                return new JsonResponse(['success' => true]); // ✅ Respuesta clara
            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'error' => 'Error al eliminar el paso'], 500);
            }
}


}
