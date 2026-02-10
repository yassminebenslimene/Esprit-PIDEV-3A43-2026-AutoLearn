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
            ->add('nom')
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => 'id',
            ]);
            // Optionnel : ajouter les étudiants si nécessaire
            // ->add('etudiants', EntityType::class, [
            //     'class' => Etudiant::class,
            //     'choice_label' => 'nom',
            //     'multiple' => true,
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
