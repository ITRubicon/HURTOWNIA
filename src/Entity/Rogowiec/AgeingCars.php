<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\AgeingCarsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: AgeingCarsRepository::class)]
#[Table(name: 'rogowiec_ageing_cars')]
#[Index(name: "source_idx", fields: ["source"])]
class AgeingCars
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 100)]
    private ?string $oddzial = null;

    #[ORM\Column(length: 15)]
    private ?string $jednostka_org_kod = null;

    #[ORM\Column(length: 200)]
    private ?string $jednostka_org_nazwa = null;

    #[ORM\Column(length: 50)]
    private ?string $dokument_nr = null;

    #[ORM\Column(length: 10)]
    private ?string $dokument_symbol = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dokument_data = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $auto_wartosc = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $wskaznik_stan = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $auto_rodzaj = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $wydanie_data = null;

    #[ORM\Column(length: 17)]
    private ?string $vin = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $rejestracja_nr = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $rejestracja_data = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $opis = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $dokument_rozchod = null;

    #[ORM\Column(length: 100)]
    private ?string $producent = null;

    #[ORM\Column(length: 100)]
    private ?string $marka = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $przeglad_zero = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $wsk_dok_akc = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $wskaznik_najmu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ostatnia_rezerwacja_data = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rezerwujacy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $klient = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $sprzedawca = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $zamowienie_nr = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $zamowienie_data = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $plan_odbior_data = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zamowienie_opis = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $wskaznik_przekazania = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $odkup_rodzaj = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $odkupujacy = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $fv_zakup = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fv_zakup_data = null;

    #[ORM\Column(length: 300)]
    private ?string $platnosci = null;

    #[ORM\Column]
    private ?int $liczba_dni_stan = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_2 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_3 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_4 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_5 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_6 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $wskaznik_7 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $uwaga_1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $uwaga_2 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $uwaga_3 = null;
}