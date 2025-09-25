<?php

namespace App\Command\Rogowiec;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\Rogowiec\PartsSoldRepository;
use App\Repository\SourceAuthRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rogowiec:parts:sold',
    description: 'Pobiera dane sprzedaży części. Parametry: data od (wymagane), data do (wymagane), nazwa api (opcja)',
)]
class PartsSoldCommand extends BaseApiCommand
{
    private $repo;
    protected $producerName = 'Rogowiec';

    public function __construct(PartsSoldRepository $repo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('dateFrom', InputArgument::REQUIRED, 'Data od')
            ->addArgument('dateTo', InputArgument::REQUIRED, 'Data do')
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->repo->setSource($api);
        $this->repo->setDateFrom($this->cmdArgs['dateFrom']);
        $this->repo->setDateTo($this->cmdArgs['dateTo']);
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
        $io->info("Archiwizacja faktur...");
        $this->repo->archiveInvoices();
    }

    protected function clearTable()
    {
        $this->repo->clearTable();
    }
}
