<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderItemRepository::class)]
#[Table(name: 'tema_service_order_item')]
#[Index(name: "source_docid_idx", fields: ["source", "doc_id"])]
class ServiceOrderDocumentItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12)]
    private ?string $doc_id = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $productCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 4)]
    private ?string $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $netPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $taxRate = null;

    #[ORM\Column]
    private ?bool $isExempt = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $gdnId = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $gdnName = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $invoiceNames = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $repairOrderItemMechanics = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $packageItems = null;
}
