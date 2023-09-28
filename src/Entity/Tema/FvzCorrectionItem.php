<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: WzItemRepository::class)]
#[Table(name: 'tema_fvz_correction_document_item')]
#[Index(name: "source_docid_idx", fields: ["source", "doc_id"])]
class FvzCorrectionItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $doc_id = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 4)]
    private ?string $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $netPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $taxRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 4)]
    private ?string $correctionValue = null;

    #[ORM\Column(nullable: true)]
    private ?int $carId = null;

    #[ORM\Column(nullable: true)]
    private ?string $vin = null;

    #[ORM\Column]
    private ?bool $isExempt = null;

}
