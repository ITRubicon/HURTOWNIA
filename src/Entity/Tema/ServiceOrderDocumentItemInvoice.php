<?php

namespace App\Entity\Tema;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderItemRepository::class)]
#[Table(name: 'tema_service_order_item_invoice')]
#[ORM\UniqueConstraint(name: "tema_service_order_item_invoice_uq", fields: ["source", "doc_id", "productId", "product_code", "invoiceName"])]
class ServiceOrderDocumentItemInvoice
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
    private ?int $productId = null;

    #[ORM\Column]
    private ?string $product_code = null;

    #[ORM\Column(length: 20)]
    private ?string $invoiceName = null;

}
