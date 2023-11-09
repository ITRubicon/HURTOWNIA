<?php

namespace App\Entity\Rogowiec;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_invoice_archive')]
#[Index(name: "source_idx", fields: ["source", "number"])]
// #[Index(name: "source_id_idx", fields: ["source", "id"])]
// #[UniqueConstraint("source_id_un", columns: ["source", "id"])]
#[UniqueConstraint("source_number_un", columns: ["source", "number"])]
class InvoiceArchive
{
    // #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $number = null;

    #[ORM\Column(length: 50)]
    private ?string $correctedNo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $worker = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $docDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $saleDate = null;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $currency = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private ?string $netValue = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private ?string $grossValue = null;
}
