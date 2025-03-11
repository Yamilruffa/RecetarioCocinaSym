<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Receta;
use App\Form\RecetaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\RecetaRepository;

class VistaRecetaController extends AbstractController
{
    #[Route('/vista/receta', name: 'app_vista_receta', methods: ['GET'])]
    public function index(RecetaRepository $recetaRepository): Response
    {
        $usuario = $this->getUser();
    
        if (!$usuario) {
            throw $this->createAccessDeniedException('Debes estar autenticado para ver las recetas.');
        }
    
        $recetas = $recetaRepository->createQueryBuilder('r')
            ->leftJoin('r.pasos', 'p')
            ->addSelect('p')
            ->where('r.usuario = :usuario')
            ->andWhere('r.visible = :visible')
            ->setParameter('usuario', $usuario)
            ->setParameter('visible', 'si')
            ->getQuery()
            ->getResult();
    
        return $this->render('vista_receta/index.html.twig', [
            'recetas' => $recetas,
        ]);
    }

    #[Route('/vista/receta/new', name: 'app_vista_receta_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recetum = new Receta();
        $usuario = $this->getUser();

        if (!$usuario) {
            throw $this->createAccessDeniedException('Debes estar autenticado para crear una receta.');
        }

        $recetum->setUsuario($usuario);
        $form = $this->createForm(RecetaType::class, $recetum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('png')->getData();
            if ($file) {
                $filename = uniqid() . '.png';
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/public/RecetasIMG',
                    $filename
                );
                $recetum->setPng('/RecetasIMG/' . $filename);
            }
            

            $entityManager->persist($recetum);
            $entityManager->flush();

            // Guardar el ID de la receta en la sesión
            $request->getSession()->set('receta_id', $recetum->getId());


            return $this->redirectToRoute('app_vista_pasos_new',['id' => $recetum->getId()]);
        }

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
        if ($this->isCsrfTokenValid('delete'.$recetum->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recetum);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_vista_receta', [], Response::HTTP_SEE_OTHER);
    }


}
