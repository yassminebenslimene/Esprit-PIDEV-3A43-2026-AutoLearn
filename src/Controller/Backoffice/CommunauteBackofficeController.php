<?php

namespace App\Controller\Backoffice;

use App\Entity\Communaute;
use App\Form\CommunauteType;
use App\Repository\CommunauteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/backoffice/communautes')]
#[IsGranted('ROLE_ADMIN')]
final class CommunauteBackofficeController extends AbstractController
{
    #[Route('', name: 'backoffice_communaute_index', methods: ['GET'])]
    public function index(CommunauteRepository $communauteRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        
        if ($search) {
            $communautes = $communauteRepository->createQueryBuilder('c')
                ->leftJoin('c.owner', 'o')
                ->where('c.nom LIKE :search')
                ->orWhere('c.description LIKE :search')
                ->orWhere('o.nom LIKE :search')
                ->orWhere('o.prenom LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $communautes = $communauteRepository->findAll();
        }

        return $this->render('backoffice/communaute/index.html.twig', [
            'communautes' => $communautes,
            'search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_communaute_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Communaute $communaute): Response
    {
        return $this->render('backoffice/communaute/show.html.twig', [
            'communaute' => $communaute,
        ]);
    }

    #[Route('/{id}/edit', name: 'backoffice_communaute_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Communaute $communaute, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Communauté modifiée avec succès!');

            return $this->redirectToRoute('backoffice_communaute_index');
        }

        return $this->render('backoffice/communaute/edit.html.twig', [
            'communaute' => $communaute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_communaute_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Communaute $communaute, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$communaute->getId(), $request->request->get('_token'))) {
            $entityManager->remove($communaute);
            $entityManager->flush();
            $this->addFlash('success', 'Communauté supprimée avec succès!');
        }

        return $this->redirectToRoute('backoffice_communaute_index');
    }
}
