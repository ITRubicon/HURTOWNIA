<?php

namespace App\Entity\Tema;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Table(name: 'tema_dms_user')]
#[Index(name: "source_idx", fields: ["source"])]
class DmsUser
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $rowId = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fullName = null;
}
