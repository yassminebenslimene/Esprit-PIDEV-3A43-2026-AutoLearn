<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/equipe')]
final class EquipeController extends AbstractController
{
    #[Route('/', name: 'backoffice_equipes', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('backoffice/equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAllWithEvenement(),
        ]);
    }

    #[Route('/new', name: 'backoffice_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();

            $this->addFlash('success', 'Équipe créée avec succès');
            return $this->redirectToRoute('backoffice_equipes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('backoffice/equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Équipe modifiée avec succès');
            return $this->redirectToRoute('backoffice_equipes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_equipe_delete', methods: ['GET'])]
    public function delete(Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($equipe);
        $entityManager->flush();

        $this->addFlash('success', 'Équipe supprimée avec succès');
        return $this->redirectToRoute('backoffice_equipes', [], Response::HTTP_SEE_OTHER);
    }
}
