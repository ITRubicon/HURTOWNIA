<?php

namespace App\Entity\Tema;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: ServiceOrderEndDocumentRepository::class)]
#[Table(name: 'tema_service_order_end_documents')]
#[Index(name: "source_docid_idx", fields: ["source", "doc_id"])]
class ServiceOrderEndDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $doc_id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $end_doc_id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

}
