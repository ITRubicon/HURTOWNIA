<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\AgeingProductionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: AgeingProductionRepository::class)]
#[Table(name: 'rogowiec_ageing_production')]
#[Index(name: "source_idx", fields: ["source"])]
class AgeingProduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 50)]
    private ?string $zlecenie_nr = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $zlecenie_data = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $indeks = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    private ?string $ilosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $cena_zakup = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wartosc_zakup = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $czesci_nazwa = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $czesci_nazwa_2 = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $nr_magazynowy = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dokument_magazynowy_data = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $kod_interwencja = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $platnosc_kanal = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $rodzaj = null;

    #[ORM\Column(length: 10)]
    private ?string $jednostka = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $dokument_sumbol = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_klienta = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $klient_nazwa = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $odbierajacy = null;

    #[ORM\Column(length: 100)]
    private ?string $prowadzacy = null;

    #[ORM\Column(length: 17)]
    private ?string $vin = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $rejestracja_nr = null;
}