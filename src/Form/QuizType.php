<?php
// Déclaration du fichier PHP

// Définition du namespace pour les formulaires
namespace App\Form;

<<<<<<< HEAD
// Import de l'entité Chapitre pour le champ de sélection
use App\Entity\GestionDeCours\Chapitre;
// Import de l'entité Quiz que ce formulaire va gérer
use App\Entity\Quiz;
// Import du type EntityType pour les champs de relation
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// Import de la classe de base pour créer des formulaires
=======
use App\Entity\Quiz;
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
use Symfony\Component\Form\AbstractType;
// Import du type ChoiceType pour les listes déroulantes
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
// Import du type IntegerType pour les champs numériques entiers
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
// Import du type TextareaType pour les champs de texte multiligne
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// Import du type TextType pour les champs de texte simple
use Symfony\Component\Form\Extension\Core\Type\TextType;
// Import de FormBuilderInterface pour construire le formulaire
use Symfony\Component\Form\FormBuilderInterface;
// Import de OptionsResolver pour configurer les options du formulaire
use Symfony\Component\OptionsResolver\OptionsResolver;
// Import du type VichImageType pour l'upload d'images
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Formulaire pour créer et éditer un quiz
 * 
 * Cette classe définit tous les champs du formulaire de quiz
 * avec leurs validations, labels et options d'affichage
 */
class QuizType extends AbstractType
{
    /**
     * Construit le formulaire en ajoutant tous les champs nécessaires
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Commence la construction du formulaire
        $builder
            // Ajoute le champ "titre" de type texte simple
            ->add('titre', TextType::class, [
                // Label affiché au-dessus du champ
                'label' => 'Titre du quiz',
                // Attributs HTML du champ input
                'attr' => [
                    // Texte d'aide dans le champ vide
                    'placeholder' => 'Entrez le titre du quiz',
                    // Limite de caractères côté HTML
                    'maxlength' => 255
                ],
                // Champ obligatoire
                'required' => true,
                // Texte d'aide affiché sous le champ
                'help' => 'Entre 3 et 255 caractères'
            ])
            // Ajoute le champ "description" de type textarea
            ->add('description', TextareaType::class, [
                // Label du champ
                'label' => 'Description',
                // Attributs HTML du textarea
                'attr' => [
                    // Texte d'aide dans le champ vide
                    'placeholder' => 'Décrivez le contenu du quiz',
                    // Nombre de lignes visibles
                    'rows' => 5,
                    // Limite de caractères
                    'maxlength' => 2000
                ],
                // Champ obligatoire
                'required' => true,
                // Texte d'aide
                'help' => 'Entre 10 et 2000 caractères'
            ])
            // Ajoute le champ "etat" de type liste déroulante
            ->add('etat', ChoiceType::class, [
                // Label du champ
                'label' => 'État',
                // Liste des choix possibles (texte affiché => valeur en base)
                'choices' => [
                    'Actif' => 'actif',
                    'Inactif' => 'inactif',
                    'Brouillon' => 'brouillon',
                    'Archivé' => 'archive'
                ],
                // Champ obligatoire
                'required' => true,
<<<<<<< HEAD
                // Texte d'aide
                'help' => 'Définissez le statut du quiz'
            ])
            // Ajoute le champ "chapitre" de type relation Entity
            ->add('chapitre', EntityType::class, [
                // Classe de l'entité liée
                'class' => Chapitre::class,
                // Propriété de l'entité à afficher dans la liste
                'choice_label' => 'titre',
                // Label du champ
                'label' => 'Chapitre *',
                // Texte affiché quand aucun choix n'est sélectionné
                'placeholder' => 'Sélectionnez un chapitre obligatoirement',
                // Champ obligatoire
                'required' => true,
                // Texte d'aide avec emoji pour attirer l'attention
                'help' => '🔒 OBLIGATOIRE : Chaque quiz doit appartenir à un chapitre',
                // Attributs HTML du select
                'attr' => [
                    // Classe CSS pour styliser le champ obligatoire
                    'class' => 'required-field'
                ]
            ])
            // Ajoute le champ "dureeMaxMinutes" de type nombre entier
            ->add('dureeMaxMinutes', IntegerType::class, [
                // Label du champ
                'label' => 'Durée maximale (minutes)',
                // Attributs HTML de l'input
                'attr' => [
                    // Texte d'aide dans le champ vide
                    'placeholder' => 'Ex: 20',
                    // Valeur minimale autorisée
                    'min' => 1
                ],
                // Champ optionnel (peut être null)
                'required' => false,
                // Texte d'aide
                'help' => 'Laissez vide pour un quiz sans limite de temps'
            ])
            // Ajoute le champ "seuilReussite" de type nombre entier
            ->add('seuilReussite', IntegerType::class, [
                // Label du champ
                'label' => 'Seuil de réussite (%)',
                // Attributs HTML de l'input
                'attr' => [
                    // Texte d'aide dans le champ vide
                    'placeholder' => 'Ex: 50',
                    // Valeur minimale (0%)
                    'min' => 0,
                    // Valeur maximale (100%)
                    'max' => 100
                ],
                // Champ optionnel
                'required' => false,
                // Texte d'aide
                'help' => 'Pourcentage minimum pour valider le quiz (défaut: 50%)'
            ])
            // Ajoute le champ "maxTentatives" de type nombre entier
            ->add('maxTentatives', IntegerType::class, [
                // Label du champ
                'label' => 'Nombre maximum de tentatives',
                // Attributs HTML de l'input
                'attr' => [
                    // Texte d'aide dans le champ vide
                    'placeholder' => 'Ex: 3',
                    // Valeur minimale
                    'min' => 1
                ],
                // Champ optionnel
                'required' => false,
                // Texte d'aide
                'help' => 'Laissez vide pour un nombre illimité de tentatives'
            ])
            // Image désactivée - propriété imageFile n'existe pas dans l'entité Quiz
            /*
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image du quiz (optionnel)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
                'help' => 'Formats acceptés: JPG, PNG, GIF (max 2MB)'
            ])
            */
=======
                'placeholder' => 'Sélectionnez un état',
                'help' => 'Définissez le statut du quiz'
            ])
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
        ;
        // Fin de la construction du formulaire
    }

    /**
     * Configure les options par défaut du formulaire
     * 
     * @param OptionsResolver $resolver Le résolveur d'options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        // Définit les options par défaut
        $resolver->setDefaults([
            // Lie le formulaire à l'entité Quiz
            // Cela permet de mapper automatiquement les champs aux propriétés de l'entité
            'data_class' => Quiz::class,
        ]);
    }
}
