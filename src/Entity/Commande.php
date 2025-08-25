<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCommande;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'payée'; // ex: payée, en cours, expédiée

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: CommandeItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: 'integer')]
    private int $total = 0;

    public function __construct()
    {
        $this->dateCommande = new \DateTime();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getDateCommande(): \DateTimeInterface { return $this->dateCommande; }
    public function setDateCommande(\DateTimeInterface $date): static { $this->dateCommande = $date; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }
    public function setAdresseLivraison(?string $adresse): static { $this->adresseLivraison = $adresse; return $this; }

    public function getItems(): Collection { return $this->items; }
    public function addItem(CommandeItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCommande($this);
        }
        return $this;
    }
    public function removeItem(CommandeItem $item): static
    {
        if ($this->items->removeElement($item)) {
            if ($item->getCommande() === $this) $item->setCommande(null);
        }
        return $this;
    }

    public function getTotal(): int { return $this->total; }
    public function setTotal(int $total): static { $this->total = $total; return $this; }
}
