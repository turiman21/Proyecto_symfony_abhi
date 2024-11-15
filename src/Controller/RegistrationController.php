<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registerbien', name: 'app_register_bien')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Codificar la contraseña
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Asignar roles
            $rolesSeleccionados = $form->get('roles')->getData();
            $user->setRoles($rolesSeleccionados);

            // Guardar el objeto User
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirigir según el rol asignado
            if (in_array('ROLE_ADMIN', $rolesSeleccionados)) {
                return $this->redirectToRoute('admin_dashboard');
            } elseif (in_array('ROLE_CONSOLE_TECH', $rolesSeleccionados)) {
                return $this->redirectToRoute('ROLE_CONSOLE_TECH');
            } elseif (in_array('ROLE_TELEPHONY_TECH', $rolesSeleccionados)) {
                return $this->redirectToRoute('tecnico_telefonia_Tech');
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    // Endpoint de registro de usuario con JWT
    #[Route('/user/register', name: 'app_register', methods: ['POST'])]
    public function registro(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $JWTTokenManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Crear un nuevo usuario
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNombre($data['nombre']);
        $user->setApellido1($data['apellido1']);
        $user->setApellido2($data['apellido2']);
        $user->setDirecccion($data['direccion']);

        // Hashear la contraseña
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // Generar el token JWT
        $token = $JWTTokenManager->create($user);

        // Responder con el token JWT
        return new JsonResponse(['token' => $token], Response::HTTP_CREATED);
    }

}
