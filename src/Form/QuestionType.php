<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('texteQuestion', TextareaType::class, [
                'label' => 'Texte de la question',
                'attr' => [
                    'placeholder' => 'Entrez votre question',
                    'rows' => 4,
                    'maxlength' => 1000
                ],
                'required' => true,
                'help' => 'Entre 10 et 1000 caractères'
            ])
            ->add('point', IntegerType::class, [
                'label' => 'Points',
                'attr' => [
                    'placeholder' => 'Nombre de points',
                    'min' => 1,
                    'max' => 100
                ],
                'required' => true,
                'help' => 'Entre 1 et 100 points'
            ])
            ->add('quiz', EntityType::class, [
                'class' => Quiz::class,
                'choice_label' => 'titre',
                'label' => 'Quiz associé',
                'placeholder' => 'Sélectionnez un quiz',
                'required' => true,
                'help' => 'Choisissez le quiz auquel appartient cette question'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
