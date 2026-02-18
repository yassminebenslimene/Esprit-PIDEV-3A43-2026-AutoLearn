<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentaireController extends AbstractController
{
#[Route('/commentaire/new/{id}', name: 'app_commentaire_new', methods: ['POST'])]
public function new(
    Post $post,
    Request $request,
    EntityManagerInterface $em
): Response {
    $communaute = $post->getCommunaute();
    if (!$communaute || !$communaute->canPost($this->getUser())) {
        throw $this->createAccessDeniedException('Vous devez être le créateur ou un membre invité pour commenter dans cette communauté.');
    }

    $data = $request->request->all('commentaire');

    if (!isset($data['contenu']) || trim($data['contenu']) === '') {
        return $this->redirectToRoute('app_communaute_show', [
            'id' => $communaute->getId()
        ]);
    }

    

    $commentaire = new Commentaire();
    $commentaire->setPost($post);
    $commentaire->setContenu($data['contenu']);
    if ($this->getUser()) {
        $commentaire->setUser($this->getUser());
    }

    $em->persist($commentaire);
    $em->flush();

    return $this->redirectToRoute('app_communaute_show', [
        'id' => $post->getCommunaute()->getId()
    ]);
}

#[Route('/commentaire/{id}/edit', name: 'app_commentaire_edit')]
public function edit(
    Commentaire $commentaire,
    Request $request,
    EntityManagerInterface $em
): Response {
    if ($commentaire->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez modifier que vos propres commentaires.');
    }
    if ($request->isMethod('POST')) {
        $data = $request->request->all('commentaire');

        if (isset($data['contenu']) && trim($data['contenu']) !== '') {
            $commentaire->setContenu($data['contenu']);
            $em->flush();
        }

        return $this->redirectToRoute('app_communaute_show', [
            'id' => $commentaire->getPost()->getCommunaute()->getId()
        ]);
    }

    return $this->render('frontoffice/commentaire/edit.html.twig', [
        'commentaire' => $commentaire
    ]);
}

#[Route('/commentaire/{id}/delete', name: 'app_commentaire_delete', methods: ['POST'])]
public function delete(
    Commentaire $commentaire,
    Request $request,
    EntityManagerInterface $em
): Response {
    if ($commentaire->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres commentaires.');
    }
    if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
        $communauteId = $commentaire->getPost()->getCommunaute()->getId();
        $em->remove($commentaire);
        $em->flush();

        return $this->redirectToRoute('app_communaute_show', [
            'id' => $communauteId
        ]);
    }

    throw $this->createAccessDeniedException();
}



}
