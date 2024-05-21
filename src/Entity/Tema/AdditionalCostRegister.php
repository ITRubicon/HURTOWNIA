<?php

namespace App\Entity\Tema;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: WzDocumentRepository::class)]
#[Table(name: 'tema_additional_cost_register')]
#[Index(name: "source_issuedate_idx", fields: ["source", "id"])]
class AdditionalCostRegister
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;
}
