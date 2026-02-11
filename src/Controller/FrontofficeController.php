<?php

namespace App\Controller;

use App\Repository\ChapitreRepository;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontOfficeController extends AbstractController
{
    #[Route('/', name: 'app_frontoffice')]
    public function index(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll();

        return $this->render('frontoffice/index.html.twig', [
            'cours' => $cours,
        ]);
    }


}
