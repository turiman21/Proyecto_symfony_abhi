<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api/users', name: 'app_api',methods: ['POST'])]

    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setNombre($data['nombre']);
        $user->setApellido1($data['apellido1']);
        $user->setApellido2($data['apellido2']);
        $user->setDirecccion($data['direcccion']);

        // Cifrar la contraseña
        $password = $passwordEncoder->encodePassword($user, $data['password']);
        $user->setPassword($password);

        $user->setRoles($data['roles'] ?? ['ROLE_USER']);
        $user->setVerified(true);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User created!'], JsonResponse::HTTP_CREATED);
    }


    
}
