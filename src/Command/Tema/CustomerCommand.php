<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\CustomerContactRepository;
use App\Repository\Tema\CustomerRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:customer',
    description: 'Pobiera klientów',
)]
class CustomerCommand extends BaseApiCommand
{
    private $repo;
    private $contactRepo;
    protected $producerName = 'Tema';

    public function __construct(CustomerRepository $repo, CustomerContactRepository $contactRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
        $this->contactRepo = $contactRepo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->repo->setSource($api);
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $contactsCount = count($fetchedRows['contacts']);
        if ($contactsCount > 0) {
            $io->info(sprintf('Pobrano %s kontaktów', $contactsCount));
            $this->contactRepo->setSource($api);
            $this->contactRepo->saveContacts($fetchedRows['contacts']);
        }
        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->repo->removeForCurrentSource();
        $this->contactRepo->removeForCurrentSource();
    }
}
