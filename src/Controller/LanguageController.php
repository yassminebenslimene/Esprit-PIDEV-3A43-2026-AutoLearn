<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LanguageController extends AbstractController
{
    #[Route('/change-language/{locale}', name: 'app_change_language')]
    public function changeLanguage(string $locale, Request $request): Response
    {
        // Vérifier que la locale est valide
        $allowedLocales = ['fr', 'en', 'ar', 'es'];
        
        if (!in_array($locale, $allowedLocales)) {
            throw $this->createNotFoundException('Langue non supportée');
        }

        // Stocker la locale dans la session
        $request->getSession()->set('_locale', $locale);

        // Rediriger vers la page précédente ou la page d'accueil
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_frontoffice');
    }
}
