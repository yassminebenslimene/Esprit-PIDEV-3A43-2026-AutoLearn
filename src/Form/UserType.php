<?php

namespace App\Form;

use App\DTO\UserCreateDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
  // In UserType.php - buildForm method
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('nom', TextType::class, [
            'attr' => [
                'placeholder' => 'Ex: Dupont',
                'title' => 'Lettres, espaces et apostrophes seulement'
            ]
        ])
        ->add('prenom', TextType::class, [
            'attr' => [
                'placeholder' => 'Ex: Jean',
                'title' => 'Lettres, espaces et apostrophes seulement'
            ]
        ])
        ->add('email', EmailType::class, [
            'attr' => [
                'placeholder' => 'exemple@domaine.com',
                'title' => 'Format: nom.prenom@domaine.com'
            ]
        ])
        ->add('password', PasswordType::class, [
            'required' => !$options['is_edit'],
            'attr' => [
                'placeholder' => 'Minimum 8 caractères',
                'title' => 'Majuscule, minuscule, chiffre et caractère spécial (@$!%*?&)'
            ],
            'help' => '8 caractères min avec : majuscule, minuscule, chiffre, caractère spécial (@$!%*?&)'
        ]);

    // 🔥 ADD ROLE ONLY IF NOT EDIT MODE
    if (!$options['is_edit']) {
        $builder->add('role', ChoiceType::class, [
            'choices' => [
                'Administrateur' => 'ADMIN',
                'Étudiant' => 'ETUDIANT',
            ],
            'placeholder' => 'Sélectionnez un rôle',
            'attr' => ['class' => 'form-select']
        ]);
    }

    $builder->add('niveau', ChoiceType::class, [
        'choices' => [
            'Débutant' => 'DEBUTANT',
            'Intermédiaire' => 'INTERMEDIAIRE',
            'Avancé' => 'AVANCE',
        ],
        'required' => false,
        'placeholder' => 'Sélectionnez un niveau',
        'attr' => ['class' => 'form-select']
    ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserCreateDTO::class,
            'is_edit' => false,
            'validation_groups' => function ($form) {
                $groups = ['Default'];
                $data = $form->getData();
                
                // Ajouter le groupe 'registration' si c'est une création
                if (!$form->getConfig()->getOption('is_edit')) {
                    $groups[] = 'registration';
                }
                
                // Ajouter la validation du niveau si c'est un étudiant
                if ($data && $data->role === 'ETUDIANT') {
                    $groups[] = 'niveau_validation';
                }
                
                return $groups;
            },
        ]);
    }
}