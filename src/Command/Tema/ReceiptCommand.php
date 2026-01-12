<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\ReceiptCorrectionItemRepository;
use App\Repository\Tema\ReceiptCorrectionRepository;
use App\Repository\Tema\ReceiptItemRepository;
use App\Repository\Tema\ReceiptRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:receipt',
    description: 'Pobiera paragony',
)]
class ReceiptCommand extends BaseApiCommand
{
    private $repo;
    private $itemRepo;
    private $correctionRepo;
    private $correctionItemRepo;
    protected $producerName = 'Tema';

    public function __construct(
        ReceiptRepository $repo,
        ReceiptItemRepository $itemRepo,
        ReceiptCorrectionRepository $correctionRepo,
        ReceiptCorrectionItemRepository $correctionItemRepo,
        SourceAuthRepository $apiAuthRepo,
        ApiFetchErrorRepository $errorRepo
    )
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
        $this->itemRepo = $itemRepo;
        $this->correctionRepo = $correctionRepo;
        $this->correctionItemRepo = $correctionItemRepo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->itemRepo->setSource($api);
        $this->repo->setSource($api);
        $this->repo->addRelatedRepository($this->itemRepo, 'items');

        $this->correctionItemRepo->setSource($api);
        $this->correctionRepo->setSource($api);
        $this->correctionRepo->addRelatedRepository($this->correctionItemRepo, 'items');

        $this->repo->removeForCurrentSource();
        $this->itemRepo->removeForCurrentSource();

        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
        unset($fetchedRows);

        $this->correctionRepo->removeForCurrentSource();
        $this->correctionItemRepo->removeForCurrentSource();

        $fetchedRows = $this->correctionRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów korekt", $fetchedRows['fetched']));
        unset($fetchedRows);
    }

    protected function clearTable() {}
}
