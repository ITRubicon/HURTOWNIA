<?php

namespace App\Entity\Rogowiec;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_customer_email_archive')]
#[Index(name: "source_customercode_idx", fields: ["source", "customer_code"])]
#[UniqueConstraint("source_code_address_un", columns: ["source", "customer_code", "address"])]
class CustomerEmailArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private ?string $customer_code;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $owner = null;

    #[ORM\Column]
    private ?bool $is_default = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $fetchDate = null;
}