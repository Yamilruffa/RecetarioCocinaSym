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
use App\Entity\Paso;
use App\Form\PasoType;
use App\Repository\PasoRepository;

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
            // Asegurarse de que el token CSRF sea válido
            if ($this->isCsrfTokenValid('delete_user' . $user->getId(), $request->request->get('_token'))) {

                // Eliminar la persona asociada al usuario
                $persona = $user->getPersona(); // Asumiendo que la relación entre User y Persona es OneToOne
                if ($persona) {
                    $entityManager->remove($persona);
                }

                // Eliminar al usuario
                $entityManager->remove($user);
                $entityManager->flush();

                $this->addFlash('success', 'Usuario y persona eliminados correctamente.');
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

    // MOSTRAR RECETA
    #[Route('/admin/approve-recipe/{id}', name: 'admin_approve_recipe', methods: ['POST'])]
    public function approveRecipe(Request $request, Receta $receta, EntityManagerInterface $entityManager): Response
    {
        // Verificar el token CSRF
        if (!$this->isCsrfTokenValid('approve_recipe' . $receta->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF inválido.');
            return $this->redirectToRoute('app_panel');
        }
    
        // Verificar si la receta está visible
        if ($receta->getVisible() !== 'no') {
            $this->addFlash('error', 'La receta  está visible.');
            return $this->redirectToRoute('app_panel');
        }
    
        // desOcultar la receta
        $receta->setVisible("si");
        $entityManager->flush();
    
        return $this->redirectToRoute('app_panel');
    }

    // OCULTAR RECETA
    #[Route('/admin/hide-recipe/{id}', name: 'admin_hide_recipe', methods: ['POST'])]
    public function hideRecipe(Request $request, Receta $receta, EntityManagerInterface $entityManager): Response
    {
        // Verificar el token CSRF
        if (!$this->isCsrfTokenValid('hide_recipe' . $receta->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF inválido.');
            return $this->redirectToRoute('app_panel');
        }
    
        // Verificar si la receta está visible
        if ($receta->getVisible() !== 'si') {
            $this->addFlash('error', 'La receta no está visible o no puede ser ocultada.');
            return $this->redirectToRoute('app_panel');
        }
    
        // Ocultar la receta
        $receta->setVisible("no"); // Cambiar a no (oculta)
        $entityManager->flush();
    
        $this->addFlash('success', 'Receta ocultada correctamente.');
        return $this->redirectToRoute('app_panel');
    }

    // BORRAR RECETA
    #[Route('/admin/remove/{id}_recipe', name: 'admin_delete_recipe', methods: ['POST'])]

    public function delete(Request $request, Receta $recetum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recetum->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recetum);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_panel', [], Response::HTTP_SEE_OTHER);
    }


    
    #[Route('/vista/admin/{id}/pasos/agregar_panel', name:'app_vista_pasos_new_panel', methods: ['GET', 'POST'])]
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
            return $this->redirectToRoute('app_panel', ['id' => $receta->getId()]);
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
}
