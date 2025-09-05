<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    // On conserve id_auteur pour la DB
    #[ORM\Column]
    private ?int $id_auteur = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_auteur", referencedColumnName: "id", nullable: false)]
    private ?Utilisateur $auteur = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[Vich\UploadableField(mapping: 'article_images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }
    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }
    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getIdAuteur(): ?int
    {
        return $this->id_auteur;
    }
    public function setIdAuteur(int $id_auteur): static
    {
        $this->id_auteur = $id_auteur;
        return $this;
    }

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }
    public function setAuteur(Utilisateur $auteur): static
    {
        $this->auteur = $auteur;
        $this->id_auteur = $auteur->getId(); // mettre à jour id_auteur aussi
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }
    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $file = null): void
    {
        $this->imageFile = $file;
        if ($file !== null) {
            $this->updatedAt = new \DateTime(); 
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function setImageName(?string $name): void
    {
        $this->imageName = $name;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }
    public function setImageSize(?int $size): void
    {
        $this->imageSize = $size;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?\DateTimeInterface $d): void
    {
        $this->updatedAt = $d;
    }
}
