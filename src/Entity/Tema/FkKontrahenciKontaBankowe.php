<?php

namespace App\Entity\Tema;

use App\Repository\FkCechyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FkCechyRepository::class)]
#[Table(name: 'tema_fk_kontrahenci_konta_bankowe')]
#[Index(name: "source_kontrahentId_idx", fields: ["source", "kontrahent_id"])]
class FkKontrahenciKontaBankowe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $source = null;

    #[ORM\Column(length: 20)]
    private ?string $kontrahent_id = null;

    #[ORM\Column(length: 40)]
    private ?string $account_number = null;

    #[ORM\Column(length: 255)]
    private ?string $bank_name = null;
}
