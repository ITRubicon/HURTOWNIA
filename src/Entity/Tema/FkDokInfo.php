<?php

namespace App\Entity\Tema;

use App\Repository\FkCechyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FkCechyRepository::class)]
#[Table(name: 'tema_fk_dok_info')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "fv_kod_cechy_wartosc_idx", fields: ["faktura", "kod_cechy", "wartosc"])]
class FkDokInfo
{    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $faktura = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_cechy = null;

    #[ORM\Column(length: 100)]
    private ?string $wartosc = null;

    #[ORM\Column]
    private ?int $recno = null;

    #[ORM\Column(length: 50)]
    private ?string $timestamp = null;

    #[ORM\Column(length: 30)]
    private ?string $source = null;

    #[ORM\Column()]
    private ?int $rok = null;
}
