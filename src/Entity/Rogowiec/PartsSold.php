<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\PartsSoldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: PartsSoldRepository::class)]
#[Table(name: 'rogowiec_parts_sold')]
#[Index(name: "source_idx", fields: ["source"])]
class PartsSold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 50)]
    private ?string $rodzaj = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $dokument_numer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $klient_rodzaj = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $sprzedaz_wartosc_netto = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $zakup_wartosc_netto = null;

    #[ORM\Column(length: 100)]
    private ?string $indeks = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nazwa = null;

    #[ORM\Column(length: 100)]
    private ?string $producent = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $segment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ilosc = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_klienta = null;

    #[ORM\Column(length: 5)]
    private ?string $klasyfikacja_sprzedaz = null;

    #[ORM\Column(length: 100)]
    private ?string $pracownik = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $platnosci = null;
}