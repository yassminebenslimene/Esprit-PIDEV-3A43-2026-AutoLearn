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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/backoffice/participation')]
#[IsGranted('ROLE_ADMIN')]
final class ParticipationController extends AbstractController
{
    #[Route(name: 'app_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): Response
    {
        return $this->render('backoffice/participation/index.html.twig', [
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
            if ($participation->getStatut() === StatutParticipation::EN_ATTENTE) {
                $participation->setStatut($participation->determineStatut());
                if ($participation->getStatut() === StatutParticipation::REJETEE) {
                    $this->addFlash('warning', 'Participation créée mais REJETÉE : capacité maximale.');
                } else {
                    $this->addFlash('success', 'Participation créée et ACCEPTÉE.');
                }
            } else if ($participation->getStatut() === StatutParticipation::ACCEPTEE) {
                if (!$participation->canBeAccepted()) {
                    $this->addFlash('error', 'Capacité maximale atteinte.');
                        return $this->render('backoffice/participation/new.html.twig', [
                            'participation' => $participation,
                            'form' => $form->createView(),
                        ]);
                }
                $this->addFlash('success', 'Participation ACCEPTÉE.');
            } else if ($participation->getStatut() === StatutParticipation::REJETEE) {
                $this->addFlash('warning', 'Participation REJETÉE.');
            }

            try {
                $entityManager->persist($participation);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
                    return $this->render('backoffice/participation/new.html.twig', [
                        'participation' => $participation,
                        'form' => $form->createView(),
                    ]);
            }

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

            return $this->render('backoffice/participation/new.html.twig', [
                'participation' => $participation,
                'form' => $form->createView(),
            ]);
    }

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('backoffice/participation/show.html.twig', [
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
            if ($participation->getStatut() === StatutParticipation::ACCEPTEE && $originalStatus !== StatutParticipation::ACCEPTEE) {
                if (!$participation->canBeAccepted()) {
                    $participation->setStatut($originalStatus);
                    $this->addFlash('error', 'Capacité maximale atteinte.');
                        return $this->render('backoffice/participation/edit.html.twig', [
                            'participation' => $participation,
                            'form' => $form->createView(),
                        ]);
                }
            }

            try {
                $entityManager->flush();
                $this->addFlash('success', 'Participation mise à jour.');
                return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            }
        }

            return $this->render('backoffice/participation/edit.html.twig', [
                'participation' => $participation,
                'form' => $form->createView(),
            ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($participation);
                $entityManager->flush();
                $this->addFlash('success', 'Participation supprimée!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }
}
