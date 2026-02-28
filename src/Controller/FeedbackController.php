<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    #[Route('/participation/{id}', name: 'app_feedback_form', methods: ['GET'])]
    public function showFeedbackForm(Participation $participation): Response
    {
        // Vérifier que l'événement est terminé (date ET heure)
        $now = new \DateTime();
        if ($participation->getEvenement()->getDateFin() > $now) {
            $this->addFlash('error', 'Vous ne pouvez donner votre feedback qu\'après la fin de l\'événement.');
            return $this->redirectToRoute('app_events');
        }

        // Vérifier que l'utilisateur fait partie de l'équipe
        $currentUser = $this->getUser();
        $isInTeam = false;
        
        foreach ($participation->getEquipe()->getEtudiants() as $etudiant) {
            if ($etudiant->getId() === $currentUser->getId()) {
                $isInTeam = true;
                break;
            }
        }

        if (!$isInTeam) {
            $this->addFlash('error', 'Vous ne faites pas partie de cette équipe.');
            return $this->redirectToRoute('app_events');
        }

        // Vérifier si l'étudiant a déjà donné son feedback
        $existingFeedback = $participation->getFeedbackByEtudiant($currentUser->getId());

        return $this->render('frontoffice/feedback/form.html.twig', [
            'participation' => $participation,
            'evenement' => $participation->getEvenement(),
            'equipe' => $participation->getEquipe(),
            'existing_feedback' => $existingFeedback,
        ]);
    }

    #[Route('/submit/{id}', name: 'app_feedback_submit', methods: ['POST'])]
    public function submitFeedback(
        Participation $participation,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Vérifier que l'événement est terminé (date ET heure)
        $now = new \DateTime();
        if ($participation->getEvenement()->getDateFin() > $now) {
            return new JsonResponse([
                'success' => false,
                'message' => 'L\'événement n\'est pas encore terminé.'
            ], 400);
        }

        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);

        $currentUser = $this->getUser();
        
        // Valider les données
        if (!isset($data['rating_global']) || $data['rating_global'] < 1 || $data['rating_global'] > 5) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Le rating global est invalide.'
            ], 400);
        }

        // Ajouter le feedback
        $participation->addFeedback(
            etudiantId: $currentUser->getId(),
            etudiantName: $currentUser->getPrenom() . ' ' . $currentUser->getNom(),
            ratingGlobal: (int) $data['rating_global'],
            ratingCategories: [
                'organisation' => (int) ($data['rating_organisation'] ?? 3),
                'contenu' => (int) ($data['rating_contenu'] ?? 3),
                'lieu' => (int) ($data['rating_lieu'] ?? 3),
                'animation' => (int) ($data['rating_animation'] ?? 3),
            ],
            sentiment: $data['sentiment'] ?? 'neutre',
            emoji: $data['emoji'] ?? '😐',
            comment: $data['comment'] ?? null
        );

        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Merci pour votre feedback ! Votre avis nous aide à améliorer nos événements.'
        ]);
    }
}
