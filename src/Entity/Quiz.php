<?php 

namespace App\Entity;

use App\Entity\GestionDeCours\Chapitre;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// Import de la classe File de Symfony pour gérer les fichiers uploadés
use Symfony\Component\HttpFoundation\File\File;
// Import des contraintes de validation Symfony
use Symfony\Component\Validator\Constraints as Assert;
// Import des annotations VichUploader pour gérer l'upload de fichiers
// VichUploader est un bundle Symfony qui simplifie l'upload de fichiers
// Il gère automatiquement le stockage, le nommage et la suppression des fichiers
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Entité Quiz - Représente un quiz dans le système
 * 
 * Cette classe est le cœur du système de quiz. Elle contient toutes les informations
 * nécessaires pour créer, gérer et passer un quiz.
 * 
 * Fonctionnalités principales :
 * - Stockage des informations du quiz (titre, description, paramètres)
 * - Relation avec les questions (OneToMany)
 * - Relation avec le chapitre parent (ManyToOne)
 * - Support d'image d'illustration via VichUploader Bundle
 *   → VichUploader gère automatiquement l'upload, le stockage et la suppression des images
 *   → Configuration dans config/packages/vich_uploader.yaml
 *   → Les images sont stockées dans public/uploads/quiz_images/
 * - Validation complète des données via Symfony Validator
 * 
 * États possibles : actif, inactif, brouillon, archive
 * 
 * @author Votre Nom
 * @version 1.0
 */
// Annotation Doctrine : Lie cette classe à la table 'quiz' en base de données
#[ORM\Entity(repositoryClass: QuizRepository::class)]
// Annotation VichUploader : Active la gestion automatique d'upload de fichiers pour cette entité
// Cette annotation permet à VichUploader de détecter et gérer les champs uploadables
#[Vich\Uploadable]
class Quiz
{
    /**
     * Identifiant unique du quiz (clé primaire)
     * Généré automatiquement par la base de données
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Titre du quiz
     * 
     * Contraintes :
     * - Obligatoire (NotBlank)
     * - Entre 3 et 255 caractères
     * - Caractères alphanumériques + accents + ponctuation basique
     * 
     * Exemple : "Quiz Python - Les Bases"
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du quiz est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ\s\-',.!?]+$/u",
        message: "Le titre contient des caractères non autorisés."
    )]
    private ?string $titre = null;

    /**
     * Description détaillée du quiz
     * 
     * Contraintes :
     * - Obligatoire
     * - Entre 10 et 2000 caractères
     * - Peut contenir du HTML (sera nettoyé à l'affichage)
     * 
     * Utilisée pour expliquer le contenu et les objectifs du quiz
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    /**
     * État du quiz (machine à états)
     * 
     * Valeurs possibles :
     * - 'actif' : Quiz disponible pour les étudiants
     * - 'inactif' : Quiz temporairement désactivé
     * - 'brouillon' : Quiz en cours de création (non visible)
     * - 'archive' : Quiz archivé (historique)
     * 
     * Transitions autorisées :
     * brouillon → actif → inactif → archive
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "L'état du quiz est obligatoire.")]
    #[Assert\Choice(
        choices: ['actif', 'inactif', 'brouillon', 'archive'],
        message: "L'état doit être: actif, inactif, brouillon ou archive."
    )]
    private ?string $etat = null;

    /**
     * Durée maximale du quiz en minutes
     * 
     * - NULL = temps illimité
     * - Valeur positive = temps limité (ex: 30 minutes)
     * 
     * Si défini, un timer s'affiche pendant le passage du quiz
     * et le quiz est soumis automatiquement à la fin du temps
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "La durée doit être un nombre positif.")]
    private ?int $dureeMaxMinutes = null;

    /**
     * Seuil de réussite en pourcentage
     * 
     * - Valeur entre 0 et 100
     * - Défaut : 50%
     * - Détermine si l'étudiant a réussi ou échoué
     * 
     * Exemples :
     * - 50% : Quiz d'entraînement
     * - 70% : Quiz d'évaluation standard
     * - 100% : Quiz très exigeant (toutes les réponses doivent être correctes)
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "Le seuil de réussite doit être entre {{ min }}% et {{ max }}%."
    )]
    private ?int $seuilReussite = 50;

    /**
     * Nombre maximum de tentatives autorisées
     * 
     * - NULL = tentatives illimitées
     * - Valeur positive = nombre limité (ex: 3 tentatives)
     * 
     * Permet de limiter le nombre de fois qu'un étudiant peut passer le quiz
     * Utile pour les évaluations officielles
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "Le nombre de tentatives doit être positif.")]
    private ?int $maxTentatives = null;

    /**
     * Relation ManyToOne avec Chapitre
     * 
     * Un quiz appartient à un seul chapitre
     * Un chapitre peut avoir plusieurs quiz
     * 
     * Obligatoire : Un quiz doit toujours être lié à un chapitre
     * Permet d'organiser les quiz par thématique
     */
    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le chapitre est obligatoire. Un quiz doit appartenir à un chapitre.")]
    private ?Chapitre $chapitre = null;

