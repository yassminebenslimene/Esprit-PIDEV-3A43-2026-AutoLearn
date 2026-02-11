<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Etudiant;
use App\Entity\Evenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom de l\'équipe',
                'attr' => ['placeholder' => 'Ex: Les Champions']
            ])
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => 'titre',
                'label' => 'Événement',
                'placeholder' => 'Sélectionnez un événement'
            ])
            ->add('etudiants', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => function($etudiant) {
                    return $etudiant->getPrenom() . ' ' . $etudiant->getNom() . ' (' . $etudiant->getEmail() . ')';
                },
                'label' => 'Étudiants (4 à 6 membres)',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['size' => 10]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
