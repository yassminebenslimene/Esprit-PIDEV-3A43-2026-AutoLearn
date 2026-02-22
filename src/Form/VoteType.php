<?php
// src/Form/VoteType.php

namespace App\Form;

use App\Entity\Vote;
use Sbyaute\StarRatingBundle\Form\StarRatingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valeur', StarRatingType::class, [
                'label' => 'Votre note',
                'stars' => 5, // Nombre d'étoiles (défaut: 5)
                'rating_type' => 'stars', // Type de notation
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'star-rating-widget'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vote::class,
        ]);
    }
}