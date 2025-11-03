<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\FkCechyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fk:cechy',
    description: 'Pobiera cechy dokumentów dla roku. Parametry: nazwa api (opcja), dateFrom [domyślnie bieżący rok]',
)]
class FkCechyCommand extends BaseApiCommand
{
    private $cechy;
    protected $producerName = 'Tema';

    public function __construct(FkCechyRepository $cechy, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->cechy = $cechy;
    }
    
    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL')
            ->addArgument('dateFrom', InputArgument::OPTIONAL, 'Data od [domyślnie bieżący rok]', date('Y-01-01'))
        ;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->cechy->setSource($api);
        $this->cechy->setDateFrom($this->dateFrom);
        $io->text(sprintf("Pobieranie cech dokumentów dla roku %s", date('Y', strtotime($this->dateFrom))));

        $fetchedRows = $this->cechy->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->cechy->removeForCurrentSource();
    }
}