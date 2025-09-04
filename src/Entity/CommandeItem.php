<?php

namespace App\Entity;

use App\Repository\CommandeItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeItemRepository::class)]
class CommandeItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column(type: 'integer')]
    private int $quantite;

    #[ORM\Column(type: 'integer')]
    private int $prix; // prix unitaire au moment de l'achat

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }
    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }
    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }
    public function setQuantite(int $qte): static
    {
        $this->quantite = $qte;
        return $this;
    }

    public function getPrix(): int
    {
        return $this->prix;
    }
    public function setPrix(int $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getTotal(): int
    {
        return $this->prix * $this->quantite;
    }
}
