<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontofficeController extends AbstractController
{
     #[Route('/', name: 'app_frontoffice')]
    public function index(): Response
    {
        // Si l'utilisateur est connecté
        if ($this->getUser()) {
            $user = $this->getUser();
            
            // Si c'est un admin, rediriger vers le backoffice
            if ($user instanceof Admin || $user->getRole() === 'ADMIN') {
                return $this->redirectToRoute('app_backoffice');
            }
        }
        
        // Sinon, afficher le frontoffice normalement
        return $this->render('frontoffice/index.html.twig');
    }

      #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('frontoffice/index.html.twig');
    }
}
