<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_schedule_resources_availability')]
#[Index(name: "source_resource_code_idx", fields: ["source", "resourceCode"])]
#[Index(name: "day_idx", fields: ["day"])]
class ScheduleResourcesAvailability
{
    #[ORM\Id]
    #[ORM\Column(length: 17)]
    private ?string $resourceCode = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Id]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $day = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $start = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $end = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $hours = null;
}