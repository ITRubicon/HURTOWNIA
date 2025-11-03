<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\DmsUserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:user',
    description: 'Pobiera listę pracowników. Parametry: nazwa api (opcja)',
)]
class DmsUserCommand extends BaseApiCommand
{
    private $repo;
    protected $producerName = 'Tema';

    public function __construct(DmsUserRepository $repo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->repo->setSource($api);
        $this->repo->removeForCurrentSource();
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
    }

    protected function clearTable() {}
}
