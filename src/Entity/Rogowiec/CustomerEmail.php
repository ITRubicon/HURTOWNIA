<?php

namespace App\Entity\Rogowiec;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_customer_email')]
#[Index(name: "source_customercode_idx", fields: ["source", "customer_code"])]
class CustomerEmail
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
}