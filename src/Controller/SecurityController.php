<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;



class SecurityController extends AbstractController
{
    private $entityManager;

    // Inyectar el EntityManager a través del constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/auth', name: 'auth', methods: ['POST','OPTIONS'])]
    public function auth(Request $request, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        error_log(json_encode($data)); // Registro de datos en el archivo de errores

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }

    #[Route('/sign-in', name: 'app_sign_in')]
    public function app_sign_in(AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authChecker): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        if ($this->getUser()) {
            if ($authChecker->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard');
            } elseif ($authChecker->isGranted('ROLE_CONSOLE_TECH')) {
                return $this->redirectToRoute('tecnico_consolas_dashboard');
            } elseif ($authChecker->isGranted('ROLE_TELEPHONY_TECH')) {
                return $this->redirectToRoute('tecnico_telefonia_dashboard');
            } else {
                // Cambia 'app_home' por una ruta válida existente en tu proyecto
                return $this->redirectToRoute('tecnico_index');
            }
        }


        return $this->render('auth/sign-in.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }






    #[Route('/get-token', name: 'app_get_token', methods: ['GET'])]
    public function getTokenForUser(JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }




    #[Route('/sign-out', name: 'app_sign_out')]
    public function app_sign_out(): void
    {
        // Este método puede permanecer vacío; Symfony se encarga del cierre de sesión.
        throw new \LogicException('Este método puede dejarse vacío. La ruta de cierre de sesión es manejada por Symfony.');
    }


    #[Route('/refresh', name: 'token_refresh', methods: ['POST'])]
    public function refreshToken(JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        // Obtiene el usuario autenticado desde el token actual
        $user = $this->getUser();

        // Verifica si hay un usuario autenticado
        if (!$user instanceof UserInterface) {
            return new JsonResponse(['error' => 'Invalid token or user not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Genera un nuevo token para el usuario autenticado
        $newToken = $JWTManager->create($user);

        return new JsonResponse(['token' => $newToken]);
    }

    #[Route('/auth/mi-perfil', name: 'api_mi_perfil', methods: ['GET'])]
    public function miPerfil(): JsonResponse
    {
        // Obtener el usuario autenticado
        $user = $this->getUser();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Devolver los datos del usuario en formato JSON
        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            // Incluye más datos del usuario aquí si es necesario
        ]);
    }



}
