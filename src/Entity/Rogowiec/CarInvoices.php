<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\CarsSoldRepository;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarsSoldRepository::class)]
#[Table(name: 'rogowiec_car_invoices')]
#[UniqueConstraint(name: "car_invoices_unique", columns: ["source", "vin", "fv_numer"])]
#[Index(name: "source_idx", fields: ["source"])]
class CarInvoices
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column]
    private ?int $vehicle_id = null;

    #[ORM\Column(length: 17)]
    private ?string $vin = null;

    #[ORM\Column(length: 50)]
    private ?string $fv_numer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fv_data = null;
    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fv_data_wplyw = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wartosc = null;

    #[ORM\Column(length: 10)]
    private ?string $faktura_rodzaj = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $kod_typu_korekty = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $typ_korekty = null;
}