<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;
    private Security $security;
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;

    public function __construct(
        RouterInterface $router, 
        Security $security, 
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ) {
        $this->router = $router;
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();

        // Check if user is suspended
        if ($user instanceof User && $user->getIsSuspended()) {
            // Logout the user immediately
            $this->security->logout(false);
            
            // Add flash message with suspension reason
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add(
                'error',
                'Votre compte a été suspendu. Raison: ' . $user->getSuspensionReason()
            );
            
            // Redirect back to login
            return new RedirectResponse($this->router->generate('backoffice_login'));
        }

        // Update last login time
        if ($user instanceof User) {
            $user->setLastLoginAt(new \DateTime());
            $this->entityManager->flush();
        }

        // Redirect Admin to backoffice
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return new RedirectResponse($this->router->generate('app_backoffice'));
        }

        // Redirect Étudiant to frontoffice 
        return new RedirectResponse($this->router->generate('app_frontoffice')); 
    }
}
