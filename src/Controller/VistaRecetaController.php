<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Receta;
use App\Form\RecetaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Repository\RecetaRepository;
use App\Repository\PasoRepository;


class VistaRecetaController extends AbstractController
{

    #[Route('/vista/receta', name: 'app_vista_receta', methods: ['GET'])]
    public function index(RecetaRepository $recetaRepository): Response
    {
        // Obtener el usuario logueado
        $usuario = $this->getUser();
    
        if (!$usuario) {
            throw $this->createAccessDeniedException('Debes estar autenticado para ver las recetas.');
        }
    
        // Obtener solo las recetas del usuario logueado y cargar los pasos asociados
        $recetas = $recetaRepository->createQueryBuilder('r')
            ->leftJoin('r.pasos', 'p') // Asume que "pasos" es la relación en la entidad Receta
            ->addSelect('p')
            ->where('r.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getResult();
    
        return $this->render('vista_receta/index.html.twig', [
            'recetas' => $recetas,
        ]);
    }
    

    #[Route('/vista/receta/new', name: 'app_vista_receta_new', methods: ['GET', 'POST'])]
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

            // Redirigir a la creacion de pasos
            return $this->redirectToRoute('app_vista_pasos_new');
        }

        // Renderizar el formulario
        return $this->renderForm('vista_receta/new.html.twig', [
            'recetum' => $recetum,
            'form' => $form,
        ]);
    }

    #[Route('vista/receta/{id}/edit', name: 'app_vista_receta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Receta $recetum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetaType::class, $recetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vista_receta', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vista_receta/edit.html.twig', [
            'recetum' => $recetum,
            'form' => $form,
        ]);
    }

    #[Route('vista/receta/{id}/show', name: 'app_vista_receta_show', methods: ['GET'])]
        public function show(Receta $recetum): Response
        {
            return $this->render('vista_receta/show.html.twig', [
                'recetum' => $recetum,
            ]);
        }

        #[Route('/{id}', name: 'app_vista_receta_delete', methods: ['POST'])]
        public function delete(Request $request, Receta $recetum, EntityManagerInterface $entityManager): Response
        {
            // Verificación del token CSRF
            if ($this->isCsrfTokenValid('delete'.$recetum->getId(), $request->request->get('_token'))) {
                // Eliminar la receta de la base de datos
                $entityManager->remove($recetum);
                $entityManager->flush();
            }
        
            // Redirigir a la página de recetas después de eliminar
            return $this->redirectToRoute('app_vista_receta', [], Response::HTTP_SEE_OTHER);
        }
        

}
