<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\ServiceOrderCarRepository;
use App\Repository\Tema\ServiceOrderDocumentRepository;
use App\Repository\Tema\ServiceOrderEndDocumentRepository;
use App\Repository\Tema\ServiceOrderItemRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:service',
    description: 'Pobiera zlecenia serwisowe pozycje z nich. Parametry: nazwa api (opcja)',
)]
class ServiceOrderDocumentCommand extends BaseApiCommand
{
    private $docRepo;
    private $itemRepo;
    private $endDocRepo;
    private $carRepo;
    protected $producerName = 'Tema';

    public function __construct(
        ServiceOrderDocumentRepository $docRepo,
        ServiceOrderItemRepository $itemRepo,
        ServiceOrderEndDocumentRepository $endDocRepo,
        ServiceOrderCarRepository $carRepo,
        SourceAuthRepository $apiAuthRepo,
        ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->docRepo = $docRepo;
        $this->itemRepo = $itemRepo;
        $this->endDocRepo = $endDocRepo;
        $this->carRepo = $carRepo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
        ;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->itemRepo->setSource($api);
        $this->endDocRepo->setSource($api);
        $this->carRepo->setSource($api);
        $this->docRepo->setSource($api);
        $this->docRepo->addRelatedRepository($this->itemRepo, 'items');
        $this->docRepo->addRelatedRepository($this->endDocRepo, 'endDocs');
        $this->docRepo->addRelatedRepository($this->carRepo, 'cars');

        $fetchedRows = $this->docRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->docRepo->clearTable();
        $this->itemRepo->clearTable();
        $this->endDocRepo->clearTable();
        $this->carRepo->clearTable();
    }
}