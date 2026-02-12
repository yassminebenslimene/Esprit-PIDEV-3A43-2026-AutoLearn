<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Etudiant;
use App\Form\EquipeFrontType;
use App\Repository\EquipeRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/equipe')]
#[IsGranted('ROLE_USER')]
class FrontofficeEquipeController extends AbstractController
{
    #[Route('/mes-equipes', name: 'app_mes_equipes', methods: ['GET'])]
    public function mesEquipes(EquipeRepository $equipeRepository): Response
    {
        $user = $this->getUser();
        
        // Récupérer les équipes où l'utilisateur est membre
        $equipes = $equipeRepository->createQueryBuilder('e')
            ->join('e.etudiants', 'et')
            ->where('et.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
        
        return $this->render('frontoffice/equipe/mes_equipes.html.twig', [
            'equipes' => $equipes,
        ]);
    }

    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeFrontType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();

            $this->addFlash('success', 'Équipe créée avec succès !');
            return $this->redirectToRoute('app_mes_equipes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur connecté est membre de cette équipe
        $user = $this->getUser();
        $isMember = false;
        foreach ($equipe->getEtudiants() as $etudiant) {
            if ($etudiant->getId() === $user->getId()) {
                $isMember = true;
                break;
            }
        }
        
        if (!$isMember) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres équipes.');
            return $this->redirectToRoute('app_mes_equipes');
        }

        $form = $this->createForm(EquipeFrontType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Équipe modifiée avec succès !');
            return $this->redirectToRoute('app_mes_equipes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('frontoffice/equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }
}
