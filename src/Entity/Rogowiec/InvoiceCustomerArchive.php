<?php

namespace App\Entity\Rogowiec;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[Table(name: 'rogowiec_invoice_customer_archive')]
#[Index(name: "source_invoiceId_idx", fields: ["source", "invoice_id"])]
#[UniqueConstraint("source_invoiceId_kind_un", columns: ["source", "invoice_id", "customer_kind", "customer_code"])]
class InvoiceCustomerArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $invoiceId = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $customer_kind = null;

    #[ORM\Column(length: 10)]
    private ?string $customerCode = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $taxNumber = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $personalId = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $businesNumber = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $kind = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $fetchDate = null;
}
