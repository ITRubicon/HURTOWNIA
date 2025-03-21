<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\FvCorrectionDocumentRepository;
use App\Repository\Tema\FvCorrectionItemRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fv:correction',
    description: 'Pobiera faktury sprzedaży i poycje z nich. Parametry: nazwa api (opcja)',
)]
class FvCorrectionDocumentCommand extends BaseApiCommand
{
    private $docRepo;
    private $itemRepo;
    protected $producerName = 'Tema';

    public function __construct(FvCorrectionDocumentRepository $docRepo, FvCorrectionItemRepository $itemRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->docRepo = $docRepo;
        $this->itemRepo = $itemRepo;
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
        $this->docRepo->setSource($api);
        $this->docRepo->addRelatedRepository($this->itemRepo, 'items');

        $fetchedRows = $this->docRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->docRepo->clearTable();
        $this->itemRepo->clearTable();
    }
}