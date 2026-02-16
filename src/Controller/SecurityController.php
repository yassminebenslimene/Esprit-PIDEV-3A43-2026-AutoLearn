<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Etudiant;
use App\Entity\User;
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormError;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'backoffice_login')]
public function login(AuthenticationUtils $authUtils): Response
{
    if ($this->getUser()) {
        
        $user = $this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_backoffice');
        }
        return $this->redirectToRoute('app_frontoffice');
    }

    return $this->render('backoffice/cnx/login.html.twig', [
        'last_username' => $authUtils->getLastUsername(),
        'error' => $authUtils->getLastAuthenticationError()
    ]);
}

    #[Route('/logout', name: 'backoffice_logout')]
    public function logout(): void {}

    #[Route('/register', name: 'backoffice_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator
    ): Response {
        $dto = new UserCreateDTO();
        
        // Créer le formulaire avec validation HTML5 désactivée
        $form = $this->createForm(UserType::class, $dto, [
            'is_edit' => false,
            'action' => $this->generateUrl('backoffice_register'),
            'method' => 'POST',
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Déterminer les groupes de validation dynamiquement
            $validationGroups = ['Default'];
            
            // Toujours ajouter 'registration' pour l'inscription
            $validationGroups[] = 'registration';
            
            // Ajouter 'niveau_validation' si c'est un étudiant
            if ($dto->role === 'ETUDIANT') {
                $validationGroups[] = 'niveau_validation';
            }
            
            // Valider le DTO avec les groupes appropriés
            $errors = $validator->validate($dto, null, $validationGroups);
            
            // Vérifier l'unicité de l'email
            if (count($errors) === 0) {
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $dto->email]);
                if ($existingUser) {
                    // Ajouter l'erreur directement au champ email
                    $form->get('email')->addError(new FormError('Cet email est déjà utilisé'));
                }
            }
            
            // Si pas d'erreurs de validation (y compris l'unicité email)
            if (count($errors) === 0 && $form->isValid()) {
                // Créer l'utilisateur selon le rôle
                if ($dto->role === 'ADMIN') {
                    $user = new Admin();
                } else {
                    $user = new Etudiant();
                    // Le niveau est déjà validé par les contraintes
                    $user->setNiveau($dto->niveau);
                }

                $user->setNom($dto->nom);
                $user->setPrenom($dto->prenom);
                $user->setEmail($dto->email);
                $user->setRole($dto->role);
                $user->setPassword($hasher->hashPassword($user, $dto->password));

                // Validation de l'entité complète
                $entityErrors = $validator->validate($user);
                if (count($entityErrors) === 0) {
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
                    return $this->redirectToRoute('backoffice_login');
                } else {
                    // Ajouter les erreurs d'entité aux champs correspondants
                    foreach ($entityErrors as $error) {
                        $propertyPath = $error->getPropertyPath();
                        if ($form->has($propertyPath)) {
                            $form->get($propertyPath)->addError(new FormError($error->getMessage()));
                        } else {
                            // Si le champ n'existe pas dans le formulaire, ajouter comme erreur globale
                            $form->addError(new FormError($error->getMessage()));
                        }
                    }
                }
            } else {
                // Ajouter les erreurs de validation du DTO aux champs correspondants
                foreach ($errors as $error) {
                    $propertyPath = $error->getPropertyPath();
                    if ($form->has($propertyPath)) {
                        $form->get($propertyPath)->addError(new FormError($error->getMessage()));
                    } else {
                        // Si le champ n'existe pas dans le formulaire, ajouter comme erreur globale
                        $form->addError(new FormError($error->getMessage()));
                    }
                }
            }
        }

        return $this->render('backoffice/cnx/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}