    /**
     * Collection des questions du quiz (relation OneToMany)
     * 
     * Un quiz peut avoir plusieurs questions
     * Une question appartient à un seul quiz
     * 
     * Options importantes :
     * - orphanRemoval: true → Si on supprime le quiz, les questions sont supprimées
     * - cascade: ['persist'] → Quand on sauvegarde le quiz, les questions sont sauvegardées aussi
     * 
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', orphanRemoval: true, cascade: ['persist'])]
    private Collection $questions;

    /**
     * Fichier image uploadé (objet File de Symfony)
     * 
     * IMPORTANT : Cette propriété n'est PAS persistée en base de données
     * Elle est utilisée uniquement lors du processus d'upload
     * 
     * Fonctionnement de VichUploader :
     * 1. L'utilisateur sélectionne un fichier dans le formulaire
     * 2. Le fichier est stocké temporairement dans cette propriété
     * 3. VichUploader traite le fichier et le sauvegarde sur le disque
     * 4. Le nom du fichier est stocké dans la propriété $imageName
     * 
     * Configuration VichUploader (dans vich_uploader.yaml) :
     * - mapping: 'quiz_images' → Nom du mapping défini dans la config
     * - fileNameProperty: 'imageName' → Propriété où stocker le nom du fichier
     * - size: 'imageSize' → Propriété où stocker la taille du fichier
     */
    #[Vich\UploadableField(mapping: 'quiz_images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    /**
     * Nom du fichier image stocké sur le serveur
     * 
     * Cette propriété EST persistée en base de données
     * Généré automatiquement par VichUploader lors de l'upload
     * 
     * Format du nom : quiz_[timestamp]_[random].[extension]
     * Exemple : "quiz_65a1b2c3d4e5f.jpg"
     * 
     * VichUploader génère un nom unique pour éviter les conflits
     * et améliorer la sécurité (pas de nom de fichier prévisible)
     */
    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    /**
     * Taille du fichier image en octets
     * 
     * Stockée automatiquement par VichUploader
     * Utile pour afficher la taille du fichier ou pour des statistiques
     * 
     * Exemple : 245678 (octets) = ~240 KB
     */
    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    /**
     * Date de dernière modification de l'image
     * 
     * Mise à jour automatiquement lors de l'upload d'une nouvelle image
     * Utilisée par VichUploader pour gérer le cache des navigateurs
     * 
     * Fonctionnement du cache :
     * - Quand l'image change, updatedAt change aussi
     * - L'URL de l'image inclut ce timestamp
     * - Le navigateur détecte le changement d'URL et recharge l'image
     * - Évite les problèmes de cache (affichage de l'ancienne image)
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Constructeur
     * Initialise la collection de questions vide
     * Appelé automatiquement lors de la création d'un nouveau quiz
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant du quiz
     * 
     * @return int|null L'ID du quiz ou null si pas encore persisté
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    // ========== GETTERS ET SETTERS POUR LES PROPRIÉTÉS BASIQUES ==========

    /**
     * Retourne le titre du quiz
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Définit le titre du quiz
     * 
     * @param string $titre Le nouveau titre
     * @return static Pour permettre le chaînage de méthodes
     */
    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    // ========== GESTION DES QUESTIONS (RELATION ONETOMANY) ==========

