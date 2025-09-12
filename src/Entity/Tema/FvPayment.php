<?php

namespace App\Entity\Tema;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'tema_fv_payment')]
#[ORM\UniqueConstraint(name: "source_docId_paymentDocumentId_customerId_uniq", fields: ["source", "docId", "paymentDocumentId", "customerId"])]
#[ORM\Index(name: "source_docId_idx", fields: ["source", "docId"])]
#[ORM\Index(name: "source_customerId_idx", fields: ["source", "customerId"])]
class FvPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $docId = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $documentPaymentStatus = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $documentValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $documentPaymentValue = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $customerId = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $paymentDocumentId = null;
}
