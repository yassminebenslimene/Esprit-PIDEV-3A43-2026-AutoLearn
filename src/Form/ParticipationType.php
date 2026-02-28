<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Evenement;
use App\Entity\Participation;
use App\Enum\StatutParticipation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'label' => 'Équipe',
                'placeholder' => 'Sélectionnez une équipe'
            ])
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => 'titre',
                'label' => 'Événement',
                'placeholder' => 'Sélectionnez un événement'
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutParticipation::class,
                'label' => 'Statut',
                'choice_label' => function($choice) {
                    return $choice->value;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
