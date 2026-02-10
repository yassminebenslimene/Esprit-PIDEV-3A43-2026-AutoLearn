<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Enum\StatutParticipation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participation')]
final class ParticipationController extends AbstractController
{
    #[Route(name: 'app_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): Response
    {
        return $this->render('participation/index.html.twig', [
            'participations' => $participationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Auto-déterminer le statut basé sur la capaciteMax de l'événement
            // Si c'est une nouvelle participation (statut par défaut EN_ATTENTE), 
            // vérifier si on peut l'accepter
            if ($participation->getStatut() === StatutParticipation::EN_ATTENTE) {
                $participation->setStatut($participation->determineStatut());
            } else if ($participation->getStatut() === StatutParticipation::ACCEPTEE) {
                // L'admin essaie de créer une participation directement acceptée
                // Vérifier si c'est possible, sinon refuser
                if (!$participation->canBeAccepted()) {
                    $this->addFlash('error', 'Impossible d\'accepter cette participation : capacité maximale atteinte pour cet événement.');
                    return $this->render('participation/new.html.twig', [
                        'participation' => $participation,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->persist($participation);
            $entityManager->flush();

            $this->addFlash('success', 'Participation créée avec succès. Statut: ' . $participation->getStatut()->value);
            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $originalStatus = $participation->getStatut();
        
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le statut change vers ACCEPTEE, vérifier que c'est possible
            if ($participation->getStatut() === StatutParticipation::ACCEPTEE && $originalStatus !== StatutParticipation::ACCEPTEE) {
                if (!$participation->canBeAccepted()) {
                    $participation->setStatut($originalStatus);
                    $this->addFlash('error', 'Impossible d\'accepter cette participation : capacité maximale atteinte pour cet événement.');
                    return $this->render('participation/edit.html.twig', [
                        'participation' => $participation,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Participation mise à jour. Nouveau statut: ' . $participation->getStatut()->value);
            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }
}
