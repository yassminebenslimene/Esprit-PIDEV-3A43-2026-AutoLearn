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
                'label' => 'Membres de l\'équipe (4 à 6 étudiants)',
                'multiple' => true,
                'expanded' => true, // Checkboxes au lieu de select multiple
                'attr' => [
                    'class' => 'student-checkboxes'
                ],
                'help' => 'Sélectionnez entre 4 et 6 étudiants pour former votre équipe'
            ])
            // Pas de champ événement - sera défini automatiquement par le contrôleur
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
