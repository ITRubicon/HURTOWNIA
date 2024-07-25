<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderItemRepository::class)]
#[Table(name: 'tema_service_order_item_mechanic')]
#[ORM\Index(name: "tema_source_docId_productId_productCode_invoiveName_idx", fields: ["source", "doc_id", "product_id", "product_code", "userId"])]
class ServiceOrderDocumentItemMechanic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12)]
    private ?string $doc_id = null;

    #[ORM\Column]
    private ?int $product_id = null;

    #[ORM\Column]
    private ?string $product_code = null;

    #[ORM\Column(length: 10)]
    private ?string $userId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 1)]
    private ?string $manHour = null;

}
