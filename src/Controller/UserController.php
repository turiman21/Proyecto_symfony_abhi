<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/admin/usuarios')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_usuario_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $usuarios = $userRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/nuevo', name: 'admin_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Usuario creado exitosamente.');
            return $this->redirectToRoute('admin_usuario_index');
        }

        return $this->render('admin/usuarios/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_usuario_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/usuarios/show.html.twig', [
            'usuario' => $user,
        ]);
    }

    #[Route('admin/usuario/{id}/editar', name: 'admin_usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Usuario actualizado exitosamente.');
            return $this->redirectToRoute('admin_usuario_index');
        }

        return $this->render('admin/usuarios/editar.html.twig', [
            'form' => $form->createView(),
            'usuario' => $user,
        ]);
    }

    #[Route('/{id}/eliminar', name: 'admin_usuario_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario eliminado exitosamente.');
        } else {
            $this->addFlash('danger', 'No se pudo eliminar el usuario.');
        }

        return $this->redirectToRoute('admin_usuario_index');
    }

    


}
