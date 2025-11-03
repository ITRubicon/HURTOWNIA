<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\MmItemRepository;
use App\Repository\Tema\MmRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:mm',
    description: 'Pobiera dokumenty MM i poycje z nich. Parametry: nazwa api (opcja), dateFrom, dateTo [domyślnie bieżący miesiąc]',
)]
class MmDocumentCommand extends BaseApiCommand
{
    private $mmRepo;
    private $itemRepo;
    protected $producerName = 'Tema';

    public function __construct(MmRepository $mmRepo, MmItemRepository $itemRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->mmRepo = $mmRepo;
        $this->itemRepo = $itemRepo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL')
            ->addArgument('dateFrom', InputArgument::OPTIONAL, 'Data początkowa (domyślnie pierwszy dzień miesiąca', date('Y-m-01'))
            ->addArgument('dateTo', InputArgument::OPTIONAL, 'Data końcowa (domyślnie dzień dzisiejszy)', date('Y-m-d'));;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->itemRepo->setSource($api);
        $this->mmRepo->setDateFrom($this->dateFrom);
        $this->mmRepo->setDateTo($this->dateTo);
        $this->mmRepo->setSource($api);
        $this->mmRepo->addRelatedRepository($this->itemRepo, 'items');

        $this->mmRepo->removeForCurrentSource();
        $this->itemRepo->removeForCurrentSource();

        $fetchedRows = $this->mmRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        unset($fetchedRows);
    }

    protected function clearTable() {}
}
