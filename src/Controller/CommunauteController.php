<?php

namespace App\Controller;

use App\Entity\Communaute;
use App\Form\CommunauteType;
use App\Repository\CommunauteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/communaute')]
final class CommunauteController extends AbstractController
{
    #[Route(name: 'app_communaute_index', methods: ['GET'])]
    public function index(CommunauteRepository $communauteRepository): Response
    {
        return $this->render('frontoffice/communaute/index.html.twig', [
            'communautes' => $communauteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_communaute_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $communaute = new Communaute();
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($communaute);
            $entityManager->flush();

            return $this->redirectToRoute('app_communaute_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/frontoffice/communaute/new.html.twig', [
            'communaute' => $communaute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_communaute_show', methods: ['GET'])]
    public function show(Communaute $communaute): Response
    {
        return $this->render('frontoffice/communaute/show.html.twig', [
            'communaute' => $communaute,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_communaute_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Communaute $communaute, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_communaute_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/communaute/edit.html.twig', [
            'communaute' => $communaute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_communaute_delete', methods: ['POST'])]
    public function delete(Request $request, Communaute $communaute, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$communaute->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($communaute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_communaute_index', [], Response::HTTP_SEE_OTHER);
    }
}
