<?php

namespace App\Entity\Tema;

use App\Repository\RodoTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: RodoTypeRepository::class)]
#[Table(name: 'tema_car_stock')]
#[Index(name: "source_stockid_idx", fields: ["source", "stock_id"])]
class CarStockType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private ?string $stock_id = null;

    #[ORM\Column(length: 50)]
    private ?int $car_id = null;
}
