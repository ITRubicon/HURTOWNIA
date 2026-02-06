<?php

namespace App\Entity\Tema;

use App\Repository\FkCechyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FkCechyRepository::class)]
#[Table(name: 'tema_fk_zapisy')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "data_dok_idx", fields: ["data_dok"])]
#[Index(name: "konto_idx", fields: ["konto"])]
#[Index(name: "id_wpisu_idx", fields: ["id_wpisu"])]
#[Index(name: "rok_idx", fields: ["rok"])]
class FkWpis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $konto = null;

    #[ORM\Column]
    private int $lp = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $z_dnia = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $data_dok = null;

    #[ORM\Column(length: 100)]
    private ?string $tresc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private float $kwota = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private float $winien = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private float $ma = 0;

    #[ORM\Column(length: 30)]
    private ?string $numer_dow = null;

    #[ORM\Column(length: 30)]
    private ?string $pozyc_dow = null;

    #[ORM\Column(length: 50)]
    private ?string $faktura = null;

    #[ORM\Column(length: 50)]
    private ?string $konto_p = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $termin = null;

    #[ORM\Column]
    private int $nr_pozycji = 0;

    #[ORM\Column]
    private int $id_progr = 0;

    #[ORM\Column]
    private int $id_uz = 0;

    #[ORM\Column]
    private int $kod_oper = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $data_zapis = null;

    #[ORM\Column]
    private int $dziennik = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $waluta_wn = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $waluta_ma = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $waluta_ku = null;

    #[ORM\Column(length: 12)]
    private ?string $id_wpisu = null;

    #[ORM\Column(length: 50)]
    private ?string $dow_podst = null;

    #[ORM\Column(length: 50)]
    private ?string $fk = null;

    #[ORM\Column(length: 50)]
    private int $kod_ak = 0;

    #[ORM\Column(length: 50)]
    private int $kod_ak2 = 0;

    #[ORM\Column(length: 50)]
    private ?string $region = null;

    #[ORM\Column(length: 50)]
    private ?string $kod_kontr = null;

    #[ORM\Column(length: 50)]
    private ?string $id_dokum = null;

    #[ORM\Column(length: 50)]
    private ?string $char_plat = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $uwagi = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $data_wplyw = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wart_dok = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wart_vat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wplata = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wart_wal_d = null;

    #[ORM\Column(length: 50)]
    private ?string $kod_rej = null;

    #[ORM\Column(length: 50)]
    private ?string $fid_dokum = null;

    #[ORM\Column(length: 50)]
    private ?string $cargo = null;

    #[ORM\Column(length: 50)]
    private ?string $oddzial = null;

    #[ORM\Column(length: 50)]
    private ?string $typrozr = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    private ?string $id_platn = null;

    #[ORM\Column(length: 50)]
    private ?string $id_zlec = null;

    #[ORM\Column(length: 50)]
    private ?string $nr_zlecenia = null;

    #[ORM\Column(length: 50)]
    private ?string $id_zrodla = null;

    #[ORM\Column(length: 50)]
    private ?string $id_rozbic = null;

    #[ORM\Column]
    private int $recno = 0;

    #[ORM\Column(length: 50)]
    private ?string $timestamp = null;

    #[ORM\Column(length: 30)]
    private ?string $source = null;

    #[ORM\Column()]
    private ?int $rok = null;
}
