<?php

namespace App\Command\Rogowiec;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\Rogowiec\AgeingCarsRepository;
use App\Repository\SourceAuthRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rogowiec:ageing:cars',
    description: 'Pobiera dane wiekowania samochodów. Pobiera tylko na dzień dzisiejszy. Parametry: nazwa api (opcja)',
)]
class AgeingCarsCommand extends BaseApiCommand
{
    private $repo;
    protected $producerName = 'Rogowiec';

    public function __construct(AgeingCarsRepository $repo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
        ;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->repo->setSource($api);
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
    }

    protected function clearTable()
    {
        $this->repo->clearTable();
    }
}
