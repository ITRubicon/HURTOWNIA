<?php

namespace App\Entity\Rogowiec;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_invoice_archive')]
#[Index(name: "source_id_idx", fields: ["source", "id"])]
#[Index(name: "source_number_idx", fields: ["source", "number"])]
#[Index(name: "source_correctedNo_idx", fields: ["source", "correctedNo"])]
class InvoiceArchive
{
    // #[ORM\Id]
    #[ORM\Column(nullable: true)]
    private ?int $id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $correctedNo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $worker = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $docDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $saleDate = null;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private ?string $netValue = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private ?string $grossValue = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $customerCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $platnosci = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $metodaPlatnosci = null;

    #[ORM\Column(length: 18, nullable: true)]
    private ?string $statusPlatnosci = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $fetchDate = null;
}