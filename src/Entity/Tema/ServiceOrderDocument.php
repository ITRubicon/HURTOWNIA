<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: WzDocumentRepository::class)]
#[Table(name: 'tema_service_order_document')]
#[Index(name: "source_openingDate_idx", fields: ["source", "openingDate"])]
#[Index(name: "source_closingDate_idx", fields: ["source", "closingDate"])]
class ServiceOrderDocument
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $openingDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $closingDate = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?\DateTimeInterface $isCanceled = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $netValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $grossValue = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $stockStatus = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $customerId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

}
