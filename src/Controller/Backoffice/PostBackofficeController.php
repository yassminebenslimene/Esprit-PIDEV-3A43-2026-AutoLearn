<?php

namespace App\Controller\Backoffice;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\CommunauteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/backoffice/posts')]
#[IsGranted('ROLE_ADMIN')]
final class PostBackofficeController extends AbstractController
{
    #[Route('', name: 'backoffice_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        $communauteId = $request->query->get('communaute');
        
        $qb = $postRepository->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.communaute', 'c');
        
        if ($search) {
            $qb->where('p.contenu LIKE :search')
               ->orWhere('u.nom LIKE :search')
               ->orWhere('u.prenom LIKE :search')
               ->orWhere('c.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($communauteId) {
            $qb->andWhere('c.id = :communauteId')
               ->setParameter('communauteId', $communauteId);
        }
        
        $posts = $qb->orderBy('p.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult();

        return $this->render('backoffice/post/index.html.twig', [
            'posts' => $posts,
            'search' => $search,
            'communauteId' => $communauteId,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_post_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('backoffice/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_post_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post supprimé avec succès!');
        }

        return $this->redirectToRoute('backoffice_post_index');
    }
}
