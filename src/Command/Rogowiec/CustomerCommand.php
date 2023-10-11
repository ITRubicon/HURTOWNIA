<?php

namespace App\Command\Rogowiec;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\Rogowiec\CustomerAddressRepository;
use App\Repository\Rogowiec\CustomerEmailRepository;
use App\Repository\Rogowiec\CustomerPhoneRepository;
use App\Repository\Rogowiec\CustomerRepository;
use App\Repository\Rogowiec\CustomerRodoRepository;
use App\Repository\SourceAuthRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rogowiec:customer',
    description: 'Pobiera dane klientów. Parametry: nazwa api (opcja)',
)]
class CustomerCommand extends BaseApiCommand
{
    private $repo;
    private $addressRepo;
    private $phoneRepo;
    private $emailRepo;
    private $rodoRepo;
    protected $producerName = 'Rogowiec';

    public function __construct(CustomerRepository $repo, CustomerAddressRepository $addressRepo, CustomerPhoneRepository $phoneRepo, CustomerEmailRepository $emailRepo, CustomerRodoRepository $rodoRepo, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->repo = $repo;
        $this->addressRepo = $addressRepo;
        $this->phoneRepo = $phoneRepo;
        $this->emailRepo = $emailRepo;
        $this->rodoRepo = $rodoRepo;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->repo->setSource($api);
        $this->addressRepo->setSource($api);
        $this->phoneRepo->setSource($api);
        $this->emailRepo->setSource($api);
        $this->rodoRepo->setSource($api);
        $fetchedRows = $this->repo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $addressCount = count($fetchedRows['addresses']);
        if ($addressCount > 0) {
            $io->info(sprintf('Pobrano %s adresów', $addressCount));
            $this->addressRepo->saveAddresses($fetchedRows['addresses']);
        }
        unset($fetchedRows['addresses']);

        $phonesCount = count($fetchedRows['phones']);
        if ($phonesCount > 0) {
            $io->info(sprintf('Pobrano %s telefonów', $phonesCount));
            $this->phoneRepo->savePhones($fetchedRows['phones']);
        }
        unset($fetchedRows['phones']);

        $emailsCount = count($fetchedRows['emails']);
        if ($emailsCount > 0) {
            $io->info(sprintf('Pobrano %s emaili', $emailsCount));
            $this->emailRepo->saveEmails($fetchedRows['emails']);
        }
        unset($fetchedRows['emails']);

        $rodoCount = count($fetchedRows['rodo']);
        if ($rodoCount > 0) {
            $io->info(sprintf('Pobrano %s zgód rodo', $rodoCount));
            $this->rodoRepo->saveRodo($fetchedRows['rodo']);
        }
        unset($fetchedRows['rodo']);

        $io->info('Archiwum klientów');
        $this->repo->archive();
    }

    protected function clearTable()
    {
        $this->repo->clearTable();
        $this->addressRepo->clearTable();
        $this->phoneRepo->clearTable();
        $this->emailRepo->clearTable();
        $this->rodoRepo->clearTable();
    }
}
