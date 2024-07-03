<?php

namespace App\Entity\Tema;

use App\Repository\FkCechyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FkCechyRepository::class)]
#[Table(name: 'tema_fk_cechy')]
#[Index(name: "source_idx", fields: ["source"])]
#[Index(name: "kod_cechy_wartosc_idx", fields: ["kod_cechy", "wartosc"])]
#[Index(name: "wpis_id_idx", fields: ["wpis_id"])]
class FkCechy
{    
    #[ORM\Id]
    #[ORM\Column()]
    private ?int $rok = null;
    
    #[ORM\Id]
    #[ORM\Column]
    private ?int $recno = null;

    #[ORM\Column(length: 12)]
    private ?string $wpis_id = null;

    #[ORM\Column(length: 10)]
    private ?string $kod_cechy = null;

    #[ORM\Column(length: 100)]
    private ?string $wartosc = null;

    #[ORM\Column(length: 50)]
    private ?string $timestamp = null;

    #[ORM\Column(length: 30)]
    private ?string $source = null;
}
