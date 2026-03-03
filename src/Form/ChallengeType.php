<?php
namespace App\Form;

use App\Entity\Challenge;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\ExerciceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du challenge',
                'attr' => ['placeholder' => 'Ex: Challenge PHP Avancé']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez le contenu et les objectifs du challenge...']
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'attr' => ['placeholder' => 'Ex: 30', 'min' => 1]
            ])
            ->add('niveau', ChoiceType::class, [
                'label' => 'Niveau de difficulté',
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermédiaire' => 'Intermédiaire',
                    'Avancé' => 'Avancé'
                ],
                'placeholder' => 'Sélectionnez un niveau',
                'attr' => ['class' => 'form-select']
            ])
            ->add('exercices', CollectionType::class, [
                'entry_type' => ExerciceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
        ]);
    }
}
