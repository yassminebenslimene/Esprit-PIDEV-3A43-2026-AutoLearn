<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Texte du post',
                'required' => false,
                'attr' => [
                    'rows' => 8,
                    'placeholder' => 'Écrivez votre message ici...',
                    'class' => 'rich-editor'
                ],
            ])

            // ✅ IMAGE UPLOAD via Vich
            ->add('imageFileUpload', VichImageType::class, [
                'label' => 'Uploader une image',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide',
                    ])
                ],
            ])

            // ✅ VIDEO UPLOAD via Vich
            ->add('videoFileUpload', VichFileType::class, [
                'label' => 'Uploader une vidéo',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '50M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/webm',
                        ],
                        'mimeTypesMessage' => 'Vidéo invalide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}