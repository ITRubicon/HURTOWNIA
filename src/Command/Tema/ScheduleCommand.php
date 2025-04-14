<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\ScheduleRepository;
use App\Repository\Tema\ScheduleReservationRepository;
use App\Repository\Tema\ScheduleResourcesAvailabilityRepository;
use App\Repository\Tema\ScheduleResourcesRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:schedule',
    description: 'Pobiera zapisy z grafika rezerwacji pracwoników',
)]
class ScheduleCommand extends BaseApiCommand
{
    protected $producerName = 'Tema';
    private const HANDLED_API = 'https://1099dcrm.tema.com.pl';

    public function __construct(
        private ScheduleRepository $repo,
        private ScheduleReservationRepository $reservationRepo,
        private ScheduleResourcesRepository $resourcesRepo,
        private ScheduleResourcesAvailabilityRepository $resourcesAvailabilityRepo,
        protected SourceAuthRepository $apiAuthRepo,
        protected ApiFetchErrorRepository $errorRepo
    ) {
        parent::__construct($apiAuthRepo, $errorRepo);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('dateFrom', InputArgument::REQUIRED, 'Data od')
            ->addArgument('dateTo', InputArgument::REQUIRED, 'Data do')
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        if ($api->getBaseUrl() !== self::HANDLED_API) {
            $io->warning(sprintf('To api nie jest obsługiwane: %s', $api->getBaseUrl()));
            return;
        }

        // Pobieramy do przodu, bo wyliczenia są robione do końca miesiąca
        $dateTo = date('Y-m-t', strtotime('+ 1 month', strtotime($this->dateTo)));

        $this->repo->setSource($api);
        $this->repo->setDateFrom($this->dateFrom);
        $this->repo->setDateTo($dateTo);

        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $this->resourcesRepo->setSource($api);
        $this->resourcesRepo->setDateFrom($this->dateFrom);
        $this->resourcesRepo->setDateTo($dateTo);
        $this->resourcesAvailabilityRepo->setSource($api);
        $this->resourcesRepo->addRelatedRepository($this->resourcesAvailabilityRepo, 'availability');

        $fetchedRows = $this->resourcesRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $this->reservationRepo->setSource($api);
        $this->reservationRepo->setDateFrom($this->dateFrom);
        $this->reservationRepo->setDateTo($dateTo);

        $fetchedRows = $this->reservationRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
    }

    protected function clearTable()
    {
        $this->repo->clearTable();
        $this->reservationRepo->clearTable();
        $this->resourcesRepo->clearTable();
        $this->resourcesAvailabilityRepo->clearTable();
    }
}
