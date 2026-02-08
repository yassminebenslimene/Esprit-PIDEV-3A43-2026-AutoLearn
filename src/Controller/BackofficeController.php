<?php
// src/Controller/BackofficeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackofficeController extends AbstractController
{
    #[Route('/backoffice', name: 'app_backoffice')]
    public function index(): Response
    {
        return $this->render('backoffice/index.html.twig');
    }

    #[Route('/backoffice/analytics', name: 'backoffice_analytics')]
    public function analytics(): Response
    {
        return $this->render('backoffice/analytics.html.twig');
    }

    #[Route('/backoffice/users', name: 'backoffice_users')]
    public function users(): Response
    {
        return $this->render('backoffice/users.html.twig');
    }

    #[Route('/backoffice/settings', name: 'backoffice_settings')]
    public function settings(): Response
    {
        return $this->render('backoffice/settings.html.twig');
    }

    #[Route('/backoffice/about-templatemo', name: 'backoffice_about_templatemo')]
    public function aboutTemplatemo(): Response
    {
        return $this->render('backoffice/about-templatemo.html.twig');
    }

    
}