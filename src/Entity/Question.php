<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Import du repository pour les requêtes personnalisées
use App\Repository\QuestionRepository;
// Import des classes pour gérer les collections Doctrine
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
// Import des types de données Doctrine
use Doctrine\DBAL\Types\Types;
// Import des annotations ORM pour mapper l'entité à la base de données
use Doctrine\ORM\Mapping as ORM;
// Import de la classe File pour gérer les fichiers uploadés
use Symfony\Component\HttpFoundation\File\File;
// Import des contraintes de validation Symfony
use Symfony\Component\Validator\Constraints as Assert;
// Import des annotations VichUploader pour gérer les uploads de fichiers
use Vich\UploaderBundle\Mapping\Annotation as Vich;

// Définit cette classe comme une entité Doctrine avec son repository
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
// Annotation VichUploader : Active la gestion automatique d'upload pour cette entité
// Permet à VichUploader de gérer automatiquement les fichiers (images, audio, vidéo)
#[Vich\Uploadable]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le texte de la question est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: "La question doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La question ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $texteQuestion = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de points est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de points doit être positif.")]
    #[Assert\Range(
        min: 1,
        max: 100,
        notInRangeMessage: "Le nombre de points doit être entre {{ min }} et {{ max }}."
    )]
    #[Assert\Type(
        type: 'integer',
        message: "Le nombre de points doit être un nombre entier."
    )]
    private ?int $point = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La question doit être associée à un quiz.")]
    private ?Quiz $quiz = null;

    /**
     * Collection des options de réponse pour cette question
     * Une question peut avoir plusieurs options (réponses possibles)
     * 
     * @var Collection<int, Option>
     */
    #[ORM\OneToMany(targetEntity: Option::class, mappedBy: 'question', orphanRemoval: true, cascade: ['persist'])]
    private Collection $options;

    // ========================================
    // GESTION DES IMAGES - VichUploaderBundle
    // ========================================
    
    /**
     * Fichier image temporaire uploadé par l'utilisateur
     * 
     * Annotation VichUploader pour gérer l'upload d'images :
     * - mapping: 'question_images' → Utilise la configuration définie dans vich_uploader.yaml
     * - fileNameProperty: 'imageName' → Stocke le nom du fichier dans la propriété $imageName
     * - size: 'imageSize' → Stocke la taille du fichier dans la propriété $imageSize
     * 
     * VichUploader gère automatiquement :
     * 1. L'upload du fichier vers le dossier configuré
     * 2. La génération d'un nom unique pour éviter les conflits
     * 3. La mise à jour des propriétés imageName et imageSize
     * 4. La suppression de l'ancien fichier lors d'un remplacement
     * 5. La suppression du fichier lors de la suppression de l'entité
     */
    #[Vich\UploadableField(mapping: 'question_images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    /**
     * Nom du fichier image stocké sur le serveur
     * Généré automatiquement par VichUploader (avec SmartUniqueNamer)
     * Exemple : "python-question-5f4dcc3b5aa765d61d8327deb882cf99.jpg"
     */
    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    /**
     * Taille du fichier image en octets
     * Rempli automatiquement par VichUploader lors de l'upload
     * Permet de vérifier la taille et d'afficher des informations à l'utilisateur
     */
    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    // ========================================
    // GESTION DES FICHIERS AUDIO - VichUploaderBundle
    // ========================================
    
    /**
     * Fichier audio temporaire uploadé par l'utilisateur
     * 
     * Annotation VichUploader pour gérer l'upload de fichiers audio :
     * - mapping: 'question_audio' → Utilise la configuration définie dans vich_uploader.yaml
     * - fileNameProperty: 'audioName' → Stocke le nom du fichier dans la propriété $audioName
     * - size: 'audioSize' → Stocke la taille du fichier dans la propriété $audioSize
     * 
     * Permet d'ajouter des questions audio (prononciation, écoute, etc.)
     */
    #[Vich\UploadableField(mapping: 'question_audio', fileNameProperty: 'audioName', size: 'audioSize')]
    private ?File $audioFile = null;

    /**
     * Nom du fichier audio stocké sur le serveur
     * Généré automatiquement par VichUploader
     */
    #[ORM\Column(nullable: true)]
    private ?string $audioName = null;

    /**
     * Taille du fichier audio en octets
     * Rempli automatiquement par VichUploader
     */
    #[ORM\Column(nullable: true)]
    private ?int $audioSize = null;

    // ========================================
    // GESTION DES FICHIERS VIDÉO - VichUploaderBundle
    // ========================================
    
    /**
     * Fichier vidéo temporaire uploadé par l'utilisateur
     * 
     * Annotation VichUploader pour gérer l'upload de fichiers vidéo :
     * - mapping: 'question_video' → Utilise la configuration définie dans vich_uploader.yaml
     * - fileNameProperty: 'videoName' → Stocke le nom du fichier dans la propriété $videoName
     * - size: 'videoSize' → Stocke la taille du fichier dans la propriété $videoSize
     * 
     * Permet d'ajouter des questions vidéo (tutoriels, démonstrations, etc.)
     */
    #[Vich\UploadableField(mapping: 'question_video', fileNameProperty: 'videoName', size: 'videoSize')]
    private ?File $videoFile = null;

    /**
     * Nom du fichier vidéo stocké sur le serveur
     * Généré automatiquement par VichUploader
     */
    #[ORM\Column(nullable: true)]
    private ?string $videoName = null;

    /**
     * Taille du fichier vidéo en octets
     * Rempli automatiquement par VichUploader
     */
    #[ORM\Column(nullable: true)]
    private ?int $videoSize = null;

    /**
     * Date de dernière mise à jour de la question
     * 
     * Utilisé par VichUploader pour gérer le cache des fichiers :
     * - Mis à jour automatiquement lors de l'upload d'un nouveau fichier
     * - Permet de forcer le rechargement des images/audio/vidéo dans le navigateur
     * - Évite les problèmes de cache en ajoutant un timestamp à l'URL
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexteQuestion(): ?string
    {
        return $this->texteQuestion;
    }

    public function setTexteQuestion(string $texteQuestion): static
    {
        $this->texteQuestion = $texteQuestion;
        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): static
    {
        $this->point = $point;
        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setQuestion($this);
        }
        return $this;
    }

    public function removeOption(Option $option): static
    {
        if ($this->options->removeElement($option)) {
            if ($option->getQuestion() === $this) {
                $option->setQuestion(null);
            }
        }
        return $this;
    }

    // ========================================
    // GETTERS/SETTERS POUR LES IMAGES - VichUploader
    // ========================================
    
    /**
     * Définit le fichier image à uploader
     * 
     * Appelé automatiquement par Symfony Form lors de la soumission du formulaire
     * VichUploader intercepte ce setter pour gérer l'upload
     * 
     * @param File|null $imageFile Le fichier image uploadé
     */
    public function setImageFile(?File $imageFile = null): void
    {
        // Stocke le fichier temporaire
        $this->imageFile = $imageFile;
        
        // Si un nouveau fichier est uploadé, met à jour le timestamp
        // Cela force le rechargement de l'image dans le navigateur (évite le cache)
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Récupère le fichier image temporaire
     * Utilisé par VichUploader et Symfony Form
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Définit le nom du fichier image
     * Appelé automatiquement par VichUploader après l'upload
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * Récupère le nom du fichier image stocké
     * Utilisé pour générer l'URL de l'image dans les templates Twig
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * Définit la taille du fichier image
     * Appelé automatiquement par VichUploader après l'upload
     */
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    /**
     * Récupère la taille du fichier image en octets
     * Permet d'afficher la taille à l'utilisateur ou de faire des validations
     */
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    // ========================================
    // GETTERS/SETTERS POUR LES FICHIERS AUDIO - VichUploader
    // ========================================
    
    /**
     * Définit le fichier audio à uploader
     * Fonctionne de la même manière que setImageFile()
     */
    public function setAudioFile(?File $audioFile = null): void
    {
        $this->audioFile = $audioFile;
        if (null !== $audioFile) {
            // Met à jour le timestamp pour forcer le rechargement du fichier audio
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Récupère le fichier audio temporaire
     */
    public function getAudioFile(): ?File
    {
        return $this->audioFile;
    }

    /**
     * Définit le nom du fichier audio
     * Appelé automatiquement par VichUploader
     */
    public function setAudioName(?string $audioName): void
    {
        $this->audioName = $audioName;
    }

    /**
     * Récupère le nom du fichier audio stocké
     */
    public function getAudioName(): ?string
    {
        return $this->audioName;
    }

    /**
     * Définit la taille du fichier audio
     */
    public function setAudioSize(?int $audioSize): void
    {
        $this->audioSize = $audioSize;
    }

    /**
     * Récupère la taille du fichier audio en octets
     */
    public function getAudioSize(): ?int
    {
        return $this->audioSize;
    }

    // ========================================
    // GETTERS/SETTERS POUR LES FICHIERS VIDÉO - VichUploader
    // ========================================
    
    /**
     * Définit le fichier vidéo à uploader
     * Fonctionne de la même manière que setImageFile()
     */
    public function setVideoFile(?File $videoFile = null): void
    {
        $this->videoFile = $videoFile;
        if (null !== $videoFile) {
            // Met à jour le timestamp pour forcer le rechargement de la vidéo
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Récupère le fichier vidéo temporaire
     */
    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    /**
     * Définit le nom du fichier vidéo
     * Appelé automatiquement par VichUploader
     */
    public function setVideoName(?string $videoName): void
    {
        $this->videoName = $videoName;
    }

    /**
     * Récupère le nom du fichier vidéo stocké
     */
    public function getVideoName(): ?string
    {
        return $this->videoName;
    }

    /**
     * Définit la taille du fichier vidéo
     */
    public function setVideoSize(?int $videoSize): void
    {
        $this->videoSize = $videoSize;
    }

    /**
     * Récupère la taille du fichier vidéo en octets
     */
    public function getVideoSize(): ?int
    {
        return $this->videoSize;
    }

    /**
     * Récupère la date de dernière mise à jour
     * Utilisé par VichUploader pour gérer le cache des fichiers
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date de dernière mise à jour
     * Mis à jour automatiquement lors de l'upload de fichiers
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
