<?php

namespace App\Form;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du quiz',
                'attr' => [
                    'placeholder' => 'Entrez le titre du quiz',
                    'maxlength' => 255
                ],
                'required' => true,
                'help' => 'Entre 3 et 255 caractères'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez le contenu du quiz',
                    'rows' => 5,
                    'maxlength' => 2000
                ],
                'required' => true,
                'help' => 'Entre 10 et 2000 caractères'
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Actif' => 'actif',
                    'Inactif' => 'inactif',
                    'Brouillon' => 'brouillon',
                    'Archivé' => 'archive'
                ],
                'required' => true,
                'help' => 'Définissez le statut du quiz'
            ])
            ->add('chapitre', EntityType::class, [
                'class' => Chapitre::class,
                'choice_label' => 'titre',
                'label' => 'Chapitre',
                'placeholder' => 'Sélectionnez un chapitre',
                'required' => false,
                'help' => 'Associez ce quiz à un chapitre spécifique'
            ])
            ->add('dureeMaxMinutes', IntegerType::class, [
                'label' => 'Durée maximale (minutes)',
                'attr' => [
                    'placeholder' => 'Ex: 20',
                    'min' => 1
                ],
                'required' => false,
                'help' => 'Laissez vide pour un quiz sans limite de temps'
            ])
            ->add('seuilReussite', IntegerType::class, [
                'label' => 'Seuil de réussite (%)',
                'attr' => [
                    'placeholder' => 'Ex: 50',
                    'min' => 0,
                    'max' => 100
                ],
                'required' => false,
                'help' => 'Pourcentage minimum pour valider le quiz (défaut: 50%)'
            ])
            ->add('maxTentatives', IntegerType::class, [
                'label' => 'Nombre maximum de tentatives',
                'attr' => [
                    'placeholder' => 'Ex: 3',
                    'min' => 1
                ],
                'required' => false,
                'help' => 'Laissez vide pour un nombre illimité de tentatives'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
