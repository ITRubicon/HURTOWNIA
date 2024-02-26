<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: WzDocumentRepository::class)]
#[Table(name: 'tema_fv_document')]
#[Index(name: "source_issuedate_idx", fields: ["source", "issueDate"])]
#[Index(name: "source_vin_idx", fields: ["source", "vin"])]
#[Index(name: "source_docId_idx", fields: ["source", "doc_id"])]
class SaleInvoiceDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $doc_id = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $customerId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $netValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $grossValue = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $order_id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $order_name = null;
    
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $payment_method = null;

    #[ORM\Column(nullable: true)]
    private ?int $operator_code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $who = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $forWhom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $branch = null;
}
