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

//////////
use Doctrine\DBAL\Connection;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;




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




    #[Route('vista/receta/{id}/show', name: 'app_vista_receta_show', methods: ['GET', 'POST'])]
    public function show(Receta $recetum, Request $request, Connection $connection): Response
    {
        $user = $this->getUser();
        $isFavorite = false;

        if ($user) {
            $userId = $connection->fetchOne('SELECT id FROM `user` WHERE email = ?', [$user->getUserIdentifier()]);
            $isFavorite = (bool) $connection->fetchOne(
                'SELECT 1 FROM user_receta WHERE user_id = ? AND receta_id = ?',
                [$userId, $recetum->getId()]
            );
        }

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            if (!$user) {
                return new JsonResponse(['success' => false, 'error' => 'Usuario no autenticado'], 403);
            }

            $recipeId = $recetum->getId();

            if ($isFavorite) {
                $connection->executeStatement('DELETE FROM user_receta WHERE user_id = ? AND receta_id = ?', [$userId, $recipeId]);
                return new JsonResponse(['success' => true, 'action' => 'removed']);
            } else {
                $connection->executeStatement('INSERT INTO user_receta (user_id, receta_id) VALUES (?, ?)', [$userId, $recipeId]);
                return new JsonResponse(['success' => true, 'action' => 'added']);
            }
        }

        return $this->render('vista_receta/show.html.twig', [
            'recetum' => $recetum,
            'isFavorite' => $isFavorite
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

    #[Route('/vista/receta/favoritos/toggle/{id}', name: 'app_toggle_favorito', methods: ['POST'])]
    public function toggleFavorito(int $id, Connection $connection): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Usuario no autenticado'], 403);
        }

        $userId = $connection->fetchOne('SELECT id FROM `user` WHERE email = ?', [$user->getUserIdentifier()]);

        if (!$userId) {
            return new JsonResponse(['success' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        // Verificar si la receta ya está en favoritos
        $isFavorite = $connection->fetchOne(
            'SELECT 1 FROM user_receta WHERE user_id = ? AND receta_id = ?',
            [$userId, $id]
        );

        if ($isFavorite) {
            $connection->executeStatement('DELETE FROM user_receta WHERE user_id = ? AND receta_id = ?', [$userId, $id]);
            return new JsonResponse(['success' => true, 'action' => 'removed']);
        } else {
            $connection->executeStatement('INSERT INTO user_receta (user_id, receta_id) VALUES (?, ?)', [$userId, $id]);
            return new JsonResponse(['success' => true, 'action' => 'added']);
        }
    }

    #[Route('/recetas/favoritas', name: 'app_recetas_favoritas')]
    public function recetasFavoritas(Connection $connection): Response
    {
        // Obtener el usuario autenticado
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Debe estar autenticado para ver sus recetas favoritas.');
        }

        // Consulta SQL para obtener las recetas favoritas del usuario autenticado
        $sql = "
            SELECT r.*
            FROM receta r
            INNER JOIN user_receta ur ON r.id = ur.receta_id
            INNER JOIN user u ON ur.user_id = u.id
            WHERE u.email = :email
        ";

        $favoritas = $connection->fetchAllAssociative($sql, ['email' => $user->getUserIdentifier()]);

        return $this->render('vista_receta/favorita.html.twig', [
            'recetas' => $favoritas,
        ]);
    }






}
