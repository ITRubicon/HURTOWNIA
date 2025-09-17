<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: ServiceEntityRepository::class)]
#[Table(name: 'tema_car_order')]
#[UniqueConstraint(name: "source_docId_idx", fields: ["source", "doc_id"])]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "source_vin_idx", fields: ["source", "vin"])]
class CarOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12)]
    private ?string $doc_id = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $closing_date = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_canceled = null;

    #[ORM\Column(length: 10)]
    private ?string $customer_id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $payer_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $vehicle_id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $seller_id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;
}
