<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Repository\ChallengeRepository;
<<<<<<< HEAD
=======
use App\Repository\EvenementRepository;
use App\Repository\EquipeRepository;
use App\Form\ChallengeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
>>>>>>> 401cf0655463466bc7ffa96bc5d9951a3d068425
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChallengeController extends AbstractController
{
<<<<<<< HEAD
     #[Route('/challenges', name: 'app_challenges')]  //  Route pour les étudiants
    public function index(ChallengeRepository $challengeRepository): Response
=======
    #[Route('/challenges', name: 'frontchallenge')]
    public function index(
        ChallengeRepository $challengeRepository,
        EvenementRepository $evenementRepository,
        EquipeRepository $equipeRepository
    ): Response
>>>>>>> 401cf0655463466bc7ffa96bc5d9951a3d068425
    {
        $challenges = $challengeRepository->findAll();
        $evenements = $evenementRepository->findAll();
        $equipes = $equipeRepository->findAll();

<<<<<<< HEAD
        return $this->render('frontoffice/challenges.html.twig', [
            'challenges' => $challenges
=======
        return $this->render('frontoffice/index.html.twig', [
            'challenges' => $challenges,
            'evenements' => $evenements,
            'equipes' => $equipes,
>>>>>>> 401cf0655463466bc7ffa96bc5d9951a3d068425
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