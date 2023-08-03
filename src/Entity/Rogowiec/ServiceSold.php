<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\ServiceSoldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceSoldRepository::class)]
#[Table(name: 'rogowiec_service_sold')]
#[Index(name: "source_idx", fields: ["source"])]
class ServiceSold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private ?string $serwis_nr = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $zlecenie_data = null;

    #[ORM\Column(length: 100)]
    private ?string $marka = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(length: 20)]
    private ?string $zlecenie_nr = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_interwencja = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $silnik_typ = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $rejestracja_nr = null;

    #[ORM\Column(length: 17)]
    private ?string $vin = null;

    #[ORM\Column(nullable: true)]
    private ?int $rok_produkcja = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_klienta = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $klient_nazwa = null;

    #[ORM\Column(length: 10)]
    private ?string $usluga_punkt_sprzedazy = null;

    #[ORM\Column(length: 20)]
    private ?string $platnik = null;

    #[ORM\Column(length: 10)]
    private ?string $dokument_status = null;

    #[ORM\Column(length: 50)]
    private ?string $fv_numer = null;

    #[ORM\Column(length: 100)]
    private ?string $dokument_typ = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fv_data = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $stawka = null;

    #[ORM\Column(length: 30)]
    private ?string $pozycja_typ = null;

    #[ORM\Column(length: 50)]
    private ?string $pozycja_symbol = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $segment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pozycja_nazwa = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $koszt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $rabat_procent = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $rabat_wartosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $marza = null;

    #[ORM\Column(length: 5)]
    private ?string $jednostka = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ilosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $jednostka_czas = null;

    #[ORM\Column(length: 100)]
    private ?string $doradca = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mechanik = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firma_ubezpieczeniowa = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $szkoda_nr = null;

    #[ORM\Column(length: 100)]
    private ?string $fv_wystawiajacy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $zlecenie_czas_zamkniecia = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $przekazal_salon = null;

    #[ORM\Column(length: 5)]
    private ?string $klasyfikacja_sprzedaz = null;

    #[ORM\Column(length: 300)]
    private ?string $platnosci = null;
}
