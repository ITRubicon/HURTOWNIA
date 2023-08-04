<?php

namespace App\Command\Rogowiec;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\Rogowiec\ServiceSoldRepository;
use App\Repository\SourceAuthRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rrogowiec:service:sold',
    description: 'Pobiera dane usług serwisowych. Parametry: data od (wymagane), data do (wymagane), nazwa api (opcja)',
)]
class ServiceSoldCommand extends BaseApiCommand
{
    private $repo;
    protected $producerName = 'Rogowiec';

    public function __construct(ServiceSoldRepository $repo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('dateFrom', InputArgument::REQUIRED, 'Data od')
            ->addArgument('dateTo', InputArgument::REQUIRED, 'Data do')
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->repo->setSource($api);
        $this->repo->setDateFrom($this->cmdArgs['dateFrom']);
        $this->repo->setDateTo($this->cmdArgs['dateTo']);
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
    }

    protected function clearTable()
    {
        $this->repo->clearTable();
    }
}
