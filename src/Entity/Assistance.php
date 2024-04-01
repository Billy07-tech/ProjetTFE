<?php

namespace App\Entity;

use App\Repository\AssistanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssistanceRepository::class)]
#[ORM\Table(name:"assistance")]
class Assistance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_envoie = null; 

    public function __construct()
    {
        $this->date_envoie = new \DateTimeImmutable(); 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDateEnvoieFormatted(): string
    {
        return $this->date_envoie->format('Y-m-d H:i:s');
    }

    public function setDateEnvoie(?\DateTimeInterface $date_envoie): void
    {
        $this->date_envoie = $date_envoie ? \DateTimeImmutable::createFromMutable($date_envoie) : null;
    }

}
