<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $contenu = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageFile = null;

    #[Vich\UploadableField(mapping: 'post_images', fileNameProperty: 'imageFile')]
    private ?File $imageFileUpload = null;

    #[ORM\Column(nullable: true)]
    private ?string $videoFile = null;

    #[Vich\UploadableField(mapping: 'post_videos', fileNameProperty: 'videoFile')]
    private ?File $videoFileUpload = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Communaute $communaute = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(referencedColumnName: 'userId', nullable: true)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Commentaire::class, orphanRemoval: true)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(?string $contenu): self { $this->contenu = $contenu ?? ''; return $this; }

    public function getSummary(): ?string { return $this->summary; }
    public function setSummary(?string $summary): self { $this->summary = $summary; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getImageFile(): ?string { return $this->imageFile; }
    public function setImageFile(?string $imageFile): void { $this->imageFile = $imageFile; }

    public function setImageFileUpload(?File $file = null): void
    {
        $this->imageFileUpload = $file;
        if ($file) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getImageFileUpload(): ?File
    {
        return $this->imageFileUpload;
    }

    public function getVideoFile(): ?string { return $this->videoFile; }
    public function setVideoFile(?string $videoFile): void { $this->videoFile = $videoFile; }

    public function setVideoFileUpload(?File $file = null): void
    {
        $this->videoFileUpload = $file;
        if ($file) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getVideoFileUpload(): ?File
    {
        return $this->videoFileUpload;
    }

    public function getCommunaute(): ?Communaute { return $this->communaute; }
    public function setCommunaute(?Communaute $communaute): self { $this->communaute = $communaute; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getCommentaires(): Collection { return $this->commentaires; }

    public function __toString(): string
    {
        return (string) ($this->contenu ?? '');
    }
}