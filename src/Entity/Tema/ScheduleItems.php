<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_schedule_items')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "source_warehouse_idx", fields: ["source", "warehouseId"])]
#[Index(name: "date_from_idx", fields: ["dateFrom"])]
#[Index(name: "date_to_idx", fields: ["dateTo"])]
class ScheduleItems
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $relatedReservation = null;

    #[ORM\Column(length: 14, nullable: true)]
    private ?string $reservationId = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $warehouseId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateFrom = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateTo = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastModifiedByOperator = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $createdByOperator = null;

    #[ORM\Column(length: 11)]
    private ?string $type = null;

    #[ORM\Column(length: 80)]
    private ?string $resource = null;
}