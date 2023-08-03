<?php

namespace App\Entity\Tema;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderCarRepository::class)]
#[Table(name: 'tema_service_order_car')]
#[Index(name: "source_docid_idx", fields: ["source", "doc_id"])]
class ServiceOrderCar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $doc_id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $registrationNo = null;

    #[ORM\Column(nullable: true)]
    private ?int $brandId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(nullable: true)]
    private ?int $mileage = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $userId = null;

}
