<?php

namespace App\Entity\Tema;

use App\Repository\RodoTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: RodoTypeRepository::class)]
#[Table(name: 'tema_typ_rodo')]
#[Index(name: "source_idx", fields: ["source"])]
class RodoType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column]
    private ?int $type_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 10)]
    private ?string $channel = null;

    #[ORM\Column(nullable: true)]
    private array $scope = [];

}
