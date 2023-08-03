<?php

namespace App\Entity\Tema;

use App\Repository\RodoCustomerContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: RodoCustomerContactRepository::class)]
#[Table(name: 'tema_contact_rodo')]
#[Index(name: "source_idx", fields: ["source", "customer_id", "contact_id"])]
class RodoCustomerContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 10)]
    private ?string $customer_id = null;

    #[ORM\Column(length: 6)]
    private ?string $contact_id = null;

    #[ORM\Column]
    private ?bool $value = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modifyDate = null;

    #[ORM\Column]
    private ?int $typeId = null;

}
