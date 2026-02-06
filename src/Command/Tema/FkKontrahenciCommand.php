<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\FkCechyRepository;
use App\Repository\Tema\FkKontrahenciKontaBankoweRepository;
use App\Repository\Tema\FkKontrahenciRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fk:kontrahenci',
    description: 'Pobiera kontrahentów. Parametry: nazwa api (opcja)',
)]
class FkKontrahenciCommand extends BaseApiCommand
{
    private $kontrahenci;
    private $kontaBankowe;
    protected $producerName = 'Tema';

    public function __construct(FkKontrahenciRepository $kontrahenci, FkKontrahenciKontaBankoweRepository $kontaBankowe, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->kontrahenci = $kontrahenci;
        $this->kontaBankowe = $kontaBankowe;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        if (!$api->hasFk()) {
            $io->warning(sprintf("Api %s nie obsługuje FK, pomijam", $api->getName()));
            return;
        }
        
        $this->kontrahenci->setSource($api);
        $this->kontaBankowe->setSource($api);
        $this->kontrahenci->removeForCurrentSource();
        $this->kontaBankowe->removeForCurrentSource();

        $fetchedRows = $this->kontrahenci->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $io->info(sprintf("Pobrano %s kont bankowych", count($fetchedRows['bank_accounts'])));
        $this->kontaBankowe->saveItems($fetchedRows['bank_accounts']);
        unset($fetchedRows);
    }

    protected function clearTable() {}
}
