<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Receta;
use App\Repository\RecetaRepository;

class PanelController extends AbstractController
{
    #[Route('/panel', name: 'app_panel')]
    public function index(UserRepository $userRepository, RecetaRepository $recetaRepository): Response
    {
        return $this->render('panel/index.html.twig', [
            'controller_name' => 'PanelController',
            'users' => $userRepository->findAll(),
            'recetas' => $recetaRepository->findAll(),
        ]);
    }

    // BORRAR USUARIO POR ID
    #[Route('/admin/delete-user/{id}', name: 'admin_delete_user', methods: ['POST', 'DELETE'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_user' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario eliminado correctamente.');
        } else {
            $this->addFlash('error', 'Token CSRF inválido.');
        }

        return $this->redirectToRoute('app_panel');
    }

    // CAMBIAR CONTRASEÑA DE USUARIO
    #[Route('/admin/change-password/{id}', name: 'admin_change_password', methods: ['GET', 'POST'])]
    public function changePassword(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $entityManager->flush();
                $this->addFlash('success', 'Contraseña cambiada correctamente.');
                return $this->redirectToRoute('app_panel');
            } else {
                $this->addFlash('error', 'La contraseña no puede estar vacía.');
            }
        }

        return $this->render('admin/change_password.html.twig', [
            'user' => $user
        ]);
    }

    // APROBAR RECETA
    #[Route('/admin/approve-recipe/{id}', name: 'admin_approve_recipe', methods: ['POST'])]
    public function approveRecipe(Request $request, Receta $receta, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve_recipe' . $receta->getId(), $request->request->get('_token'))) {
            $receta->setVisible('yes');
            $entityManager->flush();
            $this->addFlash('success', 'Receta habilitada correctamente.');
        } else {
            $this->addFlash('error', 'Token CSRF inválido.');
        }

        return $this->redirectToRoute('app_panel');
    }

    // BORRAR RECETA
    #[Route('/admin/delete-recipe/{id}', name: 'admin_delete_recipe', methods: ['POST', 'DELETE'])]
    public function deleteRecipe(Request $request, Receta $receta, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_recipe' . $receta->getId(), $request->request->get('_token'))) {
            $entityManager->remove($receta);
            $entityManager->flush();
            $this->addFlash('success', 'Receta eliminada correctamente.');
        } else {
            $this->addFlash('error', 'Token CSRF inválido.');
        }

        return $this->redirectToRoute('app_panel');
    }
}
