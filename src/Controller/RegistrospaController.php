<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


//registro
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// persona
use App\Entity\Persona;
use App\Form\PersonaType;
use App\Repository\PersonaRepository;




class RegistrospaController extends AbstractController
{
    #[Route('/registrospa', name: 'app_registrospa', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Paso 1: Crear y procesar el formulario de registro
        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            // Codificar la contraseña
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $registrationForm->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Paso 2: Redirigir a la misma ruta para continuar con la creación de Persona
            return $this->redirectToRoute('app_registrospa', ['userId' => $user->getId()]);
        }

        // Paso 3: Verificar si estamos creando una Persona
        $userId = $request->query->get('userId');
        if ($userId) {
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                throw $this->createNotFoundException('Usuario no encontrado.');
            }

            $persona = new Persona();
            $persona->setUsuario($user);

            $personaForm = $this->createForm(PersonaType::class, $persona);
            $personaForm->handleRequest($request);

            if ($personaForm->isSubmitted() && $personaForm->isValid()) {
                $entityManager->persist($persona);
                $entityManager->flush();

                return $this->redirectToRoute('app_home'); // Cambiar a la ruta que prefieras
            }

            return $this->render('registrospa/persona_new.html.twig', [
                'personaForm' => $personaForm->createView(),
            ]);
        }

        // Renderizar el formulario de registro inicial
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $registrationForm->createView(),
        ]);
    }
}