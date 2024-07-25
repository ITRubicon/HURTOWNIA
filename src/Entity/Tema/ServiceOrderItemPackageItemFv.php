<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderItemRepository::class)]
#[Table(name: 'tema_service_order_item_package_item_fv')]
#[ORM\Index(name: "tema_ServiceOrderItemPackageItemFv_idx", fields: ["source", "doc_id", "item_product_id", "item_product_code", "product_id", "product_code"])]
class ServiceOrderItemPackageItemFv
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

    #[ORM\Column]
    private ?int $product_id = null;

    #[ORM\Column(length: 255)]
    private ?string $product_code = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $name = null;
}
