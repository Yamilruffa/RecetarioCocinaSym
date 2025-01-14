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
        // Crear una nueva instancia de la entidad Receta
        $recetum = new Receta();

        // Obtener el usuario logueado
        $usuario = $this->getUser();

        // Validar que el usuario esté logueado
        if (!$usuario) {
            throw $this->createAccessDeniedException('Debes estar autenticado para crear una receta.');
        }

        // Asignar el usuario logueado a la receta
        $recetum->setUsuario($usuario);

        // Crear el formulario
        $form = $this->createForm(RecetaType::class, $recetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Manejo de archivo (PNG)
            $file = $form->get('png')->getData();
            if ($file) {
                $filename = uniqid() . '.png';
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/public/RecetasIMG',
                    $filename
                );
                $recetum->setPng('/RecetasIMG/' . $filename);
            }

            // Guardar la receta en la base de datos
            $entityManager->persist($recetum);
            $entityManager->flush();

            // Agregar un mensaje de éxito
            $this->addFlash('success', '¡Receta creada con éxito!');

            // Redirigir a la creacion de pasos
            return $this->redirectToRoute('app_Vista_Paso_new');
        }

        // Renderizar el formulario
        return $this->renderForm('vista_receta/new.html.twig', [
            'recetum' => $recetum,
            'form' => $form,
        ]);
    }
}
