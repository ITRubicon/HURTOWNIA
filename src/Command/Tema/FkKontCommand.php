<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\FkKontRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fk:kont',
    description: 'Pobiera zapisy kont dokumentów dla roku. Parametry: nazwa api (opcja), dateFrom [domyślnie bieżący rok]',
)]
class FkKontCommand extends BaseApiCommand
{
    private $kont;
    protected $producerName = 'Tema';

    public function __construct(FkKontRepository $kont, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->kont = $kont;
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
        $this->kont->setSource($api);
        $this->kont->setDateFrom($this->dateFrom);
        $this->kont->removeForCurrentSource();

        $io->text(sprintf("Pobieranie zapisów kont dla roku %s", date('Y', strtotime($this->dateFrom))));
        $fetchedRows = $this->kont->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));
        unset($fetchedRows);
    }

    protected function clearTable() {}
}
