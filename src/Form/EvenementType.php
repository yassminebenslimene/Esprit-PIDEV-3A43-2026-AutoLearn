<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Ex: Hackathon 2026']
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez l\'événement...', 'rows' => 4]
            ])
            ->add('type', null, [
                'label' => 'Type d\'événement',
                'choice_label' => function($choice) {
                    return $choice->value;
                }
            ])
            ->add('dateDebut', null, [
                'label' => 'Date de début',
                'widget' => 'single_text'
            ])
            ->add('dateFin', null, [
                'label' => 'Date de fin',
                'widget' => 'single_text'
            ])
            ->add('nbMax', null, [
                'label' => 'Nombre maximum d\'équipes',
                'attr' => ['min' => 1, 'placeholder' => 'Ex: 10']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
