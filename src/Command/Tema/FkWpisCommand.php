<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\FkWpisRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fk:zapisy',
    description: 'Pobiera zapisy dokumentów dla roku. Parametry: nazwa api (opcja), dateFrom [domyślnie bieżący rok]',
)]
class FkWpisCommand extends BaseApiCommand
{
    private $zapisy;
    protected $producerName = 'Tema';

    public function __construct(FkWpisRepository $zapisy, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->zapisy = $zapisy;
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
        $this->zapisy->setSource($api);
        $this->zapisy->setDateFrom($this->dateFrom);
        $io->text(sprintf("Pobieranie zapisów dokumentów dla roku %s", date('Y', strtotime($this->dateFrom))));

        $fetchedRows = $this->zapisy->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->zapisy->clearTable();
    }
}