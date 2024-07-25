<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderItemRepository::class)]
#[Table(name: 'tema_service_order_item_package_item')]
#[ORM\Index(name: "tema_ServiceOrderItemPackageItem_idx", fields: ["source", "doc_id", "item_product_id", "item_product_code", "product_id", "product_code"])]
class ServiceOrderItemPackageItem
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
    private ?int $item_product_id = null;

    #[ORM\Column(length: 255)]
    private ?string $item_product_code = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $product_id = null;

    #[ORM\Column(length: 255)]
    private ?string $product_code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 4)]
    private ?string $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $netPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $taxRate = null;

    #[ORM\Column]
    private ?bool $isExempt = null;

    #[ORM\Column(length: 30)]
    private ?string $gdnName = null;

    #[ORM\Column(length: 12)]
    private ?string $gdnId = null;
}
