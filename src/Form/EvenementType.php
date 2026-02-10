<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Enum\TypeEvenement;
use App\Enum\StatutEvenement;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')

            ->add('type', EnumType::class, [
                'class' => TypeEvenement::class,
                'choice_label' => fn($choice) => $choice->name,
            ])

            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
            ])

            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
            ])

            ->add('lieu')

            ->add('capaciteMax')

            ->add('statut', EnumType::class, [
                'class' => StatutEvenement::class,
                'choice_label' => fn($choice) => $choice->name,
            ])

            ->add('isCanceled');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
