<?php

namespace App\Entity\Rogowiec;

use App\Repository\Rogowiec\OrgUnitRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: OrgUnitRepository::class)]
#[Table(name: 'rogowiec_org_unit')]
#[Index(name: "source_idx", fields: ["source"])]
class OrgUnit
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
    private ?string $jedn_org_id = null;

    #[ORM\Column(length: 12)]
    private ?string $jedn_org_kod = null;

    #[ORM\Column(length: 150)]
    private ?string $nazwa = null;

    #[ORM\Column(length: 150)]
    private ?string $rodzaj = null;

    #[ORM\Column(length: 5)]
    private ?string $oddzial = null;

    #[ORM\Column(length: 15)]
    private ?string $id_zasob = null;
}