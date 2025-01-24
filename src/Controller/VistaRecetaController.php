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

    

}
