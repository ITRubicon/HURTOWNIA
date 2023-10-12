<?php

namespace App\Entity\Rogowiec;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_customer_rodo_archive')]
#[Index(name: "source_customercode_idx", fields: ["source", "customer_code"])]
#[UniqueConstraint("source_code_un", columns: ["source", "customer_code", "producer", "number"])]
class CustomerRodoArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private $customer_code;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $producer;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $number;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $validUntil;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $consents;
}