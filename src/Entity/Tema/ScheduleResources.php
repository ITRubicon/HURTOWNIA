<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_schedule_resources')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "source_resource_code_idx", fields: ["source", "resourceCode"])]
class ScheduleResources
{
    #[ORM\Id]
    #[ORM\Column(length: 6)]
    private ?string $id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 17)]
    private ?string $resourceCode = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column(type: Types::JSON)]
    private array $warehouseIds = [];
}