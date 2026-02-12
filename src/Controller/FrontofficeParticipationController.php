<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Form\ParticipationFrontType;
use App\Repository\ParticipationRepository;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/participation')]
#[IsGranted('ROLE_USER')]
class FrontofficeParticipationController extends AbstractController
{
    #[Route('/mes-participations', name: 'app_mes_participations', methods: ['GET'])]
    public function mesParticipations(ParticipationRepository $participationRepository): Response
    {
        $user = $this->getUser();
        
        // Récupérer les participations des équipes où l'utilisateur est membre
        $participations = $participationRepository->createQueryBuilder('p')
            ->join('p.equipe', 'e')
            ->join('e.etudiants', 'et')
            ->where('et.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
        
        return $this->render('frontoffice/participation/mes_participations.html.twig', [
            'participations' => $participations,
        ]);
    }

    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EquipeRepository $equipeRepository): Response
    {
        $participation = new Participation();
        
        // Pré-sélectionner l'équipe si passée en paramètre
        $equipeId = $request->query->get('equipe');
        if ($equipeId) {
            $equipe = $equipeRepository->find($equipeId);
            if ($equipe) {
                $participation->setEquipe($equipe);
                $participation->setEvenement($equipe->getEvenement());
            }
        }
        
        $form = $this->createForm(ParticipationFrontType::class, $participation, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Valider automatiquement la participation
            $participation->validateParticipation();
            
            $entityManager->persist($participation);
            $entityManager->flush();

            if ($participation->getStatut()->value === 'ACCEPTE') {
                $this->addFlash('success', 'Participation created and accepted successfully!');
            } else {
                $this->addFlash('warning', 'Participation created but refused. Check event capacity or student duplicates.');
            }
            
            return $this->redirectToRoute('app_mes_participations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        // Vérifier que l'utilisateur connecté est membre de l'équipe
        $user = $this->getUser();
        $isMember = false;
        foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
            if ($etudiant->getId() === $user->getId()) {
                $isMember = true;
                break;
            }
        }
        
        if (!$isMember) {
            $this->addFlash('error', 'Vous ne pouvez voir que vos propres participations.');
            return $this->redirectToRoute('app_mes_participations');
        }

        return $this->render('frontoffice/participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }
}
