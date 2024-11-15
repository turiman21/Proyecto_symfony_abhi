<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            $roles = $user->getRoles();

            // Redirige según el rol del usuario
            if (in_array('ROLE_ADMIN', $roles)) {
                $redirectUrl = $this->router->generate('admin_dashboard');
            } elseif (in_array('ROLE_CONSOLE_TECH', $roles)) {
                $redirectUrl = $this->router->generate('tecnico_consolas_dashboard');
            } elseif (in_array('ROLE_TELEPHONY_TECH', $roles)) {
                $redirectUrl = $this->router->generate('tecnico_telefonia_dashboard');
            } else {
                $redirectUrl = $this->router->generate('profile');
            }

            return new RedirectResponse($redirectUrl);
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }
}
