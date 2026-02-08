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
        return $this->render('frontoffice/index.html.twig');
    }

      #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('frontoffice/index.html.twig');
    }
}
