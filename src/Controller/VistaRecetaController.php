<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Receta;
use App\Form\RecetaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class VistaRecetaController extends AbstractController
{
    #[Route('/vista/receta', name: 'app_vista_receta')]
    public function index(): Response
    {
        return $this->render('vista_receta/index.html.twig', [
            'controller_name' => 'VistaRecetaController',
        ]);
    }

    #[Route('/new', name: 'app_vista_receta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crear una nueva receta
        $recetum = new Receta();

        // Obtener el usuario logueado
        $usuario = $this->getUser();

        // Asignar el usuario logueado al campo oculto 'usuario'
        $recetum->setUsuario($usuario);

        // Crear el formulario
        $form = $this->createForm(RecetaType::class, $recetum);
        $form->handleRequest($request);

        // Procesar el formulario
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistir la receta en la base de datos
            $entityManager->persist($recetum);
            $entityManager->flush();

            // Redirigir a la lista de recetas después de guardar
            return $this->redirectToRoute('app_vista_receta_index', [], Response::HTTP_SEE_OTHER);
        }

        // Renderizar el formulario
        return $this->renderForm('vista_receta/new.html.twig', [
            'recetum' => $recetum,
            'form' => $form,
        ]);
    }
}
