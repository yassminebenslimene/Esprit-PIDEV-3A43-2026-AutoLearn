<?php

namespace App\Controller\Backoffice;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/backoffice/commentaires')]
#[IsGranted('ROLE_ADMIN')]
final class CommentaireBackofficeController extends AbstractController
{
    #[Route('', name: 'backoffice_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        $postId = $request->query->get('post');
        
        $qb = $commentaireRepository->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->leftJoin('c.post', 'p');
        
        if ($search) {
            $qb->where('c.contenu LIKE :search')
               ->orWhere('u.nom LIKE :search')
               ->orWhere('u.prenom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($postId) {
            $qb->andWhere('p.id = :postId')
               ->setParameter('postId', $postId);
        }
        
        $commentaires = $qb->orderBy('c.createdAt', 'DESC')
                           ->getQuery()
                           ->getResult();

        return $this->render('backoffice/commentaire/index.html.twig', [
            'commentaires' => $commentaires,
            'search' => $search,
            'postId' => $postId,
        ]);
    }

    #[Route('/{id}', name: 'backoffice_commentaire_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('backoffice/commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/delete', name: 'backoffice_commentaire_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire supprimé avec succès!');
        }

        return $this->redirectToRoute('backoffice_commentaire_index');
    }
}
