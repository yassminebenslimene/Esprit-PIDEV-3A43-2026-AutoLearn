<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/participation')]
final class ParticipationController extends AbstractController
{
    #[Route('/', name: 'backoffice_participations', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): Response
    {
        return $this->render('backoffice/participation/index.html.twig', [
            'participations' => $participationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'backoffice_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participation->validateParticipation();
            $entityManager->persist($participation);
            $entityManager->flush();

            $this->addFlash('success', 'Participation créée avec succès');
            return $this->redirectToRoute('backoffice_participations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('backoffice/participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participation->validateParticipation();
            $entityManager->flush();

            $this->addFlash('success', 'Participation modifiée avec succès');
            return $this->redirectToRoute('backoffice_participations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_participation_delete', methods: ['GET'])]
    public function delete(Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($participation);
        $entityManager->flush();

        $this->addFlash('success', 'Participation supprimée avec succès');
        return $this->redirectToRoute('backoffice_participations', [], Response::HTTP_SEE_OTHER);
    }
}
