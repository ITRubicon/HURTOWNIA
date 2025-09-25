<?php

namespace App\Entity\Tema;

use App\Repository\FkCechyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FkCechyRepository::class)]
#[Table(name: 'tema_fk_kont')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "konto_idx", fields: ["konto"])]
class FkKont
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $konto = null;

    #[ORM\Column(length: 255)]
    private ?string $nazwa = null;

    #[ORM\Column(length: 255)]
    private ?string $nazwa2 = null;

    #[ORM\Column(length: 50)]
    private ?string $rodzaj = null;

    #[ORM\Column(length: 300)]
    private ?string $kontrah = null;

    #[ORM\Column(length: 50)]
    private ?string $kod_kraj = null;

    #[ORM\Column(length: 50)]
    private ?string $nip = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $bo_winien = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $bo_ma = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn01 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn02 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn03 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn04 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn05 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn06 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn07 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn08 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn09 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn10 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn11 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $wn12 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma01 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma02 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma03 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma04 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma05 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma06 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma07 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma08 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma09 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma10 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma11 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $ma12 = null;

    #[ORM\Column(length: 30)]
    private ?string $waluta = null;

    #[ORM\Column]
    private ?int $lp = null;

    #[ORM\Column]
    private ?int $alert = null;

    #[ORM\Column]
    private ?int $opis = null;

    #[ORM\Column(length: 50)]
    private ?string $kod_anal = null;

    #[ORM\Column(length: 50)]
    private ?string $kod_zkonta = null;

    #[ORM\Column]
    private ?int $recno = null;

    #[ORM\Column(length: 50)]
    private ?string $timestamp = null;

    #[ORM\Column(length: 30)]
    private ?string $source = null;

    #[ORM\Column()]
    private ?int $rok = null;
}
