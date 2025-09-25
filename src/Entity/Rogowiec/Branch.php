<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\BranchRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: BranchRepository::class)]
#[Table(name: 'rogowiec_branch')]
#[Index(name: "source_idx", fields: ["source"])]
class Branch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column]
    private ?int $dms_id = null;

    #[ORM\Column(length: 3)]
    private ?string $oddzial_id = null;

    #[ORM\Column(length: 100)]
    private ?string $nazwa = null;

    #[ORM\Column(length: 50)]
    private ?string $miejscowosc = null;

    #[ORM\Column(length: 10)]
    private ?string $id_zaob = null;
}