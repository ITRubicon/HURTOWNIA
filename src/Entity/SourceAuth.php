<?php

namespace App\Entity;

use App\Repository\SourceAuthRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceAuthRepository::class)]
#[ORM\UniqueConstraint(name: 'name_un', fields: ['name'])]
class SourceAuth implements IConnection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $producer = null;

    #[ORM\Column(length: 5)]
    private ?string $type = null;

    #[ORM\Column(length: 100)]
    private ?string $base_url = null;

    #[ORM\Column(length: 20)]
    private ?string $auth_type = null;

    #[ORM\Column(length: 255)]
    private ?string $auth = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBaseUrl(): ?string
    {
        return $this->base_url;
    }

    public function setBaseUrl(string $base_url): self
    {
        $this->base_url = $base_url;

        return $this;
    }

    public function getAuthType(): ?string
    {
        return $this->auth_type;
    }

    public function setAuthType(string $auth_type): self
    {
        $this->auth_type = $auth_type;

        return $this;
    }

    public function getAuth(): ?string
    {
        return $this->auth;
    }

    public function setAuth(string $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    public function getProducer(): ?string
    {
        return $this->producer;
    }

    public function setProducer(string $producer): self
    {
        $this->producer = $producer;

        return $this;
    }
}
