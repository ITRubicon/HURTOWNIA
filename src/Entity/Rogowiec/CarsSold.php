<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\CarsSoldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarsSoldRepository::class)]
#[Table(name: 'rogowiec_cars_sold')]
#[Index(name: "source_idx", fields: ["source"])]
class CarsSold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pracownik = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $zamawiajacy = null;

    #[ORM\Column(length: 50)]
    private ?string $fv_numer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fv_data = null;

    #[ORM\Column(length: 17)]
    private ?string $vin = null;

    #[ORM\Column(length: 50)]
    private ?string $marka = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wersja = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $nr_wydanie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_wydanie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_wydanie_klient = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $sprzedaz_netto = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $korekty_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $rezerwy_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $zcs_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $kks_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $zakup_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $usterki_koszty_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $usterki_platne_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prowizja_kredyt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $zaliczki = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_nabywca = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_odbiorca = null;

    #[ORM\Column(length: 5)]
    private ?string $klasyfikacja_sprzedaz = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_zamowienie = null;

    #[ORM\Column(length: 400, nullable: true)]
    private ?string $platnosci = null;
}