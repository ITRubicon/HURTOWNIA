<?php

namespace App\Entity\Tema;

use App\Repository\FkCechyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

#[ORM\Entity(repositoryClass: FkCechyRepository::class)]
#[Table(name: 'tema_fk_kontrahenci')]
class FkKontrahenci
{
    #[ORM\Id]
    #[ORM\Column(length: 30)]
    private ?string $source = null;

    #[ORM\Id]
    #[ORM\Column(length: 20)]
    private ?string $kontrahent_id = null;

    #[ORM\Column]
    private ?int $row_id = null;

    #[ORM\Column(length: 50)]
    private ?string $vat_id = null;
    
    #[ORM\Column]
    private ?int $is_vat_payer = null;

    #[ORM\Column(length: 50)]
    private ?string $payer_erp_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    private ?string $post_code = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    private ?string $country = null;

    #[ORM\Column(length: 50)]
    private ?string $phone_number = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $payment_days = null;

    #[ORM\Column(length: 50)]
    private ?string $payment_method = null;

    #[ORM\Column(length: 100)]
    private ?string $grupa = null;

    #[ORM\Column]
    private ?int $route_id = null;
    
    #[ORM\Column]
    private ?int $route_order = null;
    
    #[ORM\Column]
    private ?int $debt_limit = null;
    
    #[ORM\Column]
    private ?int $salesman_id = null;
    
    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(length: 50)]
    private ?string $delivery_note_type = null;

    #[ORM\Column(length: 1)]
    private ?string $activity = null;

    #[ORM\Column]
    private ?int $check_maximum_discount = null;

    #[ORM\Column]
    private ?int $priority = null;

    #[ORM\Column]
    private ?int $separate_invoice = null;
    
    #[ORM\Column(length: 300)]
    private ?string $region = null;
    
    #[ORM\Column(length: 50)]
    private ?string $beer_sale_permission_no = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $beer_sale_permission_date = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $beer_sale_permission_expiration_date = null;
    
    #[ORM\Column(length: 50)]
    private ?string $wine_sale_permission_no = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $wine_sale_permission_date = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $wine_sale_permission_expiration_date = null;
    
    #[ORM\Column(length: 50)]
    private ?string $vodka_sale_permission_no = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vodka_sale_permission_date = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vodka_sale_permission_expiration_date = null;
}
