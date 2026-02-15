<?php

namespace App\Form\GestionCours;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ChapitreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu')
            ->add('ordre')
            ->add('ressourceType', ChoiceType::class, [
                'label' => 'Type de ressource',
                'choices' => [
                    'Aucune ressource' => null,
                    'Lien (Google Drive, YouTube, etc.)' => 'lien',
                    'Fichier (PDF, PPTX, ZIP, Vidéo, etc.)' => 'fichier',
                ],
                'required' => false,
                'mapped' => true,
                'expanded' => false,
            ])
            ->add('ressources', TextType::class, [
                'label' => 'Lien de la ressource',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://drive.google.com/... ou https://youtube.com/...',
                ],
            ])
            ->add('ressourceFichierUpload', FileType::class, [
                'label' => 'Fichier de ressource',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/zip',
                            'application/x-zip-compressed',
                            'application/x-rar-compressed',
                            'video/mp4',
                            'video/mpeg',
                            'video/quicktime',
                            'video/x-msvideo',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier valide (PDF, PPTX, DOCX, ZIP, RAR, MP4, AVI, MOV)',
                    ])
                ],
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'titre',
                'placeholder' => 'Choisir un cours',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapitre::class,
        ]);
    }
}