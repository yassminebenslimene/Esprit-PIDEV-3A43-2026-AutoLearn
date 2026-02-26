<?php

// Déclaration du namespace pour les formulaires
namespace App\Form;

// Import de l'entité Question que ce formulaire va gérer
use App\Entity\Question;
// Import de l'entité Quiz pour le champ de relation
use App\Entity\Quiz;
// Import du type EntityType pour les champs de relation Doctrine
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// Import de la classe de base pour créer des formulaires
use Symfony\Component\Form\AbstractType;
// Import du type IntegerType pour les champs numériques
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
// Import du type TextareaType pour les champs de texte multiligne
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// Import de FormBuilderInterface pour construire le formulaire
use Symfony\Component\Form\FormBuilderInterface;
// Import de OptionsResolver pour configurer les options du formulaire
use Symfony\Component\OptionsResolver\OptionsResolver;
// Import du type VichImageType pour l'upload d'images (VichUploaderBundle)
use Vich\UploaderBundle\Form\Type\VichImageType;
// Import du type VichFileType pour l'upload de fichiers génériques (audio, vidéo, etc.)
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Formulaire pour créer et éditer une question de quiz
 * 
 * Ce formulaire gère les questions avec support multimédia :
 * - Texte de la question
 * - Points attribués
 * - Image illustrative (VichImageType)
 * - Fichier audio (VichFileType)
 * - Fichier vidéo (VichFileType)
 */
class QuestionType extends AbstractType
{
    /**
     * Construit le formulaire en ajoutant tous les champs nécessaires
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options du formulaire
     */
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
            // Champ d'upload d'image via VichUploaderBundle
            // VichImageType est spécialisé pour les images (affiche un aperçu)
            ->add('imageFile', VichImageType::class, [
                // Label du champ
                'label' => 'Image (optionnel)',
                // Champ optionnel (une question peut ne pas avoir d'image)
                'required' => false,
                // Permet d'afficher une checkbox pour supprimer l'image existante
                'allow_delete' => true,
                // Label de la checkbox de suppression
                'delete_label' => 'Supprimer',
                // Ne pas afficher le lien de téléchargement (on préfère l'aperçu)
                'download_uri' => false,
                // Afficher l'aperçu de l'image actuelle dans le formulaire
                'image_uri' => true,
                // Utiliser l'asset helper pour générer les URLs
                'asset_helper' => true,
                // Texte d'aide pour informer l'utilisateur des formats acceptés
                'help' => 'JPG, PNG, GIF (max 2MB)'
            ])
            // Champ d'upload de fichier audio via VichUploaderBundle
            // VichFileType est générique pour tous types de fichiers (audio, vidéo, PDF, etc.)
            ->add('audioFile', VichFileType::class, [
                // Label du champ
                'label' => 'Audio (optionnel)',
                // Champ optionnel (pour les questions audio/prononciation)
                'required' => false,
                // Permet de supprimer le fichier audio existant
                'allow_delete' => true,
                // Label de la checkbox de suppression
                'delete_label' => 'Supprimer',
                // Afficher un lien de téléchargement pour écouter l'audio actuel
                'download_uri' => true,
                // Utiliser l'asset helper pour générer les URLs
                'asset_helper' => true,
                // Texte d'aide pour les formats audio acceptés
                'help' => 'MP3, WAV, OGG (max 5MB)'
            ])
            // Champ d'upload de fichier vidéo via VichUploaderBundle
            // Utilise VichFileType car les vidéos ne nécessitent pas d'aperçu dans le formulaire
            ->add('videoFile', VichFileType::class, [
                // Label du champ
                'label' => 'Vidéo (optionnel)',
                // Champ optionnel (pour les questions vidéo/tutoriels)
                'required' => false,
                // Permet de supprimer la vidéo existante
                'allow_delete' => true,
                // Label de la checkbox de suppression
                'delete_label' => 'Supprimer',
                // Afficher un lien de téléchargement pour voir la vidéo actuelle
                'download_uri' => true,
                // Utiliser l'asset helper pour générer les URLs
                'asset_helper' => true,
                // Texte d'aide pour les formats vidéo acceptés
                'help' => 'MP4, WEBM (max 20MB)'
            ])
        ;
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
            // Lie le formulaire à l'entité Question
            // Cela permet de mapper automatiquement les champs aux propriétés de l'entité
            'data_class' => Question::class,
        ]);
    }
}
