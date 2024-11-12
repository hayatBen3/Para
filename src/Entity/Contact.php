<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Fullname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailadresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->Fullname;
    }

    public function setFullname(?string $Fullname): static
    {
        $this->Fullname = $Fullname;

        return $this;
    }

    public function getEmailadresse(): ?string
    {
        return $this->emailadresse;
    }

    public function setEmailadresse(?string $emailadresse): static
    {
        $this->emailadresse = $emailadresse;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
