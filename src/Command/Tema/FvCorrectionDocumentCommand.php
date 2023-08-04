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
    name: 'tema:fv:corrections',
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
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)');
        ;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->docRepo->setSource($api);
        $this->itemRepo->setSource($api);
        $fetchedRows = $this->docRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $itemsCount = count($fetchedRows['items']);
        if ($itemsCount > 0) {
            $io->info(sprintf('Pobrano %s pozycji z dokumentów', $itemsCount));
            $this->itemRepo->saveItems($fetchedRows['items']);
        }
        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->docRepo->clearTable();
        $this->itemRepo->clearTable();
    }
}