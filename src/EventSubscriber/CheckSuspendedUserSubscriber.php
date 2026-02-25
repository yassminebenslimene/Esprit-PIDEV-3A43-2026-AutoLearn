<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class CheckSuspendedUserSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Don't check on login/logout routes
        if (in_array($route, ['backoffice_login', 'backoffice_logout', 'app_logout', 'app_login'])) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Check if user is suspended
        if ($user->getIsSuspended()) {
            // Log out the user
            $this->security->logout(false);

            // Add flash message
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add(
                'error',
                'Votre compte a été suspendu. Raison: ' . $user->getSuspensionReason()
            );

            // Redirect to login
            $response = new RedirectResponse($this->urlGenerator->generate('backoffice_login'));
            $event->setResponse($response);
        }
    }
}
