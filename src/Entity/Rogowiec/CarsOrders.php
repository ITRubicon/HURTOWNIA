<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\CarsOrdersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarsOrdersRepository::class)]
#[Table(name: 'rogowiec_cars_orders')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "vin_idx", fields: ["vin"])]
#[Index(name: "source_idZam_idx", fields: ["source", "id_zam"])]
class CarsOrders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_zam = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 50)]
    private ?string $numer_zam = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data = null;

    #[ORM\Column(length: 50)]
    private ?string $marka = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $opis_samochodu = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_klienta = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $wartosc_brutto = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_plan_odbior = null;

    #[ORM\Column(length: 100)]
    private ?string $stan_zamowienia = null;

    #[ORM\Column(length: 50)]
    private ?string $status_zamowienia = null;

    #[ORM\Column(length: 3)]
    private ?string $zamkniete = null;

    #[ORM\Column(length: 100)]
    private ?string $sprzedawca = null;

    #[ORM\Column(length: 17, nullable: true)]
    private ?string $vin = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_samochodu = null;

    #[ORM\Column(nullable: true, length: 20)]
    private ?string $typ_zamowienia = null;

    #[ORM\Column(nullable: true, length: 100)]
    private ?string $rodzaj_zamowienia = null;

    #[ORM\Column(nullable: true, length: 100)]
    private ?string $wyroznik_zamowienia = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $komentarz = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $uwagi = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notatka1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notatka2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notatka3 = null;
}