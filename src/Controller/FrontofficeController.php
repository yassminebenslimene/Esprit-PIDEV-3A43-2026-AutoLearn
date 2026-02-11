<?php

namespace App\Controller;
use App\Repository\ChallengeRepository;
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
}
