<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Communaute;
use App\Entity\PostReaction;
use App\Form\PostType;
use App\Repository\PostReactionRepository;
use App\Service\AiSummaryService;
use App\Service\TitleSuggestionService;
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
        TitleSuggestionService $titleSuggestion
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

            // Générer le résumé AI automatiquement
            $summary = $aiSummary->generateSummary($post->getContenu());
            if ($summary) {
                $post->setSummary($summary);
            }

            // Générer le titre AI automatiquement
            $titleResult = $titleSuggestion->suggestPostTitle($post->getContenu());
            if (isset($titleResult['title'])) {
                $post->setTitre($titleResult['title']);
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

    #[Route('/{id}', name: 'app_post_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('frontoffice/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $em,
        AiSummaryService $aiSummary,
        TitleSuggestionService $titleSuggestion
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

            // Régénérer le résumé AI si le contenu a changé
            $summary = $aiSummary->generateSummary($post->getContenu());
            if ($summary) {
                $post->setSummary($summary);
            }

            // Régénérer le titre AI si le contenu a changé
            $titleResult = $titleSuggestion->suggestPostTitle($post->getContenu());
            if (isset($titleResult['title'])) {
                $post->setTitre($titleResult['title']);
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

    #[Route('/{id}', name: 'app_post_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(
        Request $request,
        Post $post,
        EntityManagerInterface $em
    ): Response {
        if ($post->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres posts.');
            return $this->redirectToRoute('app_communaute_show', [
                'id' => $post->getCommunaute()->getId()
            ]);
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {

            $communauteId = $post->getCommunaute()->getId();

            $em->remove($post);
            $em->flush();

            return $this->redirectToRoute('app_communaute_show', [
                'id' => $communauteId
            ]);
        }

        throw $this->createAccessDeniedException();
    }

    #[Route('/{id}/react', name: 'app_post_react', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function react(
        Post $post,
        Request $request,
        EntityManagerInterface $em,
        PostReactionRepository $reactionRepo
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);
        $reactionType = $data['type'] ?? null;

        $validTypes = ['like', 'love', 'wow', 'haha', 'sad', 'angry'];
        if (!in_array($reactionType, $validTypes)) {
            return new JsonResponse(['error' => 'Type de réaction invalide'], 400);
        }

        $user = $this->getUser();
        $existingReaction = $reactionRepo->findUserReaction($post, $user);

        if ($existingReaction) {
            if ($existingReaction->getType() === $reactionType) {
                // Supprimer la réaction si c'est la même
                $em->remove($existingReaction);
                $em->flush();
                
                return new JsonResponse([
                    'success' => true,
                    'action' => 'removed',
                    'counts' => $reactionRepo->countByType($post)
                ]);
            } else {
                // Changer le type de réaction
                $existingReaction->setType($reactionType);
            }
        } else {
            // Créer une nouvelle réaction
            $reaction = new PostReaction();
            $reaction->setPost($post);
            $reaction->setUser($user);
            $reaction->setType($reactionType);
            $em->persist($reaction);
        }

        $em->flush();

        return new JsonResponse([
            'success' => true,
            'action' => 'added',
            'type' => $reactionType,
            'counts' => $reactionRepo->countByType($post)
        ]);
    }
}
