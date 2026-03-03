<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Communaute;
use App\Entity\PostReaction;
use App\Form\PostType;
use App\Repository\PostReactionRepository;
use App\Service\AiSummaryService;
use App\Service\TitleSuggestionService;
use App\Service\PostManager; // ✅ AJOUT
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        Communaute $communaute,
        Request $request,
        EntityManagerInterface $em,
        AiSummaryService $aiSummary,
        TitleSuggestionService $titleSuggestion,
        PostManager $postManager // ✅ INJECTION
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$communaute->canPost($this->getUser())) {
            throw $this->createAccessDeniedException(
                'Vous devez être le créateur ou un membre invité pour poster dans cette communauté.'
            );
        }

        $post = new Post();
        $post->setCommunaute($communaute);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setContenu($post->getContenu() ?? '');

            if ($this->getUser()) {
                $post->setUser($this->getUser());
            }

            // 🔥 VALIDATION MÉTIER
            try {
                $postManager->validate($post);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->render('frontoffice/post/new.html.twig', [
                    'form' => $form,
                    'communaute' => $communaute,
                ]);
            }

            // Résumé AI
            $summary = $aiSummary->generateSummary($post->getContenu());
            if ($summary) {
                $post->setSummary($summary);
            }

            // Titre AI (seulement si pas de titre fourni)
            if (empty($post->getTitre())) {
                $titleResult = $titleSuggestion->suggestPostTitle($post->getContenu());
                if (isset($titleResult['title'])) {
                    $post->setTitre($titleResult['title']);
                }
            }

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_communaute_show', [
                'id' => $communaute->getId()
            ]);
        }

        return $this->render('frontoffice/post/new.html.twig', [
            'form' => $form,
            'communaute' => $communaute,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $em,
        AiSummaryService $aiSummary,
        TitleSuggestionService $titleSuggestion,
        PostManager $postManager // ✅ INJECTION
    ): Response {
        if ($post->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres posts.');
            return $this->redirectToRoute('app_communaute_show', [
                'id' => $post->getCommunaute()->getId()
            ]);
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setContenu($post->getContenu() ?? '');

            // 🔥 VALIDATION MÉTIER
            try {
                $postManager->validate($post);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->render('frontoffice/post/edit.html.twig', [
                    'form' => $form->createView(),
                    'post' => $post,
                ]);
            }

            // Résumé AI
            $summary = $aiSummary->generateSummary($post->getContenu());
            if ($summary) {
                $post->setSummary($summary);
            }

            // Titre AI (seulement si pas de titre fourni)
            if (empty($post->getTitre())) {
                $titleResult = $titleSuggestion->suggestPostTitle($post->getContenu());
                if (isset($titleResult['title'])) {
                    $post->setTitre($titleResult['title']);
                }
            }

            $em->flush();

            return $this->redirectToRoute('app_communaute_show', [
                'id' => $post->getCommunaute()->getId()
            ]);
        }

        return $this->render('frontoffice/post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }
}