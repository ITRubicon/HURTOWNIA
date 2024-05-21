<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\AdditionalCostDocumentItemRepository;
use App\Repository\Tema\AdditionalCostDocumentRepository;
use App\Repository\Tema\AdditionalCostRegisterRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:additional-cost',
    description: 'Pobiera faktury kosztów dodatkowych (np. zabudowy). Parametry: nazwa api (opcja)',
)]
class AdditionalCostCommand extends BaseApiCommand
{
    private $registerRepo;
    private $docRepo;
    private $itemRepo;
    protected $producerName = 'Tema';

    public function __construct(AdditionalCostRegisterRepository $registerRepo, AdditionalCostDocumentRepository $docRepo, AdditionalCostDocumentItemRepository $itemRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->registerRepo = $registerRepo;
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
        $this->registerRepo->setSource($api);
        $this->docRepo->setSource($api);
        $this->itemRepo->setSource($api);

        $fetched = $this->registerRepo->fetch();
        $io->info(sprintf("Pobrano %s rejestrów", $fetched['fetched']));

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
        $this->registerRepo->clearTable();
        $this->docRepo->clearTable();
        $this->itemRepo->clearTable();
    }
}