<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Repository\ChallengeRepository;

use App\Repository\EvenementRepository;
use App\Repository\EquipeRepository;
use App\Form\ChallengeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChallengeController extends AbstractController
{

    #[Route('/challenges', name: 'frontchallenge')]
    public function index(
        ChallengeRepository $challengeRepository,
        EvenementRepository $evenementRepository,
        EquipeRepository $equipeRepository,
        \App\Repository\Cours\CoursRepository $coursRepository
    ): Response{
        $challenges = $challengeRepository->findAll();
        $evenements = $evenementRepository->findAll();
        $equipes = $equipeRepository->findAll();
        $cours = $coursRepository->findAll();
        
        return $this->render('frontoffice/index.html.twig', [
            'cours' => $cours,
            'challenges' => $challenges,
            'evenements' => $evenements,
            'equipes' => $equipes,
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