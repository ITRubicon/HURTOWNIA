<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\CarOrderItemRepository;
use App\Repository\Tema\CarOrderRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:order',
    description: 'Pobiera zamówienia samochodów i poycje z nich. Parametry: nazwa api (opcja)',
)]
class CarOrderCommand extends BaseApiCommand
{
    private $orderRepo;
    private $itemRepo;
    protected $producerName = 'Tema';

    public function __construct(CarOrderRepository $orderRepo, CarOrderItemRepository $itemRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->orderRepo = $orderRepo;
        $this->itemRepo = $itemRepo;
    }
    
    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)')
            ->addArgument('dateFrom', InputArgument::OPTIONAL, 'Data początkowa (domyślnie pierwszy dzień miesiąca', date('Y-m-01'))
            ->addArgument('dateTo', InputArgument::OPTIONAL, 'Data końcowa (domyślnie dzień dzisiejszy)', date('Y-m-d'));
        ;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->orderRepo->setDateFrom($this->dateFrom);
        $this->orderRepo->setDateTo($this->dateTo);
        $this->orderRepo->setSource($api);
        $this->itemRepo->setSource($api);
        $fetchedRows = $this->orderRepo->fetch();
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
        $this->orderRepo->clearTable();
        $this->itemRepo->clearTable();
    }
}