    /**
     * Retourne la collection de toutes les questions du quiz
     * 
     * @return Collection<int, Question> Collection Doctrine des questions
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * Ajoute une question au quiz
     * 
     * Gère automatiquement la relation bidirectionnelle :
     * - Ajoute la question à la collection
     * - Définit ce quiz comme parent de la question
     * 
     * @param Question $question La question à ajouter
     * @return static Pour le chaînage
     */
    public function addQuestion(Question $question): static
    {
        // Vérifie que la question n'est pas déjà dans la collection (évite les doublons)
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            // Définit la relation inverse (bidirectionnelle)
            $question->setQuiz($this);
        }
        return $this;
    }

    /**
     * Retire une question du quiz
     * 
     * Gère la relation bidirectionnelle :
     * - Retire la question de la collection
     * - Supprime la référence au quiz dans la question
     * 
     * @param Question $question La question à retirer
     * @return static Pour le chaînage
     */
    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // Si la question appartenait bien à ce quiz
            if ($question->getQuiz() === $this) {
                // On supprime la référence
                $question->setQuiz(null);
            }
        }
        return $this;
    }

    // ========== GESTION DU CHAPITRE (RELATION MANYTOONE) ==========

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(?Chapitre $chapitre): static
    {
        $this->chapitre = $chapitre;
        return $this;
    }

    // ========== GETTERS ET SETTERS POUR LES PARAMÈTRES DU QUIZ ==========

    public function getDureeMaxMinutes(): ?int
    {
        return $this->dureeMaxMinutes;
    }

    public function setDureeMaxMinutes(?int $dureeMaxMinutes): static
    {
        $this->dureeMaxMinutes = $dureeMaxMinutes;
        return $this;
    }

    public function getSeuilReussite(): ?int
    {
        return $this->seuilReussite;
    }

    public function setSeuilReussite(?int $seuilReussite): static
    {
        $this->seuilReussite = $seuilReussite;
        return $this;
    }

    public function getMaxTentatives(): ?int
    {
        return $this->maxTentatives;
    }

    public function setMaxTentatives(?int $maxTentatives): static
    {
        $this->maxTentatives = $maxTentatives;
        return $this;
    }

    // ========== GESTION DE L'IMAGE (VICHUPLOADER BUNDLE) ==========
    // VichUploader simplifie l'upload de fichiers en gérant automatiquement :
    // - Le stockage des fichiers sur le disque
    // - La génération de noms uniques
    // - La suppression des anciens fichiers
    // - La gestion du cache des navigateurs

    /**
     * Définit le fichier image à uploader
     * 
     * Cette méthode est appelée automatiquement par VichUploader lors de la soumission du formulaire
     * Elle met à jour la date de modification pour forcer le rafraîchissement du cache
     * 
     * Processus d'upload avec VichUploader :
     * 1. L'utilisateur soumet le formulaire avec un fichier
     * 2. Symfony appelle cette méthode avec l'objet File
     * 3. VichUploader détecte le changement et traite le fichier
     * 4. Le fichier est copié dans le répertoire configuré (public/uploads/quiz_images/)
     * 5. Un nom unique est généré et stocké dans $imageName
     * 6. La taille du fichier est stockée dans $imageSize
     * 7. L'ancien fichier (si existant) est automatiquement supprimé
     * 
     * @param File|null $imageFile Le fichier uploadé depuis le formulaire
     */
    public function setImageFile(?File $imageFile = null): void
    {
        // Stocke le fichier uploadé dans la propriété
        $this->imageFile = $imageFile;

        // Si un fichier est uploadé (pas null)
        if (null !== $imageFile) {
            // Met à jour la date de modification avec l'heure actuelle
            // IMPORTANT : Cela force le navigateur à recharger l'image
            // Car l'URL de l'image inclut ce timestamp (via vich_uploader_asset)
            // Exemple d'URL : /uploads/quiz_images/quiz_123.jpg?v=1234567890
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Retourne le fichier image uploadé
     * 
     * Utilisé principalement par le formulaire pour afficher l'image actuelle
     * 
     * @return File|null L'objet File ou null si aucune image
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Définit le nom du fichier image
     * 
     * Cette méthode est appelée automatiquement par VichUploader après l'upload
     * Le nom est généré automatiquement pour éviter les conflits et améliorer la sécurité
     * 
     * Format du nom généré : [prefix]_[timestamp]_[random].[extension]
     * Exemple : quiz_65a1b2c3d4e5f.jpg
     * 
     * @param string|null $imageName Le nom du fichier généré par VichUploader
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * Retourne le nom du fichier image
     * 
     * Utilisé pour :
     * - Générer l'URL de l'image dans les templates (avec vich_uploader_asset)
     * - Vérifier si une image existe
     * - Afficher le nom du fichier
     * 
     * @return string|null Le nom du fichier ou null si aucune image
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * Définit la taille du fichier image
     * 
     * Appelée automatiquement par VichUploader après l'upload
     * 
     * @param int|null $imageSize La taille en octets
     */
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    /**
     * Retourne la taille du fichier image
     * 
     * Utile pour :
     * - Afficher la taille du fichier à l'utilisateur
     * - Calculer l'espace disque utilisé
     * - Générer des statistiques
     * 
     * @return int|null La taille en octets ou null si aucune image
     */
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * Retourne la date de dernière modification
     * 
     * Utilisée par VichUploader pour générer des URLs avec timestamp
     * Cela évite les problèmes de cache des navigateurs
     * 
     * @return \DateTimeImmutable|null La date de modification ou null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date de dernière modification
     * 
     * @param \DateTimeImmutable|null $updatedAt La nouvelle date
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
