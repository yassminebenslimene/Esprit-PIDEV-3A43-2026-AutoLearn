<?php

namespace App\Controller;

use App\Entity\Communaute;
use App\Form\CommunauteType;
use App\Repository\CommunauteRepository;
use App\Repository\UserRepository;
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
        // 🔒 Vérifier que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $communaute = new Communaute();
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Assigner automatiquement le propriétaire
            $communaute->setOwner($this->getUser());

            $entityManager->persist($communaute);
            $entityManager->flush();

            return $this->redirectToRoute('app_communaute_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/communaute/new.html.twig', [
            'communaute' => $communaute,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_communaute_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(Request $request, Communaute $communaute, ?UserRepository $userRepository = null, ?EntityManagerInterface $em = null): Response
    {
        // Gestion de l'invitation en POST sur la même URL
        if ($request->isMethod('POST') && $request->request->has('_invite_token')) {
            if ($this->isCsrfTokenValid('invite_' . $communaute->getId(), $request->request->get('_invite_token'))) {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
                if ($communaute->getOwner() && $communaute->getOwner()->getId() === $this->getUser()?->getId()) {
                    $email = trim((string) $request->request->get('email', ''));
                    if ($email !== '') {
                        $user = $userRepository->findOneBy(['email' => $email]);
                        if (!$user) {
                            $this->addFlash('error', 'Aucun compte trouvé avec cet email.');
                        } elseif ($user->getId() === $communaute->getOwner()?->getId()) {
                            $this->addFlash('error', 'Vous êtes déjà le propriétaire de cette communauté.');
                        } elseif (!$communaute->getMembers()->exists(fn($i, $m) => $m->getId() === $user->getId())) {
                            $communaute->addMember($user);
                            $em->flush();
                            $this->addFlash('success', $user->getPrenom() . ' ' . $user->getNom() . ' a été ajouté(e) à la communauté.');
                        } else {
                            $this->addFlash('error', 'Cette personne est déjà membre.');
                        }
                    } else {
                        $this->addFlash('error', 'Veuillez entrer un email.');
                    }
                } else {
                    $this->addFlash('error', 'Seul le créateur peut inviter des membres.');
                }
            }
            return $this->redirectToRoute('app_communaute_show', ['id' => $communaute->getId()]);
        }

        return $this->render('frontoffice/communaute/show.html.twig', [
            'communaute' => $communaute,
            'canPost' => $communaute->canPost($this->getUser()),
        ]);
    }

    /** Retirer un membre (réservé au propriétaire). */
    #[Route('/{id}/remove-member/{userId}', name: 'app_communaute_remove_member', requirements: ['id' => '\d+', 'userId' => '\d+'], methods: ['POST'], priority: 10)]
    public function removeMember(Request $request, Communaute $communaute, int $userId, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($communaute->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Seul le créateur peut retirer des membres.');
        }

        if (!$this->isCsrfTokenValid('remove_member_' . $userId, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_communaute_show', ['id' => $communaute->getId()]);
        }
        $user = $userRepository->find($userId);
        if ($user && $communaute->getMembers()->contains($user)) {
            $communaute->removeMember($user);
            $em->flush();
            $this->addFlash('success', $user->getPrenom() . ' ' . $user->getNom() . ' a été retiré(e) de la communauté.');
        }

        return $this->redirectToRoute('app_communaute_show', ['id' => $communaute->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_communaute_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'], priority: 10)]
    public function edit(Request $request, Communaute $communaute, EntityManagerInterface $entityManager): Response
    {
        // 🔒 Vérifier que seul le propriétaire peut modifier
        if ($communaute->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette communauté.');
        }

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

    #[Route('/{id}', name: 'app_communaute_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Communaute $communaute, EntityManagerInterface $entityManager): Response
    {
        // 🔒 Vérifier que seul le propriétaire peut supprimer
        if ($communaute->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette communauté.');
        }

        if ($this->isCsrfTokenValid('delete'.$communaute->getId(), $request->request->get('_token'))) {
            $entityManager->remove($communaute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_communaute_index', [], Response::HTTP_SEE_OTHER);
    }
}
