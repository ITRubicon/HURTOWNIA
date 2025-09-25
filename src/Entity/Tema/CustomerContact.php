<?php

namespace App\Entity\Tema;

use App\Repository\CustomerContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CustomerContactRepository::class)]
#[Table(name: 'tema_customer_contact')]
#[Index(name: "source_idx", fields: ["source", "customer_id"])]
class CustomerContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private ?string $customer_id = null;

    #[ORM\Column]
    private ?int $rowId = null;

    #[ORM\Column(length: 6)]
    private ?string $contact_id = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $personalId = null;

    #[ORM\Column(length: 20)]
    private ?string $firstName = null;

    #[ORM\Column(length: 20)]
    private ?string $lastName = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 19, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?bool $isDefault = null;

}
