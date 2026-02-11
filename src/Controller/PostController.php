<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Communaute;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        Communaute $communaute,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $post = new Post();
        $post->setCommunaute($communaute);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ===== IMAGE UPLOAD =====
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageName = uniqid('img_') . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_dir'),
                    $imageName
                );
                $post->setImageFile($imageName);
            }

            // ===== VIDEO UPLOAD =====
            $videoFile = $form->get('videoFile')->getData();
            if ($videoFile) {
                $videoName = uniqid('vid_') . '.' . $videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('uploads_dir'),
                    $videoName
                );
                $post->setVideoFile($videoName);
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

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ===== IMAGE UPLOAD =====
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageName = uniqid('img_') . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_dir'),
                    $imageName
                );
                $post->setImageFile($imageName);
            }

            // ===== VIDEO UPLOAD =====
            $videoFile = $form->get('videoFile')->getData();
            if ($videoFile) {
                $videoName = uniqid('vid_') . '.' . $videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('uploads_dir'),
                    $videoName
                );
                $post->setVideoFile($videoName);
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

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Post $post,
        EntityManagerInterface $em
    ): Response {
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
}
