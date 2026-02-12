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
            ->add('evenement', EntityType::class, [
                'class' => Evenement::class,
                'choice_label' => function(Evenement $evenement) {
                    return $evenement->getTitre() . ' - ' . $evenement->getLieu() . ' (' . $evenement->getDateDebut()->format('d/m/Y') . ')';
                },
                'label' => 'Événement',
                'placeholder' => 'Sélectionnez un événement',
                'attr' => ['class' => 'form-control']
            ])
            ->add('etudiants', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => function(Etudiant $etudiant) {
                    return $etudiant->getPrenom() . ' ' . $etudiant->getNom() . ' - ' . $etudiant->getNiveau();
                },
                'label' => 'Membres de l\'équipe (4 à 6 étudiants)',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control select2-multiple',
                    'size' => '8',
                    'data-placeholder' => 'Recherchez et sélectionnez les étudiants...'
                ],
                'help' => 'Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs étudiants. Vous devez sélectionner entre 4 et 6 étudiants.'
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
