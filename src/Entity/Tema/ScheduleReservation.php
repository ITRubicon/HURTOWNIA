<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_schedule_reservation')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "source_resource_code_idx", fields: ["source", "resourceCode"])]
class ScheduleReservation
{
    #[ORM\Id]
    #[ORM\Column(length: 12)]
    private ?string $id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $repairOrderNumber = null;
    
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $customer = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $customerName = null;
}