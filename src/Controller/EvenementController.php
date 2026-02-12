<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/evenement')]
final class EvenementController extends AbstractController
{
    #[Route('/', name: 'backoffice_evenements', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('backoffice/evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'backoffice_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->updateStatus();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès');
            return $this->redirectToRoute('backoffice_evenements', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('backoffice/evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->updateStatus();
            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès');
            return $this->redirectToRoute('backoffice_evenements', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_evenement_delete', methods: ['GET'])]
    public function delete(Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // IMPORTANT: Ordre de suppression pour respecter les contraintes de clés étrangères
        
        // 1. D'abord supprimer toutes les participations (qui référencent les équipes)
        foreach ($evenement->getParticipations() as $participation) {
            $entityManager->remove($participation);
        }
        
        // 2. Flush pour appliquer la suppression des participations
        $entityManager->flush();
        
        // 3. Ensuite supprimer toutes les équipes (qui référencent l'événement)
        foreach ($evenement->getEquipes() as $equipe) {
            $entityManager->remove($equipe);
        }
        
        // 4. Flush pour appliquer la suppression des équipes
        $entityManager->flush();
        
        // 5. Enfin supprimer l'événement
        $entityManager->remove($evenement);
        $entityManager->flush();

        $this->addFlash('success', 'Événement, équipes et participations supprimés avec succès');
        return $this->redirectToRoute('backoffice_evenements', [], Response::HTTP_SEE_OTHER);
    }
}
