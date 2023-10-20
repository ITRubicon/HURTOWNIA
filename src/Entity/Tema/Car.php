<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_car')]
#[Index(name: "source_idx", fields: ["source"])]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column]
    private ?int $row_id = null;

    #[ORM\Column]
    private ?int $car_id = null;

    #[ORM\Column]
    private ?int $brandId = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $brandName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $modelCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $modelName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $bodyColor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $manufacturingYear = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $userId = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $ownerId = null;

    #[ORM\Column(length: 17, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $registrationNo = null;

    #[ORM\Column(nullable: true)]
    private ?int $mileage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $firstRegistrationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextInspectionDate = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $renaultOrderNumber = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ipsNumber = null;

    #[ORM\Column]
    private ?int $typeApproval = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $vehicleKind = null;

}
