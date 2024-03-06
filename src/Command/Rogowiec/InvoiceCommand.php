<?php

namespace App\Command\Rogowiec;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\Rogowiec\CustomerRepository;
use App\Repository\Rogowiec\InvoiceRepository;
use App\Repository\SourceAuthRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rogowiec:invoice',
    description: 'Pobiera dane faktur. Parametry: data od (wymagane), data do (wymagane), nazwa api (opcja)',
)]
class InvoiceCommand extends BaseApiCommand
{
    private $repo;
    private $customerRepo;
    protected $producerName = 'Rogowiec';

    public function __construct(InvoiceRepository $repo, CustomerRepository $customerRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
        $this->customerRepo = $customerRepo;
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
        $this->customerRepo->setSource($api);
        $this->repo->setDateFrom($this->cmdArgs['dateFrom']);
        $this->repo->setDateTo($this->cmdArgs['dateTo']);
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $customersCount = count($fetchedRows['customersCodes']);
        if ($customersCount > 0) {
            $io->info(sprintf('Pobrano %s klientów z faktur', $customersCount));
            $this->customerRepo->fetchByCode($fetchedRows['customersCodes']);
            $this->customerRepo->archive();
        }
        unset($fetchedRows['customersCodes']);
        
        $io->info('Archiwum faktur');
        $this->repo->archive();
    }

    protected function clearTable()
    {
        $this->repo->clearTable();
        $this->customerRepo->clearTable();
    }
}
