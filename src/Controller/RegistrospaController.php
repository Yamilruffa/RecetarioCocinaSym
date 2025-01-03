<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Persona;
use App\Form\PersonaType;

class RegistrospaController extends AbstractController
{
    #[Route('/registrospa', name: 'app_registrospa', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $registrationForm->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_registrospa_new', ['userId' => $user->getId()]);
        }

        return $this->render('registrospa/index.html.twig', [
            'registrationForm' => $registrationForm->createView(),
        ]);
    }

    #[Route('/registrospa/new', name: 'app_registrospa_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $userId = $request->query->get('userId');
        if (!$userId) {
            throw $this->createNotFoundException('Usuario no encontrado.');
        }

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

            return $this->redirectToRoute('app_home'); // Cambiar a la ruta de tu página principal
        }

        return $this->render('registrospa/new.html.twig', [
            'personaForm' => $personaForm->createView(),
        ]);
    }
}
