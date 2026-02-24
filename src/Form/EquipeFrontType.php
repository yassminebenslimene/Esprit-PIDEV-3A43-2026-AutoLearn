<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Etudiant;
use App\Entity\Evenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUserId = $options['current_user_id'] ?? null;
        
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => ['placeholder' => 'Ex: Les Champions', 'class' => 'form-control']
            ])
            ->add('etudiants', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => function(Etudiant $etudiant) {
                    return $etudiant->getPrenom() . ' ' . $etudiant->getNom() . ' - ' . $etudiant->getNiveau();
                },
                'choice_attr' => function(Etudiant $etudiant) use ($currentUserId) {
                    // Pré-cocher et désactiver l'étudiant connecté
                    if ($currentUserId && $etudiant->getId() === $currentUserId) {
                        return ['checked' => 'checked', 'disabled' => 'disabled', 'data-current-user' => 'true'];
                    }
                    return [];
                },
                'label' => 'Membres de l\'équipe (4 à 6 étudiants)',
                'multiple' => true,
                'expanded' => true, // Checkboxes au lieu de select multiple
                'attr' => [
                    'class' => 'student-checkboxes'
                ],
                'help' => 'Vous êtes automatiquement membre de l\'équipe. Sélectionnez 3 à 5 autres étudiants.'
            ])
            // Pas de champ événement - sera défini automatiquement par le contrôleur
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
            'current_user_id' => null,
        ]);
    }
}
