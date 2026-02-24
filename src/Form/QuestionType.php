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
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image (optionnel)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
                'help' => 'JPG, PNG, GIF (max 2MB)'
            ])
            ->add('audioFile', VichFileType::class, [
                'label' => 'Audio (optionnel)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_uri' => true,
                'asset_helper' => true,
                'help' => 'MP3, WAV, OGG (max 5MB)'
            ])
            ->add('videoFile', VichFileType::class, [
                'label' => 'Vidéo (optionnel)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_uri' => true,
                'asset_helper' => true,
                'help' => 'MP4, WEBM (max 20MB)'
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
