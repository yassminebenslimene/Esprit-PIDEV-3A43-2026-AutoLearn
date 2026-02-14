<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Form\ParticipationFrontType;
use App\Repository\ParticipationRepository;
use App\Repository\EquipeRepository;
use App\Repository\EvenementRepository;
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
    public function mesParticipations(ParticipationRepository $participationRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Récupérer TOUTES les participations des équipes où l'utilisateur est membre
        $allParticipations = $participationRepository->createQueryBuilder('p')
            ->join('p.equipe', 'e')
            ->join('e.etudiants', 'et')
            ->where('et.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
        
        // Supprimer les participations refusées (nettoyage automatique)
        $deletedCount = 0;
        foreach ($allParticipations as $participation) {
            if ($participation->getStatut()->value === 'Refusé') {
                $entityManager->remove($participation);
                $deletedCount++;
            }
        }
        if ($deletedCount > 0) {
            $entityManager->flush();
        }
        
        // Récupérer uniquement les participations ACCEPTÉES ou EN_ATTENTE
        $participations = $participationRepository->createQueryBuilder('p')
            ->join('p.equipe', 'e')
            ->join('e.etudiants', 'et')
            ->where('et.id = :userId')
            ->andWhere('p.statut != :refuse')
            ->setParameter('userId', $user->getId())
            ->setParameter('refuse', 'Refusé')
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
            // Mettre à jour le statut de l'événement avant validation
            $participation->getEvenement()->updateStatus();
            
            // Valider automatiquement la participation
            $result = $participation->validateParticipation();

            if ($result['accepted']) {
                // Seulement persister si acceptée
                $entityManager->persist($participation);
                $entityManager->flush();
                $this->addFlash('success', $result['message']);
            } else {
                // Ne pas créer la participation si refusée
                $this->addFlash('error', $result['message']);
            }
            
            return $this->redirectToRoute('app_mes_participations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }
    
    #[Route('/new-for-team/{equipeId}/event/{eventId}', name: 'app_participation_new_for_team', methods: ['GET', 'POST'])]
    public function newForTeam(
        int $equipeId,
        int $eventId,
        Request $request,
        EntityManagerInterface $entityManager,
        EquipeRepository $equipeRepository,
        EvenementRepository $evenementRepository
    ): Response
    {
        $equipe = $equipeRepository->find($equipeId);
        $evenement = $evenementRepository->find($eventId);
        
        if (!$equipe || !$evenement) {
            $this->addFlash('error', 'Team or event not found.');
            return $this->redirectToRoute('app_events');
        }
        
        // Créer automatiquement la participation
        $participation = new Participation();
        $participation->setEquipe($equipe);
        $participation->setEvenement($evenement);
        
        // Mettre à jour le statut de l'événement avant validation
        $evenement->updateStatus();
        
        // Valider la participation selon les règles
        $result = $participation->validateParticipation();
        
        if ($result['accepted']) {
            // Seulement persister si acceptée
            $entityManager->persist($participation);
            $entityManager->flush();
            $this->addFlash('success', $result['message']);
        } else {
            // Ne pas créer la participation si refusée
            $this->addFlash('error', $result['message']);
        }
        
        return $this->redirectToRoute('app_mes_participations', [], Response::HTTP_SEE_OTHER);
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
            $this->addFlash('error', 'You can only view your own participations.');
            return $this->redirectToRoute('app_mes_participations');
        }

        return $this->render('frontoffice/participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
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
            $this->addFlash('error', 'You can only edit your own participations.');
            return $this->redirectToRoute('app_mes_participations');
        }

        $form = $this->createForm(ParticipationFrontType::class, $participation, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le statut de l'événement avant validation
            $participation->getEvenement()->updateStatus();
            
            // Re-valider la participation après modification
            $result = $participation->validateParticipation();
            
            if ($result['accepted']) {
                // Sauvegarder si acceptée
                $entityManager->flush();
                $this->addFlash('success', $result['message']);
            } else {
                // Supprimer la participation si elle devient refusée
                $entityManager->remove($participation);
                $entityManager->flush();
                $this->addFlash('error', $result['message']);
            }
            
            return $this->redirectToRoute('app_mes_participations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
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
            $this->addFlash('error', 'You can only delete your own participations.');
            return $this->redirectToRoute('app_mes_participations');
        }
        
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
            
            $this->addFlash('success', 'Participation deleted successfully!');
        }

        return $this->redirectToRoute('app_mes_participations', [], Response::HTTP_SEE_OTHER);
    }
}
