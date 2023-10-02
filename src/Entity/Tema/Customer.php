<?php

namespace App\Entity\Tema;

use App\Repository\CustomerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'tema_customer')]
#[Index(name: "source_idx", fields: ["source"])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(nullable: true)]
    private ?int $rowId = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $customer_id = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $vatId = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $personalId = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $krsId = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $regonId = null;

    #[ORM\Column(length: 160, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $postCode = null;

    #[ORM\Column(length: 33, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $countryName = null;

    #[ORM\Column(length: 19, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modifyDate = null;

    #[ORM\Column(length: 36, nullable: true)]
    private ?string $renaultPersonId = null;
}