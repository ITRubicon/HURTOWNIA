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
    name: 'tema:car:order',
    description: 'Pobiera zamówienia samochodów i poycje z nich. Parametry: nazwa api (opcja), dateFrom, dateTo [domyślnie bieżący miesiąc]',
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
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->itemRepo->setSource($api);
        $this->orderRepo->setSource($api);

        $this->orderRepo->removeForCurrentSource();
        $this->itemRepo->removeForCurrentSource();

        $this->orderRepo->addRelatedRepository($this->itemRepo, 'items');

        $fetchedRows = $this->orderRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        unset($fetchedRows);
    }

    protected function clearTable() {}
}
