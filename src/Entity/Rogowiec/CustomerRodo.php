<?php

namespace App\Entity\Rogowiec;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_customer_rodo')]
#[Index(name: "source_customercode_idx", fields: ["source", "customer_code"])]
class CustomerRodo
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