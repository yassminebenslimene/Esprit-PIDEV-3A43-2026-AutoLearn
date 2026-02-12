<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                    'rows' => 4,
                    'placeholder' => 'Écrivez votre message ici...'
                ],
            ])
             // ✅ IMAGE UPLOAD
            ->add('imageFile', FileType::class, [
                'label' => 'Uploader une image',
                'mapped' => false,      // ⚠️ TRÈS IMPORTANT
                'required' => false,
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

            // ✅ IMAGE PAR LIEN
            ->add('imageUrl', null, [
                'required' => false,
                'label' => 'Lien image',
            ])

            // VIDEO UPLOAD (PC)
            ->add('videoFile', FileType::class, [
                'label' => 'Uploader une vidéo',
                'mapped' => false,
                'required' => false,
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

            // ✅ VIDEO PAR LIEN
            ->add('videoUrl', null, [
                'required' => false,
                'label' => 'Lien vidéo (YouTube)',
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
