<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChallengeController extends AbstractController
{
     #[Route('/challenges', name: 'app_challenges')]  //  Route pour les étudiants
    public function index(ChallengeRepository $challengeRepository): Response
    {
        $challenges = $challengeRepository->findAll();

        return $this->render('frontoffice/challenges.html.twig', [
            'challenges' => $challenges
        ]);
    }

    #[Route('/challenge/{id}', name: 'app_challenge_show')]
public function show(int $id, ChallengeRepository $challengeRepository): Response
{
    $challenge = $challengeRepository->find($id);
    
    if (!$challenge) {
        throw $this->createNotFoundException('Challenge non trouvé');
    }
    
    return $this->render('frontoffice/challenge_show.html.twig', [
        'challenge' => $challenge
    ]);
}
}