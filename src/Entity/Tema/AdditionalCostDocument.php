<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: WzDocumentRepository::class)]
#[Table(name: 'tema_additional_cost_document')]
#[UniqueConstraint(name: "source_docId_idx", fields: ["source", "doc_id"])]
#[Index(name: "source_issuedate_idx", fields: ["source", "issueDate"])]
#[Index(name: "source_vin_idx", fields: ["source", "vin"])]
class AdditionalCostDocument
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $doc_id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $name = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $entryDate = null;
    
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $customerId = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $payment_method = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $netValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $grossValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $taxAmount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;
}
