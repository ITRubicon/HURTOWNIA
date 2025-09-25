<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\AgeingPartsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: AgeingPartsRepository::class)]
#[Table(name: 'rogowiec_ageing_parts')]
#[Index(name: "source_idx", fields: ["source"])]
class AgeingParts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 50)]
    private ?string $producent = null;

    #[ORM\Column(length: 30)]
    private ?string $indeks = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dostawa_data = null;

    #[ORM\Column(length: 50)]
    private ?string $dokument_nr = null;

    #[ORM\Column(length: 10)]
    private ?string $dokument_symbol = null;

    #[ORM\Column(length: 10)]
    private ?string $jednostka = null;

    #[ORM\Column(length: 255)]
    private ?string $nazwa = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $zastosowanie = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lokalizacja_magazyn = null;

    #[ORM\Column(length: 10)]
    private ?string $dostawca_kod = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dostawca_nazwa = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $segment = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $grupa = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rodzina = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $rodzaj_kod = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $stan_ilosc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wartosc_netto = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $ilosc_rezerwacja = null;

    #[ORM\Column]
    private ?int $id_org = null;
}