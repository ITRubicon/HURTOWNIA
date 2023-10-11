<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_customer_archive')]
#[Index(name: "source_idx", fields: ["source"])]
class CustomerArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private ?string $code;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $taxNumber;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $personalId;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $businesNumber;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $kind;
}
