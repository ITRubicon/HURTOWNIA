<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_car_reserve')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "source_vin_idx", fields: ["source", "vin"])]
class CarReserve
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $rowId = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column]
    private ?int $vehicleId = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $activity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $plannedRealizationDate = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $reserveNumber = null;

    #[ORM\Column(length: 17, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $expectedReserveValue = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $reserveId = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $orderId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;
}
